<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SaudiNationalID implements ValidationRule
{
    /**
     * Validate a Saudi National ID or Iqama Number.
     *
     * Valid IDs must be:
     * - Numeric
     * - Exactly 10 digits in length
     * - Start with either 1 (citizen) or 2 (resident)
     *
     * @param  string  $attribute  The attribute name being validated
     * @param  mixed  $value  The value being validated
     * @param  Closure(string, string=): PotentiallyTranslatedString  $fail  Closure to add validation errors
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (10 !== str($value)->length() || ! str($value)->startsWith(['1', '2'])) {
            $fail(__('The national ID / Iqama Number must be valid.'));
        }
    }
}
