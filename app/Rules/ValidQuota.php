<?php

namespace App\Rules;

use App\Models\Storage;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidQuota implements ValidationRule
{
    protected $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $totalSize = 0;

        foreach ($value as $file) {
            $totalSize += $file->getSize();
        }

        $usedQuota = $this->storage->used_quota;
        $totalQuota = $this->storage->total_quota;

        if ($usedQuota + $totalSize > $totalQuota) {
            $fail('validation.uploaded_files_exceeds_quota')->translate();
        }
    }
}
