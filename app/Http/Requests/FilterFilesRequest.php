<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterFilesRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna berwenang untuk membuat permintaan ini.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Mendapatkan aturan validasi untuk permintaan ini.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'filter' => [
                'nullable',
                Rule::in(['latest', 'shared']),
            ],
        ];
    }
}
