<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGuideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', Rule::unique('guides', 'slug')->ignore($this->route('guide'))],
            'bio' => ['nullable', 'string'],
            'languages' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:80'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', 'boolean'],
        ];
    }
}
