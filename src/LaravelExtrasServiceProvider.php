<?php

namespace TantHammar\LaravelExtras;

use Illuminate\Support\Facades\Validator;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelExtrasServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name('laravel-rules');
    }

    public function bootingPackage(): void
    {
        /**
         * Swap the order/sorting of an array, like swqp the 3rd row with the 1st. the 1st will become the 3rd.
         * src: https://ashallendesign.co.uk/blog/how-to-swap-items-in-an-array-using-laravel-macros
         * Examples: Arr::swap($array, 0, 2); or Arr::swap($array, 'foo', 'bar');
         */
        \Arr::macro('swap', static function (array $array, $keyOne, $keyTwo): array {
            if (! \Arr::isAssoc($array)) {
                $itemOneTmp = $array[$keyOne];

                $array[$keyOne] = $array[$keyTwo];
                $array[$keyTwo] = $itemOneTmp;

                return $array;
            }

            $updatedArray = [];
            foreach ($array as $key => $value) {
                if ($key === $keyOne) {
                    $updatedArray[$keyTwo] = $array[$keyTwo];
                } elseif ($key === $keyTwo) {
                    $updatedArray[$keyOne] = $array[$keyOne];
                } else {
                    $updatedArray[$key] = $value;
                }
            }

            return $updatedArray;
        });
    }

}
