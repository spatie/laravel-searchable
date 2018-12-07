<?php

namespace Spatie\Searchable;

use Spatie\Searchable\Contracts\SearchResult as SearchResultInterface;

class BasicSearchResult implements SearchResultInterface
{
    /** @var string */
    protected $name;

    /** @var null|string */
    protected $url;

    /** @var null|string */
    protected $description;

    /** @var null|string */
    protected $imageUrl;

    public function __construct(string $name, ?string $url = null, ?string $description = null, ?string $imageUrl = null)
    {
        $this->name = $name;
        $this->url = $url;
        $this->description = $description;
        $this->imageUrl = $imageUrl;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function url(): ?string
    {
        return $this->url;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function imageUrl(): ?string
    {
        return $this->imageUrl;
    }
}
