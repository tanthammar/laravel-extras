<?php

namespace TantHammar\LaravelExtras;

/**
 * Pretty print json in html or emails<br>
 * Equivalent Blade directive registered in this pkg serviceprovider<br>
 *
 * @see \TantHammar\LaravelExtras\LaravelExtrasServiceProvider
 */
class PrettyPrint
{
    public static function make(mixed $content): string
    {
        return '<pre>'.print_r($content, true).'</pre>';
    }
}
