<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePhotoRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'regex:/\S/'],
            'is_active' => ['sometimes', 'boolean'],
            'focus_x' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'focus_y' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul foto wajib diisi.',
            'title.max' => 'Judul foto tidak boleh lebih dari 255 karakter.',
            'title.regex' => 'Judul foto tidak boleh hanya berisi spasi.',
        ];
    }
}
