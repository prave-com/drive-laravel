<?php

namespace App\Http\Requests;

use App\Enums\StorageRequestStatus;
use App\Models\Storage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStorageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $storageRequest = $this->route('storageRequest');

        return $this->user()->can('update', $storageRequest);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::enum(StorageRequestStatus::class)
                    ->only([StorageRequestStatus::APPROVED, StorageRequestStatus::REJECTED]),
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $storageRequest = $this->route('storageRequest');

            if ($this->enum('status', StorageRequestStatus::class) === StorageRequestStatus::APPROVED) {
                $totalStorage = (int) env('TOTAL_STORAGE');
                $usedQuota = Storage::all()->sum('total_quota');
                $remainingQuota = $totalStorage - $usedQuota;

                if ($storageRequest->request_quota > $remainingQuota) {
                    $validator->errors()->add('status', trans('validation.quota_exceeded', ['value' => $remainingQuota]));
                }
            }
        });
    }
}
