<?php

namespace Spatie\Searchable;

use Illuminate\Foundation\Auth\User;

class Search
{
    protected $aspects = [];

    public function registerAspect(SearchAspect $searchAspect): self
    {
        $this->aspects[$searchAspect->getType()] = $searchAspect;

        return $this;
    }

    public function perform(string $query, User $user): SearchResultCollection
    {
        $searchResults = new SearchResultCollection();

        collect($this->aspects)
            ->map(function (string $aspectClassName) {
                return app($aspectClassName);
            })
            ->filter(function (SearchAspect $aspect) use ($user) {
                return $aspect->canBeUsedBy($user);
            })
            ->each(function (SearchAspect $aspect) use ($query, $user, $searchResults) {
                $searchResults->addResults($aspect->getType(), $aspect->getResults($query, $user));
            });

        return $searchResults;
    }
}
