<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGroupItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo_id' => ['sometimes', 'integer', 'exists:photos,id'],
            'photo_ids' => ['sometimes', 'array', 'min:1'],
            'photo_ids.*' => ['integer', 'exists:photos,id'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'photo_ids.required' => 'Pilih minimal satu media.',
            'photo_ids.min' => 'Pilih minimal satu media.',
            'photo_id.exists' => 'Media yang dipilih tidak valid.',
        ];
    }
}
