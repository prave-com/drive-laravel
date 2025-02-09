<?php

namespace App\Http\Requests;

use App\Models\File;
use Closure;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $file = $this->route('file');

        return $this->user()->can('update', $file);
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
                Rule::unique(File::class)
                    ->where(function ($query) {
                        $file = $this->route('file');

                        return $query->where('folder_id', $file->parent->id);
                    })
                    ->ignore($this->route('file')->id),
                function (string $attribute, mixed $value, Closure $fail) {
                    $file = $this->route('file');

                    if ($file->parent->children()->withTrashed()->where('name', $value)->exists()) {
                        $fail('validation.unique_filename')->translate([
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
        $file = $this->route('file');
        $response = back()
            ->withErrors($validator, 'rename_file_'.$file->id)
            ->withInput();

        throw new ValidationException($validator, $response);
    }
}
