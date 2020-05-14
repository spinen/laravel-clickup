<?php

namespace Spinen\ClickUp;

use Carbon\Carbon;
use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\Support\Relations\ChildOf;
use Spinen\ClickUp\Support\Relations\HasMany;

/**
 * Class View
 *
 * @package Spinen\ClickUp
 *
 * @property array $columns
 * @property array $divide
 * @property array $filters
 * @property array $grouping
 * @property array $parent
 * @property array $settings
 * @property array $sorting
 * @property array $team_sidebar
 * @property boolean $protected
 * @property Carbon $date_created
 * @property Carbon $date_protected
 * @property float $orderindex
 * @property integer $creator
 * @property Member $protected_by
 * @property string $id
 * @property string $name
 * @property string $protected_note
 * @property string $type
 * @property string $visibility
 */
class View extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date_created'   => 'datetime:Uv',
        'date_protected' => 'integer',
        'id'             => 'string',
        'orderindex'     => 'float',
        'protected'      => 'boolean',
    ];

    /**
     * Path to API endpoint.
     *
     * @var string
     */
    protected $path = '/view';

    /**
     * @return HasMany

     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function comments(): HasMany
    {
        if ($this->type !== 'conversation') {
            throw new InvalidRelationshipException(
                sprintf('The view is of type [%s], but must be of type [conversation] to have comments.', $this->type)
            );
        }

        return $this->hasMany(Comment::class);
    }

    /**
     * @return ChildOf
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function folder(): ?ChildOf
    {
        return is_a($this->parentModel, Folder::class) ? $this->childOf(Folder::class) : null;
    }

    /**
     * Accessor for ProtectedBy.
     *
     * @param array $protected_by
     *
     * @return Member
     * @throws NoClientException
     */
    public function getProtectedByAttribute($protected_by): Member
    {
        return $this->givenOne(Member::class, $protected_by);
    }

    /**
     * @return ChildOf
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function list(): ?ChildOf
    {
        return is_a($this->parentModel, TaskList::class) ? $this->childOf(TaskList::class) : null;
    }

    /**
     * @return ChildOf
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function space(): ?ChildOf
    {
        return is_a($this->parentModel, Space::class) ? $this->childOf(Space::class) : null;
    }

    /**
     * @return HasMany

     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function tasks(): HasMany
    {
        if (in_array($this->type, ['conversation', 'doc', 'embed'])) {
            throw new InvalidRelationshipException(
                sprintf('The view is of type [%s], but must be of on of the task types to have tasks.', $this->type)
            );
        }

        return $this->hasMany(Task::class);
    }

    /**
     * @return ChildOf
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function team(): ?ChildOf
    {
        return is_a($this->parentModel, Team::class) ? $this->childOf(Team::class) : null;
    }
}
