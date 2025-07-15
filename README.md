# Helpers and Macros for Laravel

## Requirements
- PHP 8.3+
- Laravel v11.0+
- MySQL or PostgreSQL database (for builder macros using raw SQL)

## Installation
```bash
composer require tanthammar/laravel-extras
```

## Migration Setup
This package publishes a migration file for the `ocr_numbers` table, used to ensure cross-table uniqueness of OCR numbers.

```bash
php artisan vendor:publish --provider="TantHammar\LaravelExtras\LaravelExtrasServiceProvider" --tag="migrations"
php artisan migrate
```

## Query Builder Macros

### Search and Filter Macros

#### whereStartsWith / orWhereStartsWith
Case-insensitive prefix matching:
```php
// Find users whose email starts with 'john'
User::whereStartsWith('email', 'john')->get();

// Chain with other conditions
User::where('active', true)->orWhereStartsWith('name', 'admin')->get();
```

#### whereEndsWith / orWhereEndsWith
Case-insensitive suffix matching:
```php
// Find users with gmail addresses
User::whereEndsWith('email', 'gmail.com')->get();
```

#### whereAllLike
Search multiple columns where ALL must match:
```php
// Find users where BOTH name and email contain 'john'
User::whereAllLike(['name', 'email'], 'john')->get();
```

#### orWhereAnyLike
Search multiple columns where ANY can match:
```php
// Find users where name OR email contains 'john'
User::where('active', true)->orWhereAnyLike(['name', 'email'], 'john')->get();
```

#### whereContains / orWhereContains
Space-separated word searching:
```php
// Find users where name contains both 'john' AND 'doe' (in any order)
User::whereContains('name', 'john doe')->get();

// OR condition with contains
User::where('active', true)->orWhereContains(['name', 'email'], 'john doe')->get();
```

#### whereRelationsLike
Search across model relationships:
```php
// Search posts by content, title, or author name
Post::whereRelationsLike(['title', 'content', 'author.name'], 'laravel')->get();
```

### Translatable Content Macros

#### whereTranslatableStartsWith / orWhereTranslatableStartsWith
Search JSON translation columns:
```php
// Find events where localized name starts with 'Summer'
Event::whereTranslatableStartsWith('name', 'Summer')->get();

// Search specific locale
Event::whereTranslatableStartsWith('name', 'Été', 'fr')->get();
```

#### whereTranslatableLike / orWhereTranslatableLike
```php
// Find events where localized name contains 'festival'
Event::whereTranslatableLike('name', 'festival')->get();
```

#### whereTranslatableContains / orWhereTranslatableContains
```php
// Find events where localized name contains 'music festival' (both words)
Event::whereTranslatableContains('name', 'music festival')->get();
```

### Utility Macros

#### whereOverlaps
Find overlapping date ranges:
```php
// Find bookings that overlap with a specific date range
Booking::whereOverlaps('start_date', 'end_date', '2024-01-01', '2024-01-31')->get();
```

#### existsById / existsByUuid
Quick existence checks:
```php
// Check if user exists by ID
if (User::existsById(123)) {
    // User exists
}

// Check if record exists by UUID
if (Product::existsByUuid('550e8400-e29b-41d4-a716-446655440000')) {
    // Product exists
}
```

#### orderByTranslation / orderByLocale
Locale-aware sorting:
```php
// Sort by translated name field
Event::orderByTranslation('name', 'asc')->get();

// Sort by regular field with locale collation
User::orderByLocale('name', 'desc')->get();
```

## Helper Classes

### String and Text Processing

#### CleanNumber
Remove all non-numeric characters:
```php
use TantHammar\LaravelExtras\CleanNumber;

$phone = CleanNumber::clean('+46 (0)70-123 45 67');
// Result: '46701234567'
```

#### NoWhiteSpace
Remove all whitespace characters:
```php
use TantHammar\LaravelExtras\NoWhiteSpace;

$clean = NoWhiteSpace::clean("Hello\n\tWorld   ");
// Result: 'HelloWorld'
```

#### MarkdownToHtmlString
Convert Markdown to HTML:
```php
use TantHammar\LaravelExtras\MarkdownToHtmlString;

$html = new MarkdownToHtmlString('**Bold text** with [link](https://example.com)');
// Use in Blade templates
{!! $html !!}

// Filament placeholder field example
Placeholder::make(trans('fields.accounting-chart'))
    ->disableLabel()
    ->content(new MarkdownToHtmlString(__('fields.account_hint')))
    ->columnSpan('full')
```

### Data Manipulation

#### Array Helper
```php
use TantHammar\LaravelExtras\ArrHelper;

// Remove empty values from array
$clean = ArrHelper::filledOnly(['name' => 'John', 'email' => '', 'age' => null]);
// Result: ['name' => 'John']

// Swap array items (using Arr::swap macro)
$assocArray = [
    'item_one'   => ['name' => 'One'],
    'item_two'   => ['name' => 'Two'],
    'item_three' => ['name' => 'Three'],
    'item_four'  => ['name' => 'Four'],
];

$newArray = Arr::swap($assocArray, 'item_one', 'item_three');
/*
 * Result:
 * [
 *     'item_three' => ['name' => 'Three'],
 *     'item_two'   => ['name' => 'Two'],
 *     'item_one'   => ['name' => 'One'],
 *     'item_four'  => ['name' => 'Four'],
 * ]
 */
```

#### Money Integer Cast
Store monetary values as integers:
```php
use TantHammar\LaravelExtras\MoneyIntegerCast;

class Product extends Model
{
    protected $casts = [
        'price' => MoneyIntegerCast::class,
    ];
}

// Store $19.99 as 1999 (integer)
$product = new Product();
$product->price = 19.99;  // Stored as 1999
echo $product->price;     // Outputs: 19.99
```

### Specialized Utilities

#### OCR3 (Swedish Bank References)
Generate OCR numbers with Luhn algorithm:
```php
use TantHammar\LaravelExtras\OCR3;

// Generate unique OCR number
$ocr = OCR3::unique();
// Result: '1234567890' (with valid check digit)

// Generate with specific length
$ocr = OCR3::unique(8);
// Result: '12345678' (8 digits with check digit)

// Use in factories
$factory->define(Invoice::class, function (Faker $faker) {
    return [
        'ocr_number' => $faker->ocr3(),
    ];
});
```

#### SKU Generator
Generate product SKUs:
```php
use TantHammar\LaravelExtras\SKU;

$sku = SKU::generate('Premium Widget');
// Result: 'PW-ABC123' (source prefix + random signature)
```

### Debug Utilities

#### PrettyPrint
Formatted debugging output:
```php
use TantHammar\LaravelExtras\PrettyPrint;

// In PHP
PrettyPrint::dump($complexArray);

// In Blade templates
@prettyPrint($data)
```

## Breaking Changes from Previous Versions

### Query Macro Renames (v4.0+)
- `whereLike()` → `whereAllLike()`
- `orWhereLike()` → `orWhereAnyLike()`

### Version Requirements (v4.0+)
- Minimum PHP version: 8.3+ (was 8.1+)
- Minimum Laravel version: 11.0+ (was 9.0+)

### Implementation Changes (v4.0+)
- All query macros now use Laravel's native `whereLike()` method with `caseSensitive: false`
- Removed custom database operator detection in favor of Laravel's built-in handling
- Improved query logic for proper OR condition chaining



