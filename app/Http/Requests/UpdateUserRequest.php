<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => [
                'required',
                'string',
                'lowercase',
                'email:rfc,dns',
                'max:255',
                Rule::unique(User::class)->ignore($this->route('user')->id),
            ],
            'name' => ['required', 'string', 'max:32'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'role' => ['required', new Rules\Enum(UserRole::class)],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
