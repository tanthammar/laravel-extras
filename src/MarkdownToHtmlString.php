<?php

namespace TantHammar\LaravelExtras;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class MarkdownToHtmlString implements Htmlable
{
    public function __construct(
        public readonly string $markdown
    ) {
    }

    public function toHtml(): string
    {
        return Str::of($this->markdown)->markdown();
    }

    public function __toString(): string
    {
        return Str::of($this->markdown)->markdown()->toHtmlString();
    }
}
