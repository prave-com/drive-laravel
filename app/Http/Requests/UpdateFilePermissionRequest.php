<?php

namespace App\Http\Requests;

use App\Enums\PermissionType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateFilePermissionRequest extends FormRequest
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
            'permission_type' => [
                'nullable',
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
            ->withErrors($validator, 'grant_access_everyone_file_'.$file->id)
            ->withInput();

        throw new ValidationException($validator, $response);
    }
}
