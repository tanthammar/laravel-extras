<?php

namespace TantHammar\LaravelExtras;

/**
 * OCR/Luhn Number generation function
 * @see https://gist.github.com/tanthammar/31e5cce3b5afd3b85aa7881cfddc23b7.
 * */
class OCR3
{
    /**
     * OCR (Luhn) Number generation function.
     * Generates OCR3 valid numbers "OCR 3 = hård kontrollnivå & referensnummer med checksiffra och variabel längdkontroll".
     *
     * @param string $base_number
     *   The base number that you wish to use for the OCR nr. Can be any number,
     *   but usually consists of client ID combined with invoice ID or similar.
     *
     * @param bool $length
     *   Use length if you want the OCR number to add a length
     *   number as the second to last digit, before the control digit.
     *   The length digit represents the length of the whole OCR, including the
     *   control digit. If the length is > 9 the second digit is used.
     *
     * @return string
     *   the complete and ready OCR number.
     *
     * Previous name = BookonsLuhn
     */
    public static function make(string $base_number, bool $length = true): string
    {

        // Add the length number
        if ($length) {
            $base_number .= substr(strlen($base_number) + 2, -1);
        }

        // Convert the number into an array
        $array_number = str_split($base_number);

        // Reverse the array for easier handling
        $reversed = array_reverse($array_number);

        // Double every other digit
        $doubled = self::double_every_other($reversed);

        // Calculate the sum of all the digits
        $sum = self::sum_of_digits($doubled);

        // Get the diff between the sum and the nearest two digit whole number
        $control_digit = abs((ceil($sum / 10) * 10) - $sum);

        return $base_number.$control_digit;
    }

    /**
     * Helper function that doubles every other digit in the array.
     */
    public static function double_every_other(mixed $reversed): mixed
    {
        // Loop through the reversed base number array and multiply each number by
        // its proper weight.
        foreach ($reversed as $key => $value) {
            if ($key % 2 === 0) {
                $reversed[$key] = $value * 2;
            }
        }

        return $reversed;
    }

    /**
     * Helper function that calculates the sum of all digits.
     * See the Wikipedia article on Luhn's algorithm for more info.
     */
    public static function sum_of_digits(mixed $doubled): int
    {
        $sum = 0;
        // Loop through the doubled base number array and recalculates its value based
        // on the sum of digits.
        foreach ($doubled as $key => $value) {
            $plus = $value > 9 ? 1 : 0;
            $doubled[$key] = $value % 10 + $plus;
            $sum += $doubled[$key];
        }

        return $sum;
    }

}
