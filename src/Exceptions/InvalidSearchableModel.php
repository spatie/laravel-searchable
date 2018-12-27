<?php

namespace Spatie\Searchable\Exceptions;

use Exception;

class InvalidSearchableModel extends Exception
{
    public static function notAModel(string $model): self
    {
        return new self("Class `{$model}` is not an Eloquent model.");
    }

    public static function modelDoesNotImplementSearchable(string $model): self
    {
        return new self("Model `{$model}` is added as a model search aspect but does not implement the `Spatie\Searchable\Searchable` interface.");
    }
}
