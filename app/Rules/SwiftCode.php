<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SwiftCode implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $swift = mb_strtoupper(trim((string) $value));

        if ( in_array(preg_match('/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/', $swift), [0, false], true)) {
            $fail(__('The :attribute must be a valid SWIFT/BIC code.'));
        }
    }
}
