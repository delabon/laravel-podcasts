<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PodcastName implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $result = preg_match('/^[a-z\-0-9\s]+$/ui', $value);

        if (!$result) {
            $fail('The :attribute must contain only alpha, num, hyphens and whitespaces.');
        }
    }
}
