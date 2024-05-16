<?php

namespace App\Http\Requests;

use Framework\Http\FormRequest;

class ChangePasswordUserRequest extends FormRequest
{
    /**
     * Provide rules to standardize the validation for a request based on this form request.
     *
     * @return array The validation rules for the request.
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string',
            'old_password' => 'required|string',
            'new_password' => 'required|string'
        ];
    }
}