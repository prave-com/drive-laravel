<?php

namespace App\Http\Requests;

use App\Enums\PermissionType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateUserFolderAccessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userFolderAccess = $this->route('userFolderAccess');

        return $this->user()->can('grantAccess', $userFolderAccess->folder);
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
        $userFolderAccess = $this->route('userFolderAccess');
        $response = back()
            ->withErrors($validator, 'update_access_user_folder_'.$userFolderAccess->id)
            ->withInput();

        throw new ValidationException($validator, $response);
    }
}
