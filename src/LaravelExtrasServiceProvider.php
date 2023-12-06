<?php

namespace TantHammar\LaravelExtras;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
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
        $this->registerMacros();
        $this->registerBuilderMacros();
        $this->registerBladeDirectives();
    }

    protected function registerBuilderMacros(): void
    {
        /** Case-insensitive, User::whereStartsWith('email', 'tin')->get() will return users where column 'email' starts with 'tin' */
        Builder::macro('whereStartsWith', function (string $attribute, ?string $searchTerm) {
            if ($searchTerm) {
                $searchTerm = strtolower($searchTerm);
                $this->where($attribute, 'LIKE', "{$searchTerm}%");
            }

            return $this;
        });

        Builder::macro('orWhereStartsWith', function (string $attribute, ?string $searchTerm) {
            if ($searchTerm) {
                $searchTerm = strtolower($searchTerm);
                $this->orWhere($attribute, 'LIKE', "{$searchTerm}%");
            }

            return $this;
        });

        /** Case-insensitive, User::whereEndsWith('email', 'gmail.com')->get() will return users where column 'email' ends with 'gmail.com' */
        Builder::macro('whereEndsWith', function (string $attribute, ?string $searchTerm) {
            if ($searchTerm) {
                $searchTerm = strtolower($searchTerm);
                $this->where($attribute, 'LIKE', "%{$searchTerm}");
            }

            return $this;
        });

        Builder::macro('orWhereEndsWith', function (string $attribute, ?string $searchTerm) {
            if ($searchTerm) {
                $searchTerm = strtolower($searchTerm);
                $this->orWhere($attribute, 'LIKE', "%{$searchTerm}");
            }

            return $this;
        });

        /** Case-insensitive, User::whereLike(['name', 'email'], 'tina hammar')->get() will return users where BOTH 'name' and 'email' contains 'tina hammar' */
        Builder::macro('whereLike', function (string | array $attributes, ?string $searchTerm) {
            if ($searchTerm) {
                $searchTerm = strtolower($searchTerm);
                foreach (\Arr::wrap($attributes) as $attribute) {
                    $this->where($attribute, 'LIKE', "%{$searchTerm}%");
                }
            }

            return $this;
        });

        /** User::orWhereLike(['name', 'email'], 'tina hammar')->get() will return users where 'name' OR 'email' contains 'tina hammar' */
        Builder::macro('orWhereLike', function (string | array $attributes, ?string $searchTerm) {
            if ($searchTerm) {
                $searchTerm = strtolower($searchTerm);
                $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                    foreach (\Arr::wrap($attributes) as $attribute) {
                        $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                    }
                });
            }

            return $this;
        });

        /** Case-insensitive, User::whereContains(['name', 'email'], 'tina hammar')->get() will return users where BOTH 'name' and 'email' contains 'tina' AND 'hammar' */
        Builder::macro('whereContains', function (string | array $attributes, ?string $searchTerm) {
            if ($searchTerm) {
                $searchTerm = strtolower(str_replace(' ', '%', $searchTerm));
                foreach (\Arr::wrap($attributes) as $attribute) {
                    $this->where($attribute, 'LIKE', "%{$searchTerm}%");
                }
            }

            return $this;
        });

        /** Case-insensitive, User::orWhereLike(['name', 'email'], 'tina hammar')->get() will return users where 'name' OR 'email' contains 'tina' AND 'hammar' */
        Builder::macro('orWhereContains', function (string | array $attributes, ?string $searchTerm) {
            if ($searchTerm) {
                $searchTerm = strtolower(str_replace(' ', '%', $searchTerm));
                $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                    foreach (\Arr::wrap($attributes) as $attribute) {
                        $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                    }
                });
            }

            return $this;
        });

        /** Case-insensitive, Event::whereTranslatableLike('name', 'Foo bar')->get() will return events where 'name' contains 'Foo Bar' or 'foo bar' */
        Builder::macro('whereTranslatableLike', function (string $column, ?string $searchTerm) {
            if ($searchTerm) {
                $searchTerm = strtolower($searchTerm);
                $this->whereRaw('lower(' . $column . '->"$.' . app()->getLocale() . '") like ?', '%' . $searchTerm . '%');
            }

            return $this;
        });

        /** Case-insensitive, Event::orWhereTranslatableLike('name', 'Foo bar')->get() will return events where 'name' contains 'Foo Bar' or 'foo bar' */
        Builder::macro('orWhereTranslatableLike', function (string $column, ?string $searchTerm) {
            if ($searchTerm) {
                $searchTerm = strtolower($searchTerm);
                $this->orWhereRaw('lower(' . $column . '->"$.' . app()->getLocale() . '") like ?', '%' . $searchTerm . '%');
            }

            return $this;
        });

        /** Case-insensitive and skipped words, Event::whereTranslatableLike('name', 'Foo bar')->get() will return events where 'name' contains 'Foo' or 'Bar' or 'foo baz bar' */
        Builder::macro('whereTranslatableContains', function (string $column, ?string $searchTerm) {
            if ($searchTerm) {
                $searchTerm = strtolower(str_replace(' ', '%', $searchTerm));
                $this->whereRaw('lower(' . $column . '->"$.' . app()->getLocale() . '") like ?', '%' . $searchTerm . '%');
            }

            return $this;
        });

        /** Case-insensitive and skipped words, Event::orWhereTranslatableLike('name', 'Foo bar')->get() will return events where 'name' contains 'Foo' or 'Bar' or 'foo baz bar' */
        Builder::macro('orWhereTranslatableContains', function (string $column, ?string $searchTerm) {
            if ($searchTerm) {
                $searchTerm = strtolower(str_replace(' ', '%', $searchTerm));
                $this->orWhereRaw('lower(' . $column . '->"$.' . app()->getLocale() . '") like ?', '%' . $searchTerm . '%');
            }

            return $this;
        });

        /** @see https://freek.dev/1182-searching-models-using-a-where-like-query-in-laravel */
        /** Post::whereRelationsLike(['name', 'text', 'author.name', 'tags.name'], $searchTerm)->get(); */
        Builder::macro('whereRelationsLike', function ($attributes, ?string $searchTerm) {
            if ($searchTerm) {
                $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                    foreach (\Arr::wrap($attributes) as $attribute) {
                        $query->when(
                            str_contains($attribute, '.'),
                            function (Builder $query) use ($attribute, $searchTerm) {
                                [$relationName, $relationAttribute] = explode('.', $attribute);

                                $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
                                    $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                                });
                            },
                            function (Builder $query) use ($attribute, $searchTerm) {
                                $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                            }
                        );
                    }
                });
            }

            return $this;
        });

        /** Order query by Spatie translatable column */
        Builder::macro('orderByTranslation', function ($field, $order = 'asc', $locale = null) {
            if (
                in_array(\Spatie\Translatable\HasTranslations::class, class_uses($this->model), false)
                && in_array($field, $this->model->translatable, false)
            ) {
                $locale = $locale ?? app()->getLocale();
                $field .= '->' . $locale;
            }
            $this->query->orderBy($field, $order);

            return $this;
        });

        /** Overlapping dates query */
        Builder::macro('whereOverlaps', function (
            string $startColumn,
            string $endColumn,
            string | CarbonInterface | \DateTimeInterface $startDateTime,
            string | CarbonInterface | \DateTimeInterface $endDateTime,
            $tz = null
        ) {
            $tz = $tz ?? config('app.timezone');
            $startDateTime = is_a($startDateTime, 'DateTimeInterface') ? $startDateTime : \Date::parse($startDateTime, $tz);
            $endDateTime = is_a($endDateTime, 'DateTimeInterface') ? $endDateTime : \Date::parse($endDateTime, $tz);

            $this->where(
                fn ($query) => $query
                    ->orWhereBetween($startColumn, [$startDateTime, $endDateTime])
                    ->orWhereBetween($endColumn, [$startDateTime, $endDateTime])
                    ->orWhere(
                        fn ($query) => $query
                            ->where($startColumn, '<=', $startDateTime)
                            ->where($endColumn, '>=', $endDateTime)
                    )
            );

            return $this;
        });

        Builder::macro('existsById', function (int $id): bool {
            return $this->where('id', $id)->exists();
        });

        Builder::macro('existsByUuid', function (string $uuid): bool {
            return $this->where('uuid', $uuid)->exists();
        });
    }

    protected function registerMacros(): void
    {
        /**
         * Swap the order/sorting of an array, like swqp the 3rd row with the 1st. the 1st will become the 3rd.
         *
         * @see https://ashallendesign.co.uk/blog/how-to-swap-items-in-an-array-using-laravel-macros
         * Examples: Arr::swap($array, 0, 2); or Arr::swap($array, 'foo', 'bar');
         */
        Arr::macro('swap', static function (array $array, $keyOne, $keyTwo): array {
            if (! Arr::isAssoc($array)) {
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

    protected function registerBladeDirectives(): void
    {
        Blade::directive('prettyPrint', function (mixed $expression) {
            return "<?php echo '<pre>' . print_r($expression, true) . '</pre>'; ?>";
        });
    }
}
