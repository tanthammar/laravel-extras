# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Laravel Extras** is a Laravel package that provides utility classes, query macros, and helper functions to extend Laravel's core functionality. The package focuses on database query enhancements, data manipulation utilities, and developer conveniences.

## Development Commands

### Package Installation
```bash
composer require tanthammar/laravel-extras
```

### Publishing Migrations
```bash
php artisan vendor:publish --provider="TantHammar\LaravelExtras\LaravelExtrasServiceProvider" --tag="migrations"
```

### Common Development Tasks
- **Testing**: No specific test commands found - check with maintainer for testing setup
- **Code Quality**: No linting/formatting tools configured - follow PSR-4 and Laravel conventions
- **Package Development**: This follows standard Laravel package structure using Spatie Laravel Package Tools

## Code Architecture

### Service Provider (`LaravelExtrasServiceProvider.php`)
Central orchestrator that registers:
- 20+ Eloquent Builder macros for enhanced querying
- Blade directives (`@prettyPrint`)
- Package migrations (ocr_numbers table)
- Locale-specific database collations

### Core Components

#### Database Query Enhancements
The package extends Laravel's Eloquent Builder with comprehensive search capabilities:

**Search Macros:**
- `whereStartsWith()` / `orWhereStartsWith()` - Case-insensitive prefix matching
- `whereEndsWith()` / `orWhereEndsWith()` - Case-insensitive suffix matching
- `whereAllLike()` / `orWhereAnyLike()` - Multiple column searching
- `whereContains()` / `orWhereContains()` - Space-separated word searching
- `whereRelationsLike()` - Search across model relationships

**Translatable Content Support:**
- `whereTranslatableStartsWith()` / `orWhereTranslatableStartsWith()`
- `whereTranslatableLike()` / `orWhereTranslatableLike()`
- `whereTranslatableContains()` / `orWhereTranslatableContains()`
- Database-specific JSON query optimization (PostgreSQL & MySQL)

**Utility Macros:**
- `whereOverlaps()` - Date range overlap detection
- `existsById()` / `existsByUuid()` - Existence checks
- `orderByTranslation()` / `orderByLocale()` - Locale-aware sorting

#### Utility Classes

**String/Text Processing:**
- `CleanNumber` - Strips non-numeric characters (phone number cleaning)
- `NoWhiteSpace` - Removes all whitespace including tabs/line breaks
- `MarkdownToHtmlString` - Converts Markdown to HTML with Laravel's Htmlable interface

**Data Manipulation:**
- `ArrHelper` - Array utilities with `filledOnly()` method
- `Mutate` - Serialization utilities for safe unserialization
- `PrettyPrint` - Debug utility for formatted output

**Database & Financial:**
- `MoneyIntegerCast` - Eloquent cast for monetary values as integers
- `DBconstraints` - Database constraint management (deprecated)

**Specialized Utilities:**
- `OCR3` - Swedish bank reference number generation using Luhn algorithm
- `SKU` - Product SKU generation from source strings

## Key Patterns

### Multi-Database Support
- Consistent support for PostgreSQL, MySQL, and fallback implementations
- Database-specific optimizations (ILIKE vs LIKE, JSON operators)
- Locale-aware collation handling for proper sorting

### Internationalization
- Locale-specific collations (Swedish, Spanish, German, Danish, Norwegian)
- Translatable content querying with JSON column support
- Configurable locale fallbacks

### Null Safety
- Consistent null handling across all utilities
- Safe string processing with proper empty checks
- Graceful degradation when values are missing

## Dependencies

- **Laravel Framework** (^11.0|^12.0)
- **Spatie Laravel Package Tools** (^1.9.2)
- **PHP** (^8.3|^8.4)

## Database Requirements

- MySQL or PostgreSQL database (required for builder macros using raw SQL)
- OCR3 functionality requires `ocr_numbers` table for uniqueness constraints

## Code Conventions

- Follow PSR-4 autoloading standard
- Use Laravel conventions and patterns
- Maintain backward compatibility with existing query macros
- All query macros should handle null values gracefully
- Case-insensitive searching by default
- Support for both associative and indexed arrays in array utilities