<?php

namespace TantHammar\LaravelExtras;

class Mutate
{
    public static function maybe_unserialize($value): mixed
    {
        return is_string($value) && self::is_serialized($value) ? unserialize($value) : $value;
    }

    public static function is_serialized(string $value): bool
    {
        return $value === 'b:0;' || @unserialize($value) !== false;
    }

}
