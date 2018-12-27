<?php

namespace Spatie\Searchable;

class SearchableAttribute
{
    /** @var string */
    protected $attribute;

    /** @var bool */
    protected $partial;

    public function __construct(string $attribute, bool $partial = true)
    {
        $this->attribute = $attribute;

        $this->partial = $partial;
    }

    public static function create(string $attribute, bool $partial = true): self
    {
        return new self($attribute, $partial);
    }

    public static function createExact(string $attribute): self
    {
        return static::create($attribute, false);
    }

    public static function createMany(array $attributes): array
    {
        return collect($attributes)
            ->map(function ($attribute) {
                return new self($attribute);
            })
            ->toArray();
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function isPartial(): bool
    {
        return $this->partial;
    }
}
