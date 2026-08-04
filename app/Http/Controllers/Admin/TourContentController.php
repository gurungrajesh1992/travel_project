<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourCostDetail;
use App\Models\TourFaq;
use App\Models\TourHighlight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Handles the small "repeatable text row" child collections of a Tour:
 * highlights, cost details (include/exclude), and FAQs. Add/remove only —
 * to correct a row, remove it and add it again.
 *
 * The store* actions accept parallel arrays so the admin can add several
 * rows in one submit instead of reloading the page per row; blank rows
 * (left over from an "+ Add row" click the admin didn't fill in) are
 * silently skipped rather than rejected.
 */
class TourContentController extends Controller
{
    public function storeHighlight(Request $request, Tour $tour): RedirectResponse
    {
        $data = $request->validate([
            'highlight_text' => ['required', 'array', 'min:1'],
            'highlight_text.*' => ['nullable', 'string', 'max:255'],
        ]);

        $texts = array_values(array_filter($data['highlight_text'], fn ($text) => trim((string) $text) !== ''));

        $nextOrder = $tour->highlights()->count();
        foreach ($texts as $i => $text) {
            $tour->highlights()->create(['highlight_text' => $text, 'sort_order' => $nextOrder + $i]);
        }

        return back()->with('status', count($texts) > 0 ? count($texts).' highlight(s) added.' : 'Nothing to add.');
    }

    public function destroyHighlight(Tour $tour, TourHighlight $highlight): RedirectResponse
    {
        abort_unless($highlight->tour_id === $tour->id, 404);
        $highlight->delete();

        return back()->with('status', 'Highlight removed.');
    }

    public function storeCostDetail(Request $request, Tour $tour): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:include,exclude'],
            'detail_text' => ['required', 'array', 'min:1'],
            'detail_text.*' => ['nullable', 'string', 'max:255'],
        ]);

        $texts = array_values(array_filter($data['detail_text'], fn ($text) => trim((string) $text) !== ''));

        $nextOrder = $tour->costDetails()->where('type', $data['type'])->count();
        foreach ($texts as $i => $text) {
            $tour->costDetails()->create(['type' => $data['type'], 'detail_text' => $text, 'sort_order' => $nextOrder + $i]);
        }

        return back()->with('status', count($texts) > 0 ? count($texts).' item(s) added.' : 'Nothing to add.');
    }

    public function destroyCostDetail(Tour $tour, TourCostDetail $costDetail): RedirectResponse
    {
        abort_unless($costDetail->tour_id === $tour->id, 404);
        $costDetail->delete();

        return back()->with('status', 'Item removed.');
    }

    public function storeFaq(Request $request, Tour $tour): RedirectResponse
    {
        $data = $request->validate([
            'question' => ['required', 'array', 'min:1'],
            'question.*' => ['nullable', 'string', 'max:255'],
            'answer' => ['nullable', 'array'],
            'answer.*' => ['nullable', 'string'],
        ]);

        $nextOrder = $tour->faqs()->count();
        $added = 0;

        foreach ($data['question'] as $i => $question) {
            $answer = $data['answer'][$i] ?? null;

            if (trim((string) $question) === '' || trim((string) $answer) === '') {
                continue;
            }

            $tour->faqs()->create(['question' => $question, 'answer' => $answer, 'sort_order' => $nextOrder + $added]);
            $added++;
        }

        return back()->with('status', $added > 0 ? "{$added} FAQ(s) added." : 'Nothing to add.');
    }

    public function destroyFaq(Tour $tour, TourFaq $faq): RedirectResponse
    {
        abort_unless($faq->tour_id === $tour->id, 404);
        $faq->delete();

        return back()->with('status', 'FAQ removed.');
    }
}
