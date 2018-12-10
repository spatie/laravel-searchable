<?php

namespace Spatie\Searchable;

class SearchResult
{
    /** @var \Spatie\Searchable\Searchable */
    protected $result;

    /** @var string */
    protected $type;

    /** @var string */
    protected $title;

    /** @var null|string */
    protected $url;

    public function __construct(string $title, ?string $url = null)
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

    public function result(): Searchable
    {
        return $this->result;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function url(): ?string
    {
        return $this->url;
    }
}
