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
 * @property array $columns
 * @property array $divide
 * @property array $filters
 * @property array $grouping
 * @property array $parent
 * @property array $settings
 * @property array $sorting
 * @property array $team_sidebar
 * @property bool $protected
 * @property Carbon $date_created
 * @property Carbon $date_protected
 * @property Collection $comments
 * @property Collection $tasks
 * @property float $orderindex
 * @property Folder|null $folder
 * @property int $creator
 * @property Member $protected_by
 * @property Space|null $space
 * @property string $id
 * @property string $name
 * @property string $protected_note
 * @property string $type
 * @property string $visibility
 * @property TaskList|null $list
 * @property Team|null $team
 */
class View extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date_created' => 'datetime:Uv',
        'date_protected' => 'integer',
        'id' => 'string',
        'orderindex' => 'float',
        'protected' => 'boolean',
    ];

    /**
     * Path to API endpoint.
     */
    protected string $path = '/view';

    /**
     * Has many Comments
     *
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
     * Optional Child of Folder
     *
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
     * @throws NoClientException
     */
    public function getProtectedByAttribute(?array $protected_by): Member
    {
        return $this->givenOne(Member::class, $protected_by);
    }

    /**
     * Optional Child of TaskList
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function list(): ?ChildOf
    {
        return is_a($this->parentModel, TaskList::class) ? $this->childOf(TaskList::class) : null;
    }

    /**
     * Optional Child of Space
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function space(): ?ChildOf
    {
        return is_a($this->parentModel, Space::class) ? $this->childOf(Space::class) : null;
    }

    /**
     * HasMany Tasks
     *
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
     * Optional Child of Team
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function team(): ?ChildOf
    {
        return is_a($this->parentModel, Team::class) ? $this->childOf(Team::class) : null;
    }
}
