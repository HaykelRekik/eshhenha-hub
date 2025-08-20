<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class VATNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /*
         * VAT Number (Tax Identification Number):
         * Always 15 digits.
         * First digit should be 3.
         * Last digit is a check digit (mod-11 check, but most systems just enforce 15 digits starting with 3).
         */
        if ( ! preg_match('/^3[0-9]{14}$/', $value)) {
            $fail("The VAT Number must be a valid Saudi VAT number.");
        }
    }
}
