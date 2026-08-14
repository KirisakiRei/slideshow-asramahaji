<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:jpeg,jpg,png,gif,webp,mp4,mkv,mov', 'max:204800'],
            'title' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File wajib diunggah.',
            'file.file' => 'File yang diunggah harus berupa file.',
            'file.mimes' => 'File harus berformat: jpeg, jpg, png, gif, webp, mp4, mkv, atau mov.',
            'file.max' => 'Ukuran file tidak boleh melebihi 200MB.',
        ];
    }
}
