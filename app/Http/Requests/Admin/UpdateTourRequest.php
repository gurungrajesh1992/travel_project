<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('map_data') === '') {
            $this->merge(['map_data' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('tours', 'slug')->ignore($this->route('tour'))],
            'short_description' => ['nullable', 'string', 'max:500'],
            'full_description' => ['nullable', 'string'],

            'destinations' => ['required', 'array', 'min:1'],
            'destinations.*' => ['exists:destinations,id'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:categories,id'],
            'difficulty_id' => ['nullable', 'exists:difficulty_levels,id'],
            'guide_id' => ['nullable', 'exists:guides,id'],

            'duration_days' => ['nullable', 'integer', 'min:1'],
            'duration_nights' => ['nullable', 'integer', 'min:0'],
            'max_altitude' => ['nullable', 'string', 'max:50'],
            'best_season' => ['nullable', 'string', 'max:150'],
            'group_size_min' => ['nullable', 'integer', 'min:1'],
            'group_size_max' => ['nullable', 'integer', 'min:1'],

            'base_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'total_seats' => ['nullable', 'integer', 'min:1'],

            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'map_type' => ['nullable', 'in:point,route'],
            'map_data' => ['nullable', 'json'],

            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],

            'is_featured' => ['sometimes', 'boolean'],
            'booking_mode' => ['required', 'in:instant,inquiry,both'],
            'status' => ['required', 'in:draft,published,archived'],
        ];
    }
}
