<?php

namespace Spinen\ClickUp;

use Carbon\Carbon;
use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\Support\Relations\ChildOf;
use Spinen\ClickUp\Support\Relations\HasMany;

/**
 * Class TaskList
 *
 *
 * @property bool $archived
 * @property bool $due_date_time
 * @property bool $override_statuses
 * @property bool $start_date_time
 * @property Carbon $due_date
 * @property Carbon $start_date
 * @property Collection $statuses
 * @property float $orderindex
 * @property int $id
 * @property int $task_count
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
        'archived' => 'boolean',
        'due_date' => 'datetime:Uv',
        'due_date_time' => 'boolean',
        'id' => 'integer',
        'orderindex' => 'float',
        'override_statuses' => 'boolean',
        'start_date' => 'datetime:Uv',
        'start_date_time' => 'boolean',
        'task_count' => 'integer',
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
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
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
     * Accessor for Assignee.
     *
     * @param  array  $assignee
     *
     * @throws NoClientException
     */
    public function getAssigneeAttribute($assignee): Member
    {
        return $this->givenOne(Member::class, $assignee);
    }

    /**
     * Accessor for Priority.
     *
     * @param  array  $priority
     *
     * @throws NoClientException
     */
    public function getPriorityAttribute($priority): Priority
    {
        return $this->givenOne(Priority::class, $priority);
    }

    /**
     * Accessor for Status.
     *
     * @param  array  $status
     *
     * @throws NoClientException
     */
    public function getStatusAttribute($status): Status
    {
        return $this->givenOne(Status::class, $status);
    }

    /**
     * Accessor for Statuses.
     *
     *
     * @throws NoClientException
     */
    public function getStatusesAttribute(array $statuses): Collection
    {
        return $this->givenMany(Status::class, $statuses);
    }

    /**
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
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function taskTemplates(): HasMany
    {
        return $this->hasMany(TaskTemplate::class);
    }

    /**
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function views(): HasMany
    {
        return $this->hasMany(View::class);
    }
}
