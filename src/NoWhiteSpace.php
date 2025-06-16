<?php

namespace TantHammar\LaravelExtras;

/**
 * Trim all whitespace (including tabs and line ends)
 */
class NoWhiteSpace
{
    public static function make(?string $string = ''): array | string | null
    {
        if ($string) {
            return preg_replace('/\s+/', '', $string);
        }

        return '';
    }
}
