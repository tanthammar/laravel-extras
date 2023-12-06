<?php

namespace TantHammar\LaravelExtras;

/**
 * Delete all characters except numbers in a string.
 * Intended use is to save clean phone numbers in db.
 */
class CleanNumber
{
    public static function make(string $number = ''): string | array | null
    {
        if ($number && $number !== '') {
            return preg_replace('/[^0-9]/', '', $number);
        }

        return $number;
    }
}
