<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookmarkFolderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $folder = $this->route('folder');

        return $this->user()->can('view', $folder);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'is_starred' => [
                'required',
                'boolean',
            ],
        ];
    }
}
