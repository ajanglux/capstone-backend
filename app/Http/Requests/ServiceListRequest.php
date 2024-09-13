<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'service_title.required' => 'The service title is required.',
            'service_title.string' => 'The service title must be a string.',
            'service_title.max' => 'The service title cannot exceed 255 characters.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
            'image.max' => 'The image may not be greater than 2MB.',
        ];
    }
}