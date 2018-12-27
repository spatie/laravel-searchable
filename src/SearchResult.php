<?php

namespace Spatie\Searchable;

class SearchResult
{
    /** @var \Spatie\Searchable\Searchable */
    public $searchable;

    /** @var string */
    public $title;

    /** @var null|string */
    public $url;

    /** @var string */
    public $type;

    public function __construct(Searchable $searchable, string $title, ?string $url = null)
    {
        $this->searchable = $searchable;

        $this->title = $title;

        $this->url = $url;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
