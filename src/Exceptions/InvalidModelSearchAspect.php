<?php

namespace Spatie\Searchable\Exceptions;

use Exception;

class InvalidModelSearchAspect extends Exception
{
    public static function noSearchableAttributes(string $model): self
    {
        return new self("Model search aspect for `{$model}` doesn't have any searchable attributes.");
    }
}
