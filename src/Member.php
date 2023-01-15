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
 *
 * @property int $id
 * @property int $role
 * @property string $color
 * @property string $email
 * @property string $initials
 * @property string $profilePicture
 * @property string $username
 * @property Task $task
 * @property TaskList $list
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
     *
     * @var string
     */
    protected $path = '/member';

    /**
     * @return ChildOf
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
     * @return ChildOf
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
