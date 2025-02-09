<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidFileName implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $fileName = $value->getClientOriginalName();

        if (! preg_match('/^[^\/:*?"<>|\\\\]+$/', $fileName) || ! preg_match('/^[\x20-\x7E]+$/', $fileName)) {
            $fail('validation.file_name_invalid_format')->translate([
                'name' => $fileName,
            ]);
        }
    }
}
