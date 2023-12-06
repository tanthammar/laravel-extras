<?php

namespace TantHammar\LaravelExtras;

use Illuminate\Support\Str;

class SKU
{
    /**
     * @src https://github.com/binary-cats/laravel-sku/blob/master/src/Concerns/SkuMacro.php
     *
     * @throws \Exception
     */
    public static function from(string $source, null | string $separator = null): string
    {
        $signature = str_shuffle(str_repeat(str_pad('0123456789', 8, random_int(0, 9) . random_int(0, 9), STR_PAD_LEFT), 2));
        // Sanitize the signature
        $signature = substr($signature, 0, 8);

        // Cleanup $source and use the three first chars, Implode with random and Uppercase it
        return Str::upper(implode(
            $separator ?: '-',
            [
                Str::of($source)->studly()->limit(3, '')->toString(),
                $signature,
            ]
        ));
    }
}
