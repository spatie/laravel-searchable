<?php

namespace Spatie\Searchable;

class SearchResult
{
    /** @var string */
    public $title;

    /** @var null|string */
    public $url;

    /** @var string */
    public $type;

    /** @var \Spatie\Searchable\Searchable */
    public $result;

    public function __construct(Searchstring $title, ?string $url = null)
    {
        $this->title = $title;

        $this->url = $url;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setResult(Searchable $result): self
    {
        $this->result = $result;

        return $this;
    }
}
