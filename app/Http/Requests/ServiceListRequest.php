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
            'service_title' => 'required|string|max:255', // Add max length validation
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'service_title.required' => 'The service title is required.',
            'service_title.string' => 'The service title must be a string.',
            'service_title.max' => 'The service title cannot exceed 255 characters.',
        ];
    }
}
