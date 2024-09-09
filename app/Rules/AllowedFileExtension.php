<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowedFileExtension implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            strtolower($value->getClientOriginalExtension()) != 'jpg' &&
            strtolower($value->getClientOriginalExtension()) != 'jpeg' &&
            strtolower($value->getClientOriginalExtension()) != 'png'
        ) {
            $fail('Hanya menerima file png, jpg dan jpeg');
        }
    }
}
