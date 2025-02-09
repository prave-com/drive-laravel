<?php

namespace App\Http\Requests;

use App\Enums\PermissionType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateUserFileAccessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userFileAccess = $this->route('userFileAccess');

        return $this->user()->can('grantAccess', $userFileAccess->file);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
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
        $userFileAccess = $this->route('userFileAccess');
        $response = back()
            ->withErrors($validator, 'update_access_user_file_'.$userFileAccess->id)
            ->withInput();

        throw new ValidationException($validator, $response);
    }
}
