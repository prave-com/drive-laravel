<?php

namespace App\Http\Requests;

use App\Rules\ValidQuota;
use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $folder = $this->route('folder');

        return $this->user()->can('create', $folder);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files' => [
                'required',
                'array',
                new ValidQuota($this->route('folder')->owner->storage),
            ],
            'files.*' => [
                'required',
                'file',
                'max:16106127360',
            ],
        ];
    }
}
