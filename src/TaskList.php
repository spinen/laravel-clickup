<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Carbon\Carbon;
use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\Support\Relations\ChildOf;
use Spinen\ClickUp\Support\Relations\HasMany;

/**
 * Class TaskList
 *
 * @package Spinen\ClickUp
 *
 * @property boolean $archived
 * @property boolean $due_date_time
 * @property boolean $override_statuses
 * @property boolean $start_date_time
 * @property Carbon $due_date
 * @property Carbon $start_date
 * @property Collection $statuses
 * @property float $orderindex
 * @property integer $id
 * @property integer $task_count
 * @property Member $assignee
 * @property Priority $priority
 * @property Status $status
 * @property string $content
 * @property string $name
 */
class TaskList extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'archived'          => 'boolean',
        'due_date'          => 'datetime:U',
        'due_date_time'     => 'boolean',
        'id'                => 'integer',
        'orderindex'        => 'float',
        'override_statuses' => 'boolean',
        'start_date'        => 'datetime:U',
        'start_date_time'   => 'boolean',
        'task_count'        => 'integer',
    ];

    /**
     * Path to API endpoint.
     *
     * @var string
     */
    protected $path = '/list';

    /**
     * Some of the responses have the collections under a property
     *
     * @var string|null
     */
    protected $responseCollectionKey = 'lists';

    /**
     * Some of the responses have the data under a property
     *
     * @var string|null
     */
    protected $responseKey = 'list';

    /**
     * @return HasMany

     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return HasMany

     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
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
     * Accessor for Assignee.
     *
     * @param array $assignee
     *
     * @return Member
     * @throws NoClientException
     */
    public function getAssigneeAttribute($assignee): Member
    {
        return $this->givenOne(Member::class, $assignee);
    }

    /**
     * Accessor for Priority.
     *
     * @param array $priority
     *
     * @return Priority
     * @throws NoClientException
     */
    public function getPriorityAttribute($priority): Priority
    {
        return $this->givenOne(Priority::class, $priority);
    }

    /**
     * Accessor for Status.
     *
     * @param array $status
     *
     * @return Status
     * @throws NoClientException
     */
    public function getStatusAttribute($status): Status
    {
        return $this->givenOne(Status::class, $status);
    }

    /**
     * Accessor for Statuses.
     *
     * @param array $statuses
     *
     * @return Collection
     * @throws NoClientException
     */
    public function getStatusesAttribute(array $statuses): Collection
    {
        return $this->givenMany(Status::class, $statuses);
    }

    /**
     * @return HasMany

     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
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
        return $this->hasMany(Task::class);
    }

    /**
     * @return HasMany

     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function taskTemplates(): HasMany
    {
        return $this->hasMany(TaskTemplate::class);
    }

    /**
     * @return HasMany

     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function views(): HasMany
    {
        return $this->hasMany(View::class);
    }
}
