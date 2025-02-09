<?php

namespace App\Http\Requests;

use App\Models\Folder;
use Closure;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateFolderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $folder = $this->route('folder');

        return $this->user()->can('update', $folder);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'ascii',
                'max:255',
                'regex:/^[^\/:*?"<>|\\\\]+$/',
                Rule::unique(Folder::class)
                    ->where(function ($query) {
                        $folder = $this->route('folder');

                        return $query->where('folder_id', $folder->parent->id);
                    })
                    ->ignore($this->route('folder')->id),
                function (string $attribute, mixed $value, Closure $fail) {
                    $folder = $this->route('folder');

                    if ($folder->parent->files()->withTrashed()->where('name', $value)->exists()) {
                        $fail('validation.unique_folder_name')->translate([
                            'name' => $value,
                        ]);
                    }
                },
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
        $folder = $this->route('folder');
        $response = back()
            ->withErrors($validator, 'rename_folder_'.$folder->id)
            ->withInput();

        throw new ValidationException($validator, $response);
    }
}
