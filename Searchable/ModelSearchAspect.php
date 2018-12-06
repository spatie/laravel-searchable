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
    protected $attributes;

    public function __construct(string $model, array $attributes = [])
    {
        $this->model = $model;
        $this->attributes = $attributes;
    }

    public static function forModel(Model $model, ...$attributes): self
    {
        return new self($model, $attributes);
    }

    public function addSearchableAttribute(string $attribute, bool $partial = true): self
    {
        $this->attributes = array_add(
            $this->attributes,
            $attribute,
            ['attribute' => $attribute, 'partial' => $partial]
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
        foreach ($this->attributes as $attribute) {
            $sql = "LOWER({$attribute['attribute']}) LIKE ?";

            $term = mb_strtolower($term, 'UTF8');

            $attribute['partial']
                ? $query->whereRaw($sql, ["%{$term}%"])
                : $query->where($attribute['attribute'], $query);
        }
    }
}
