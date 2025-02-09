<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['prohibited'],
            'name' => ['required', 'string', 'max:32'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,png,jpeg,gif,svg', 'extensions:jpg,png,jpeg,gif,svg', 'max:2048'],
        ];
    }
}
