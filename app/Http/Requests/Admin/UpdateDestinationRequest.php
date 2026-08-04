<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'exists:destinations,id'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', \Illuminate\Validation\Rule::unique('destinations', 'slug')->ignore($this->route('destination'))],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', 'boolean'],
        ];
    }
}
