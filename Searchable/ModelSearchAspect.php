<?php

namespace Spatie\Searchable;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;

class ModelSearchAspect extends SearchAspect
{
    /** @var \Illuminate\Database\Eloquent\Model */
    protected $model;

    /** @var array */
    protected $properties;

    public function __construct(Model $model, array $properties = [])
    {
        $this->model = $model;
        $this->properties = $properties;
    }

    public static function forModel(Model $model, ...$properties): self
    {
        return new self($model, $properties);
    }

    public function addSearchableProperty(string $property, bool $partial = true): self
    {
        array_add(
            $this->properties,
            $property,
            ['property' => $property, 'partial' => $partial]
        );

        return $this;
    }

    public function canBeUsedBy(User $user): bool
    {
        if (! app(Gate::class)->has($this->model)) {
            return true;
        }

        return $user->can($this->model, 'view');
    }

    public function getResults(string $term, User $user): Collection
    {
        $query = ($this->model)::query();

        $this->addSearchConditions($query, $term);

        return $query->get();
    }

    public function getType(): string
    {
        $model = new ($this->model)();

        if (property_exists($model, 'searchableType')) {
            return $model->searchableType;
        }

        return $model->getTable();
    }

    protected function addSearchConditions(Builder $query, string $term)
    {
        foreach ($this->properties as $property) {
            $sql = "LOWER({$property['property']}) LIKE ?";

            $term = mb_strtolower($term, 'UTF8');

            $property['partial']
                ? $query->whereRaw($sql, ["%{$term}%"])
                : $query->where($property['property'], $query);
        }
    }
}
