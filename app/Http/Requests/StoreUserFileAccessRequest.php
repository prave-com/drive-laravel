<?php

namespace App\Http\Requests;

use App\Enums\PermissionType;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreUserFileAccessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $file = $this->route('file');

        return $this->user()->can('grantAccess', $file);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'lowercase',
                'email:rfc,dns',
                'max:255',
                'exists:users',
                function (string $attribute, mixed $value, Closure $fail) {
                    $targetUser = User::where('email', $value)->first();
                    $file = $this->route('file');

                    if ($targetUser && $file->userFileAccesses()->where('user_id', $targetUser->id)->exists()) {
                        $fail('validation.unique_user_file_access')->translate([
                            'name' => $value,
                        ]);
                    }

                    if ($targetUser && $file->owner->id === $targetUser->id) {
                        $fail('validation.cannot_add_file_owner_to_access')->translate();
                    }
                },
            ],
            'permission_type' => [
                'required',
                Rule::enum(PermissionType::class),
            ],
        ];
    }

    /**
     * Returns validations errors.
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $file = $this->route('file');
        $response = back()
            ->withErrors($validator, 'grant_access_user_file_'.$file->id)
            ->withInput();

        throw new ValidationException($validator, $response);
    }
}
