<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhotoGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
            'slide_duration' => ['required', 'integer', 'min:1', 'max:60'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'transition_type' => ['sometimes', 'string', 'in:fade,slide-left,slide-right,slide-up,slide-down,zoom-in,zoom-out'],
            'fill_mode' => ['sometimes', 'string', 'in:cover,contain'],
        ];
    }
}
