<?php

namespace TantHammar\LaravelExtras;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

/**
 * Default = 2 decimals
 */
class MoneyIntegerCast implements CastsAttributes
{
    /* Example
     * protected $casts = [
        'price' => MoneyIntegerCast::class,
        'price_with_digits' => MoneyIntegerCast::class . ':4',
    ];
    */

    /**
     * The amount of digits.
     */
    protected int $digits;

    /**
     * Constructor
     *
     * @param  int  $digits Nr of decimals.
     * @return void
     *
     * @throws InvalidArgumentException Thrown if digits is < 1.
     */
    public function __construct(int $digits = 2)
    {
        if ($digits < 1) {
            throw new InvalidArgumentException('Digits should be a number larger than zero.');
        }

        $this->digits = $digits;
    }

    /**
     * If $value === null it becomes 0.0
     */
    public function get($model, string $key, $value, array $attributes): ?float
    {
        return round($value / (10 ** $this->digits), $this->digits);
    }

    /**
     * Transforms decimal value into integers.
     */
    public function set($model, string $key, $value, array $attributes): int
    {
        return (int) (round($value, $this->digits) * (10 ** $this->digits));
    }
}
