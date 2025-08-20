<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SaudiIBAN implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Normalize value (remove spaces)
        $iban = mb_strtoupper(str_replace(' ', '', $value));

        // Check format: starts with SA + 22 digits
        if ( in_array(preg_match('/^SA\d{22}$/', $iban), [0, false], true)) {
            $fail(__('The :attribute must be a valid Saudi IBAN.'));

            return;
        }

        // Validate IBAN checksum using MOD-97 algorithm
        if ( ! $this->isValidIban($iban)) {
            $fail(__('The :attribute is not a valid IBAN.'));
        }
    }

    private function isValidIban(string $iban): bool
    {
        // Move first 4 chars to the end
        $rearranged = mb_substr($iban, 4) . mb_substr($iban, 0, 4);

        // Convert letters to numbers (A=10, B=11, ...)
        $numericIban = '';
        foreach (mb_str_split($rearranged) as $char) {
            $numericIban .= is_numeric($char) ? $char : (ord($char) - 55);
        }

        // Use bcmod for big integers
        return '1' === bcmod($numericIban, '97');
    }
}
