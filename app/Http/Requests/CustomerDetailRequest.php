<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'nullable|integer|exists:users,id',
            'description' => 'nullable|string',
            'status' => 'sometimes|string|in:Pending,On-Going,Finished,Ready-for-Pickup,Completed,Cancelled,Incomplete,Responded',
        ];
    }
}
