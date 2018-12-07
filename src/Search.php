<?php

namespace Spatie\Searchable;

use Illuminate\Foundation\Auth\User;

class Search
{
    protected $aspects = [];

    /**
     * @param string|\Spatie\Searchable\SearchAspect $searchAspect
     *
     * @return \Spatie\Searchable\Search
     */
    public function registerAspect($searchAspect): self
    {
        if (is_string($searchAspect)) {
            $searchAspect = app($searchAspect);
        }

        $this->aspects[$searchAspect->getType()] = $searchAspect;

        return $this;
    }

    public function registerModel(string $modelClass, ...$attributes): ModelSearchAspect
    {
        $searchAspect = new ModelSearchAspect($modelClass, $attributes);

        $this->registerAspect($searchAspect);

        return $searchAspect;
    }

    public function getSearchAspects(): array
    {
        return $this->aspects;
    }

    public function perform(string $query, ?User $user = null): SearchResultCollection
    {
        $searchResults = new SearchResultCollection();

        collect($this->getSearchAspects())
            ->filter(function (SearchAspect $aspect) use ($user) {
                if (! $user) {
                    return true;
                }

                return $aspect->canBeUsedBy($user);
            })
            ->each(function (SearchAspect $aspect) use ($query, $user, $searchResults) {
                $searchResults->addResults($aspect->getType(), $aspect->getResults($query, $user));
            });

        return $searchResults;
    }
}
