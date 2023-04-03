<?php

namespace TantHammar\LaravelExtras;

class ArrHelper
{
    public static function filledOnly(array $array, int $mode = 0): array
    {
        return array_filter($array, fn ($value) => !blank($value), $mode);
    }

}
