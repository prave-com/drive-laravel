<?php

namespace App\Rules;

use App\Models\Folder;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueFileName implements ValidationRule
{
    protected $folder;

    public function __construct(Folder $folder)
    {
        $this->folder = $folder;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $fileName = $value->getClientOriginalName();

        if (
            $this->folder->children()->withTrashed()->where('name', $fileName)->exists() ||
            $this->folder->files()->withTrashed()->where('name', $fileName)->exists()
        ) {
            $fail('validation.unique_filename')->translate([
                'name' => $fileName,
            ]);
        }
    }
}
