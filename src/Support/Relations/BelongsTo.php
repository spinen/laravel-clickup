<?php

namespace Spinen\ClickUp\Support\Relations;

use GuzzleHttp\Exception\GuzzleException;
use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Exceptions\TokenException;
use Spinen\ClickUp\Support\Builder;
use Spinen\ClickUp\Support\Model;

/**
 * Class BelongsTo
 *
 * @package Spinen\ClickUp\Support\Relations
 */
class BelongsTo extends Relation
{
    /**
     * The child model instance of the relation.
     */
    protected $child;

    /**
     * The foreign key of the parentModel model.
     *
     * @var string
     */
    protected $foreignKey;

    /**
     * Create a new belongs to relationship instance.
     *
     * @param Builder $builder
     * @param Model $child
     * @param string $foreignKey
     *
     * @return void
     * @throws InvalidRelationshipException
     */
    public function __construct(Builder $builder, Model $child, $foreignKey)
    {
        $this->foreignKey = $foreignKey;

        // In the underlying base relationship class, this variable is referred to as
        // the "parentModel" since most relationships are not inversed. But, since this
        // one is we will create a "child" variable for much better readability.
        $this->child = $child;

        parent::__construct($builder->whereId($this->getForeignKey()), $this->getChild());
    }

    /**
     * Get the child Model
     *
     * @return Model
     */
    public function getChild(): Model
    {
        return $this->child;
    }

    /**
     * Get the foreign key's name
     *
     * @return integer|string
     */
    public function getForeignKey()
    {
        return $this->getChild()->{$this->getForeignKeyName()};
    }

    /**
     * Get the name of the foreign key's name
     *
     * @return string
     */
    public function getForeignKeyName(): string
    {
        return $this->foreignKey;
    }

    /**
     * Get the results of the relationship.
     *
     * @return Model|null
     * @throws GuzzleException
     * @throws InvalidRelationshipException
     * @throws NoClientException
     * @throws TokenException
     */
    public function getResults(): ?Model
    {
        if (!$this->getForeignKey()) {
            return null;
        }

        return $this->getBuilder()
                    ->get()
                    ->first();
    }
}
