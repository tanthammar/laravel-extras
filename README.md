# Helpers and Macros for Laravel

## Requirements
- PHP 8.1
- Laravel v9.0
- MySQL db (for builder macros using raw sql)

## Installation
```bash
composer require tanthammar/laravel-extras
```

## Helpers
See src/...

## Macros
See src/LaravelExtrasServiceProvider.php

## Databases
Please observe that this package publishes a migration file for a table called `ocr_numbers`.
It is used to assure cross table uniqueness of ocr-numbers.
Publish the migration file:

```bash
php artisan vendor:publish --provider="Tanthammar\LaravelExtras\LaravelExtrasServiceProvider" --tag="migrations"
```


## Examples

### Convert markdown in translation strings to html for blade files
```php
//Filament placeholder field with translation string containing Markdown tags

Placeholder::make(trans('fields.accounting-chart'))
    ->disableLabel()
    ->content(new MarkdownToHtmlString(__('fields.account_hint')))
    ->columnSpan('full')
```

### Swap the order/sorting of an array, like swap the 3rd row with the 1st. the 1st will become the 3rd.

```php
$assocArray = [
    'item_one'   => ['name' => 'One'],
    'item_two'   => ['name' => 'Two'],
    'item_three' => ['name' => 'Three'],
    'item_four'  => ['name' => 'Four'],
];
 
$newArray = Arr::swap($array, 'item_one', 'item_three');
 
/*
 * [
 *     'item_three' => ['name' => 'Three'],
 *     'item_two'   => ['name' => 'Two'],
 *     'item_one'   => ['name' => 'One'],
 *     'item_four'  => ['name' => 'Four'],
 * ]
 */
```


## Documentation
There won't be much documentation written, this repository will grow as I add items.
The source code should contain enough hints to be self-explanatory.



