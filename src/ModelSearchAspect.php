<?php

namespace Spatie\Searchable;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\Access\Gate;
use Spatie\Searchable\Exceptions\InvalidSearchableModel;
use Spatie\Searchable\Exceptions\InvalidModelSearchAspect;

class ModelSearchAspect extends SearchAspect
{
    /** @var \Illuminate\Database\Eloquent\Model */
    protected $model;

    /** @var array */
    protected $attributes = [];

    /**
     * @param string $model
     * @param array|\Closure $attributes
     *
     * @throws \Spatie\Searchable\Exceptions\InvalidSearchableModel
     */
    public function __construct(string $model, $attributes = [])
    {
        if (!is_subclass_of($model, Model::class)) {
            throw InvalidSearchableModel::notAModel($model);
        }

        if (!is_subclass_of($model, Searchable::class)) {
            throw InvalidSearchableModel::modelDoesNotImplementSearchable($model);
        }

        $this->model = $model;

        if (is_array($attributes)) {
            $this->attributes = SearchableAttribute::createMany($attributes);
        }

        if (is_callable($attributes)) {
            $callable = $attributes;

            $callable($this);
        }
    }

    public static function forModel(string $model, ...$attributes): self
    {
        return new self($model, $attributes);
    }

    public function addSearchableAttribute(string $attribute, bool $partial = true): self
    {
        $this->attributes[] = SearchableAttribute::create($attribute, $partial);

        return $this;
    }

    public function addExactSearchableAttribute(string $attribute): self
    {
        $this->attributes[] = SearchableAttribute::createExact($attribute);

        return $this;
    }

    public function canBeUsedBy(User $user): bool
    {
        if (!app(Gate::class)->has($this->model)) {
            return true;
        }

        return $user->can($this->model, 'view');
    }

    public function getType(): string
    {
        $model = new $this->model();

        if (property_exists($model, 'searchableType')) {
            return $model->searchableType;
        }

        return $model->getTable();
    }

    public function getResults(string $term, User $user = null): Collection
    {
        if (empty($this->attributes)) {
            throw InvalidModelSearchAspect::noSearchableAttributes($this->model);
        }

        $query = ($this->model)::query();

        $this->addSearchConditions($query, $term);

        return $query->get();
    }

    protected function addSearchConditions(Builder $query, string $term)
    {
        foreach ($this->attributes as $attribute) {
            $sql = "LOWER({$attribute->getAttribute()}) LIKE ?";

            $term = mb_strtolower($term, 'UTF8');

            $attribute->isPartial()
                ? $query->orWhereRaw($sql, ["%{$term}%"])
                : $query->orWhere($attribute->getAttribute(), $term);
        }
    }
}
