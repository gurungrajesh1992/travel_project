<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Destination;
use App\Models\DifficultyLevel;
use App\Models\FaqCategory;
use App\Models\Guide;
use App\Models\Tour;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = collect([
            'Nepal' => 'Home of the Himalayas — Everest, Annapurna, and centuries of Sherpa culture.',
            'India' => 'From the Himalayan foothills to royal Rajasthan.',
            'Bhutan' => 'The last Himalayan kingdom, high-value low-impact tourism.',
            'Tibet' => 'The roof of the world.',
        ])->map(fn ($description, $name) => Destination::firstOrCreate(
            ['slug' => str($name)->slug()],
            ['name' => $name, 'description' => $description, 'status' => true]
        ));

        $categories = collect(['Trekking', 'Expedition', 'Peak Climbing', 'Tour', 'Special Activities'])
            ->mapWithKeys(fn ($name) => [$name => Category::firstOrCreate(
                ['slug' => str($name)->slug()],
                ['name' => $name, 'status' => true]
            )]);

        // A couple of sub-categories under Trekking, per the requirement doc's example.
        collect(['Everest Trek', 'Annapurna Trek'])->each(fn ($name) => Category::firstOrCreate(
            ['slug' => str($name)->slug()],
            ['name' => $name, 'parent_id' => $categories['Trekking']->id, 'status' => true]
        ));

        $difficulties = collect(['Easy', 'Moderate', 'Challenging', 'Strenuous'])
            ->mapWithKeys(fn ($name, $i) => [$name => DifficultyLevel::firstOrCreate(
                ['name' => $name],
                ['sort_order' => $i]
            )]);

        $guide = Guide::firstOrCreate(
            ['slug' => 'pemba-sherpa'],
            [
                'name' => 'Pemba Sherpa',
                'bio' => 'Licensed trekking guide with 12 years of experience in the Everest and Annapurna regions.',
                'languages' => 'English, Nepali, Hindi',
                'experience_years' => 12,
                'status' => true,
            ]
        );

        $tour = Tour::firstOrCreate(
            ['slug' => 'everest-base-camp-trek'],
            [
                'primary_destination_id' => $destinations['Nepal']->id,
                'primary_category_id' => $categories['Trekking']->id,
                'difficulty_id' => $difficulties['Challenging']->id,
                'guide_id' => $guide->id,
                'title' => 'Everest Base Camp Trek',
                'short_description' => 'The classic trek to the foot of the world\'s highest mountain.',
                'full_description' => 'Trek through Sherpa villages, monasteries, and high alpine terrain to reach Everest Base Camp at 5,364m, with a sunrise climb of Kala Patthar for the best mountain views in the region.',
                'duration_days' => 14,
                'duration_nights' => 13,
                'max_altitude' => '5,364m',
                'group_size_min' => 1,
                'group_size_max' => 12,
                'best_season' => 'Mar-May, Sep-Nov',
                'base_price' => 1450,
                'currency' => 'USD',
                'total_seats' => 12,
                'is_featured' => true,
                'booking_mode' => 'both',
                'status' => 'published',
            ]
        );

        if ($tour->wasRecentlyCreated) {
            $tour->destinations()->syncWithoutDetaching([$destinations['Nepal']->id => ['is_primary' => true]]);
            $tour->categories()->syncWithoutDetaching([$categories['Trekking']->id => ['is_primary' => true]]);

            $tour->highlights()->createMany([
                ['highlight_text' => 'Fly through Lukla, one of the world\'s most dramatic mountain airstrips', 'sort_order' => 1],
                ['highlight_text' => 'Reach Everest Base Camp at 5,364m', 'sort_order' => 2],
                ['highlight_text' => 'Sunrise views from Kala Patthar (5,545m)', 'sort_order' => 3],
                ['highlight_text' => 'Explore Sherpa culture in Namche Bazaar and Tengboche Monastery', 'sort_order' => 4],
            ]);

            $days = [
                ['title' => 'Fly to Lukla, trek to Phakding', 'accommodation' => 'Teahouse', 'meals' => 'B,L,D', 'walking_hours' => '3-4 hrs'],
                ['title' => 'Trek to Namche Bazaar', 'accommodation' => 'Teahouse', 'meals' => 'B,L,D', 'walking_hours' => '5-6 hrs'],
                ['title' => 'Acclimatization day in Namche Bazaar', 'accommodation' => 'Teahouse', 'meals' => 'B,L,D', 'walking_hours' => '3 hrs'],
                ['title' => 'Trek to Tengboche', 'accommodation' => 'Teahouse', 'meals' => 'B,L,D', 'walking_hours' => '5 hrs'],
                ['title' => 'Trek to Dingboche', 'accommodation' => 'Teahouse', 'meals' => 'B,L,D', 'walking_hours' => '5-6 hrs'],
            ];

            foreach ($days as $i => $day) {
                $tour->itineraries()->create([
                    'destination_id' => $destinations['Nepal']->id,
                    'day_number' => $i + 1,
                    ...$day,
                ]);
            }

            $tour->costDetails()->createMany([
                ['type' => 'include', 'detail_text' => 'Airport transfers', 'sort_order' => 1],
                ['type' => 'include', 'detail_text' => 'Lukla flights', 'sort_order' => 2],
                ['type' => 'include', 'detail_text' => 'Teahouse accommodation', 'sort_order' => 3],
                ['type' => 'include', 'detail_text' => 'Licensed guide and porter', 'sort_order' => 4],
                ['type' => 'exclude', 'detail_text' => 'International flights', 'sort_order' => 1],
                ['type' => 'exclude', 'detail_text' => 'Nepal entry visa', 'sort_order' => 2],
                ['type' => 'exclude', 'detail_text' => 'Personal trekking gear', 'sort_order' => 3],
                ['type' => 'exclude', 'detail_text' => 'Travel insurance', 'sort_order' => 4],
            ]);

            $tour->faqs()->create([
                'question' => 'Do I need travel insurance?',
                'answer' => 'Yes — insurance covering high-altitude trekking and helicopter evacuation up to 6,000m is required.',
                'sort_order' => 1,
            ]);

            $tour->departures()->createMany([
                ['departure_date' => now()->addMonths(2)->startOfMonth()->addDays(4), 'return_date' => now()->addMonths(2)->startOfMonth()->addDays(17), 'available_seats' => 12, 'booked_seats' => 3, 'status' => 'open'],
                ['departure_date' => now()->addMonths(3)->startOfMonth()->addDays(4), 'return_date' => now()->addMonths(3)->startOfMonth()->addDays(17), 'available_seats' => 12, 'booked_seats' => 0, 'status' => 'open'],
            ]);

            $tour->seasonalPricing()->create([
                'season_name' => 'Peak Season (Autumn)',
                'start_date' => now()->month(9)->startOfMonth(),
                'end_date' => now()->month(11)->endOfMonth(),
                'price' => 1550,
            ]);

            $tour->pricingTiers()->createMany([
                ['tier_type' => 'group', 'min_pax' => 1, 'max_pax' => 2, 'price_per_person' => 1600],
                ['tier_type' => 'group', 'min_pax' => 3, 'max_pax' => 5, 'price_per_person' => 1450],
                ['tier_type' => 'group', 'min_pax' => 6, 'max_pax' => null, 'price_per_person' => 1300],
                ['tier_type' => 'child', 'min_pax' => null, 'max_pax' => null, 'price_per_person' => 900],
                ['tier_type' => 'private', 'min_pax' => 1, 'max_pax' => 1, 'price_per_person' => 2200],
            ]);
        }

        $faqCategories = collect(['Booking', 'Payment', 'Visa & Permits', 'Trekking Gear'])->mapWithKeys(
            fn ($name, $i) => [$name => FaqCategory::firstOrCreate(['slug' => str($name)->slug()], ['name' => $name, 'sort_order' => $i])]
        );

        $faqCategories['Booking']->faqs()->firstOrCreate(
            ['question' => 'How do I book a tour?'],
            ['answer' => 'Choose a departure date on the tour page and submit the booking form — no account required, though creating one lets you track your booking online.', 'sort_order' => 1, 'status' => true]
        );
        $faqCategories['Payment']->faqs()->firstOrCreate(
            ['question' => 'What payment methods are accepted?'],
            ['answer' => 'Bank transfer and major payment apps. After booking, upload your payment receipt and our team will verify it.', 'sort_order' => 1, 'status' => true]
        );
        $faqCategories['Visa & Permits']->faqs()->firstOrCreate(
            ['question' => 'Do I need a visa for Nepal?'],
            ['answer' => 'Most nationalities can get a visa on arrival at Kathmandu airport. Trekking permits (TIMS, national park fees) are arranged by us.', 'sort_order' => 1, 'status' => true]
        );
        $faqCategories['Trekking Gear']->faqs()->firstOrCreate(
            ['question' => 'What gear should I bring?'],
            ['answer' => 'A detailed packing list is sent after booking. Down jackets and sleeping bags are available for rent in Kathmandu.', 'sort_order' => 1, 'status' => true]
        );

        \App\Models\Page::firstOrCreate(['slug' => 'about-us'], [
            'title' => 'About Us',
            'content' => '<p>We are a Nepal-based tour operator specializing in trekking, expeditions, and cultural tours across Nepal, India, Bhutan, and Tibet.</p>',
            'status' => 'published',
        ]);

        \App\Models\Page::firstOrCreate(['slug' => 'contact-us'], [
            'title' => 'Contact Us',
            'content' => '<p>Reach us at hello@travel-tour.test or use the form below and we will get back to you shortly.</p>',
            'status' => 'published',
        ]);

        $admin = \App\Models\User::where('email', 'admin@travel-tour.test')->first();
        $blogCategory = \App\Models\BlogCategory::firstOrCreate(['slug' => 'trekking-tips'], ['name' => 'Trekking Tips']);

        \App\Models\BlogPost::firstOrCreate(['slug' => 'best-time-to-trek-everest-base-camp'], [
            'author_id' => $admin->id,
            'blog_category_id' => $blogCategory->id,
            'title' => 'Best Time to Trek Everest Base Camp',
            'excerpt' => 'Spring and autumn offer the clearest mountain views and the most stable weather for the EBC trek.',
            'content' => '<p>The two trekking seasons — March to May and September to November — offer the best combination of clear skies, stable trails, and open teahouses.</p>',
            'status' => 'published',
            'published_at' => now()->subDays(10),
        ]);

        $galleryCategory = \App\Models\GalleryCategory::firstOrCreate(['slug' => 'mountains'], ['name' => 'Mountains', 'sort_order' => 1, 'status' => true]);
        \App\Models\GalleryItem::firstOrCreate(
            ['tour_id' => $tour->id, 'caption' => 'Everest Base Camp'],
            ['gallery_category_id' => $galleryCategory->id, 'media_type' => 'image', 'is_featured' => true, 'sort_order' => 1, 'created_at' => now()]
        );
    }
}
