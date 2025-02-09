<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStorageRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'request_quota' => [
                'required_without:custom_quota',
                'integer',
                'in:5,10',
                'prohibits:custom_quota',
            ],
            'custom_quota' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
                'prohibits:request_quota',
            ],
            'reason' => [
                'required',
                'string',
                'max:200',
            ],
        ];
    }
}
