<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class Member
 *
 * @property int $id
 * @property int $role
 * @property string $color
 * @property string $email
 * @property string $initials
 * @property string $profilePicture
 * @property string $username
 * @property Task|null $task
 * @property TaskList|null $list
 */
class Member extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'role' => 'integer',
    ];

    /**
     * Path to API endpoint.
     */
    protected string $path = '/member';

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
     * Optional Child of Task
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function task(): ?ChildOf
    {
        return is_a($this->parentModel, Task::class) ? $this->childOf(Task::class) : null;
    }
}
