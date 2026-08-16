<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGalleryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gallery_category_id' => ['nullable', 'exists:gallery_categories,id'],
            'tour_id' => ['nullable', 'exists:tours,id'],
            'media_type' => ['required', 'in:image,video'],
            'file' => ['nullable', 'image', 'max:4096'],
            'video_url' => ['required_if:media_type,video', 'nullable', 'url'],
            'caption' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
