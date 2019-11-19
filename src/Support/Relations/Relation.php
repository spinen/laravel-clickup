<?php

namespace Spinen\ClickUp\Support\Relations;

use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Macroable;
use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Support\Builder;
use Spinen\ClickUp\Support\Model;

/**
 * Class Relation
 *
 * @package Spinen\ClickUp\Support\Relations
 */
abstract class Relation
{
    use ForwardsCalls, Macroable {
        __call as macroCall;
    }

    /**
     * The Eloquent builder builder instance.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * The parent model instance.
     *
     * @var Model
     */
    protected $parent;

    /**
     * The related model instance.
     *
     * @var Model
     */
    protected $related;

    /**
     * Create a new relation instance.
     *
     * @param Builder $builder
     * @param Model $parent
     *
     * @return void
     * @throws InvalidRelationshipException
     */
    public function __construct(Builder $builder, Model $parent)
    {
        $this->builder = $builder;
        $this->parent = $parent;
        $this->related = $builder->getModel();
    }

    /**
     * Handle dynamic method calls to the relationship.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        $result = $this->forwardCallTo($this->getBuilder(), $method, $parameters);

        if ($result === $this->getBuilder()) {
            return $this;
        }

        return $result;
    }

    /**
     * Get the Builder instance
     *
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * Get the parent Model instance
     *
     * @return Model
     */
    public function getParent(): Model
    {
        return $this->parent;
    }

    /**
     * Get the related Model instance
     *
     * @return Model
     */
    public function getRelated(): Model
    {
        return $this->related;
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    abstract public function getResults();
}
