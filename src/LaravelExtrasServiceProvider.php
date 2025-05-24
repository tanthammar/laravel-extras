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
        $package->name('laravel-rules')
            ->hasMigration('create_ocr_numbers_table');
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
                $this->orWhere(function (Builder $query) use ($attributes, $searchTerm) {
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
                $this->orWhere(function (Builder $query) use ($attributes, $searchTerm) {
                    foreach (\Arr::wrap($attributes) as $attribute) {
                        $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                    }
                });
            }

            return $this;
        });

        /**
         * Case-insensitive, Event::whereTranslatableStartsWith('name', 'Foo')->get() will
         * return events where localized 'name' starts with 'Foo' or 'foo'
         */
        Builder::macro('whereTranslatableStartsWith', function (string $column, ?string $searchTerm, ?string $locale = null): Builder
        {
            if ($searchTerm) {
                $locale = $locale ?: app()->getLocale();
                $searchTerm = strtolower($searchTerm) . '%';
                $driver = config('database.default');
                
                match ($driver) {
                    'pgsql' => $this->whereRaw("lower($column->>'{$locale}') like ?", [$searchTerm]),
                    'mysql' => $this->whereRaw("lower($column->\"$.{$locale}\") like ?", [$searchTerm]),
                    default => $this->whereStartsWith($column, $searchTerm)
                };
            }
            return $this;
        });

        /**
         * Case-insensitive, Event::orWhereTranslatableStartsWith('name', 'Foo bar')->get() will
         * return events where localized 'name' starts with 'Foo Bar' or 'foo bar'
         */
        Builder::macro('orWhereTranslatableStartsWith', function (string $column, ?string $searchTerm, ?string $locale = null): Builder
        {
            if ($searchTerm) {
                $locale = $locale ?: app()->getLocale();
                $searchTerm = strtolower($searchTerm) . '%';
                $driver = config('database.default');
                
                match ($driver) {
                    'pgsql' => $this->orWhereRaw("lower($column->>'{$locale}') like ?", [$searchTerm]),
                    'mysql' => $this->orWhereRaw("lower($column->\"$.{$locale}\") like ?", [$searchTerm]),
                    default => $this->orWhereStartsWith($column, $searchTerm)
                };
            }
            return $this;
        });

        /** Case-insensitive, Event::whereTranslatableLike('name', 'Foo bar')->get() will return events where 'name' contains 'Foo Bar' or 'foo bar' */
        Builder::macro('whereTranslatableLike', function (string $column, ?string $searchTerm, ?string $locale = null): Builder
        {
            if ($searchTerm) {
                $locale = $locale ?: app()->getLocale();
                $searchTerm = '%' . strtolower($searchTerm) . '%';
                $driver = config('database.default');
                
                match ($driver) {
                    'pgsql' => $this->whereRaw("lower($column->>'{$locale}') like ?", [$searchTerm]),
                    'mysql' => $this->whereRaw("lower($column->\"$.{$locale}\") like ?", [$searchTerm]),
                    default => $this->whereLike($column, $searchTerm)
                };
            }

            return $this;
        });

        /** Case-insensitive, Event::orWhereTranslatableLike('name', 'Foo bar')->get() will return events where 'name' contains 'Foo Bar' or 'foo bar' */
        Builder::macro('orWhereTranslatableLike', function (string $column, ?string $searchTerm, ?string $locale = null): Builder
        {
            if ($searchTerm) {
                $locale = $locale ?: app()->getLocale();
                $searchTerm = '%' . strtolower($searchTerm) . '%';
                $driver = config('database.default');
                
                match ($driver) {
                    'pgsql' => $this->orWhereRaw("lower($column->>'{$locale}') like ?", [$searchTerm]),
                    'mysql' => $this->orWhereRaw("lower($column->\"$.{$locale}\") like ?", [$searchTerm]),
                    default => $this->orWhereLike($column, $searchTerm)
                };
            }

            return $this;
        });

        /** Case-insensitive and skipped words, Event::whereTranslatableLike('name', 'Foo bar')->get() will return events where 'name' contains 'Foo' or 'Bar' or 'foo baz bar' */
        Builder::macro('whereTranslatableContains', function (string $column, ?string $searchTerm, ?string $locale = null): Builder
        {
            if ($searchTerm) {
                $locale = $locale ?: app()->getLocale();
                $searchTerm = '%' . strtolower(str_replace(' ', '%', $searchTerm)) . '%';
                $driver = config('database.default');
                
                match ($driver) {
                    'pgsql' => $this->whereRaw("lower($column->>'{$locale}') like ?", [$searchTerm]),
                    'mysql' => $this->whereRaw("lower($column->\"$.{$locale}\") like ?", [$searchTerm]),
                    default => $this->whereContains($column, $searchTerm)
                };
            }

            return $this;
        });

        /** Case-insensitive and skipped words, Event::orWhereTranslatableLike('name', 'Foo bar')->get() will return events where 'name' contains 'Foo' or 'Bar' or 'foo baz bar' */
        Builder::macro('orWhereTranslatableContains', function (string $column, ?string $searchTerm, ?string $locale = null): Builder
        {
            if ($searchTerm) {
                $locale = $locale ?: app()->getLocale();
                $searchTerm = '%' . strtolower(str_replace(' ', '%', $searchTerm)) . '%';
                $driver = config('database.default');
                
                match ($driver) {
                    'pgsql' => $this->orWhereRaw("lower($column->>'{$locale}') like ?", [$searchTerm]),
                    'mysql' => $this->orWhereRaw("lower($column->\"$.{$locale}\") like ?", [$searchTerm]),
                    default => $this->orWhereContains($column, $searchTerm)
                };
            }

            return $this;
        });

        /** @see https://freek.dev/1182-searching-models-using-a-where-like-query-in-laravel */
        /** Post::whereRelationsLike(['name', 'text', 'author.name', 'tags.name'], $searchTerm)->get(); */
        Builder::macro('whereRelationsLike', function ($attributes, ?string $searchTerm): Builder
        {
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

        /** Order alphabetically on Spatie translatable column */
        Builder::macro('orderByTranslation', function (string $field = 'name', $order = 'asc', $locale = null): Builder
        {

            $locale ??= app()->getLocale();
            $driver = config('database.default');

            if ($driver === 'pgsql') {
                // PostgreSQL collations follow the pattern: language_territory.encoding
                $collation  = match ($locale) {
                    'sv' => 'sv_SE.UTF-8', // Swedish
                    'es' => 'es_ES.UTF-8', // Spanish
                    'de' => 'de_DE.UTF-8', // German
                    'da' => 'da_DK.UTF-8', // Danish
                    'no' => 'nb_NO.UTF-8', // Norwegian Bokmål
                    default => 'en_US.UTF-8' // Default English
                };
            } else {
                // @src https://dev.mysql.com/doc/refman/8.4/en/charset-unicode-sets.html
                $collation = match ($locale) {
                    'sv' => 'utf8mb4_sv_0900_ai_ci', // swedish
                    'es' => 'utf8mb4_es_0900_ai_ci', // spanish modern
                    'de' => 'utf8mb4_de_pb_0900_ai_ci', // german phonebook
                    'da' => 'utf8mb4_da_0900_ai_ci', // danish
                    'no' => 'utf8mb4_nb_0900_ai_ci', // norwegian bokmål
                    default => 'utf8mb4_0900_ai_ci' // v9 (latest) and largest set of unicode chars
                };
            }

            if (property_exists($this->model, 'translatable') && in_array($field, $this->model->translatable, true)) {
                match ($driver) {
                    'pgsql' => $this->orderByRaw("$field->>'$locale' COLLATE \"$collation\" $order"),
                    'mysql' => $this->orderByRaw("json_unquote(json_extract(`$field`, '$.\"$locale\"')) COLLATE $collation $order"),
                    default => $this->orderBy($field, $order)
                };

                return $this;
            }

            match ($driver) {
                'pgsql' => $this->orderByRaw("$field COLLATE \"$collation\" $order"),
                'mysql' => $this->orderByRaw("$field COLLATE $collation $order"),
                default => $this->orderBy($field, $order)
            };

            return $this;
        });

        /** Overlapping dates query */
        Builder::macro('whereOverlaps', function (
            string $startColumn,
            string $endColumn,
            string | CarbonInterface | \DateTimeInterface $startDateTime,
            string | CarbonInterface | \DateTimeInterface $endDateTime,
            $tz = null
        ): Builder {
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
