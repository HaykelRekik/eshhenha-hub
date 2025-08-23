<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CRNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /*
         * CR Number (Commercial Registration Number):
         * Usually 10 digits.
         * Only digits allowed.
         * Sometimes the first digit indicates the region (e.g., "1" = Riyadh).
         */
        if (in_array(preg_match('/^\d{10}$/', (string) $value), [0, false], true)) {
            $fail('The CR Number must be a valid Saudi CR number.');
        }
    }
}
