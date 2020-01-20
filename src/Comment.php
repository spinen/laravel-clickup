<?php

namespace Spinen\ClickUp;

use Carbon\Carbon;
use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class Comment
 *
 * @package Spinen\ClickUp
 *
 * @property array $comments
 * @property array $relations
 * @property boolean $resolved
 * @property Carbon $date
 * @property integer $id
 * @property Member $assigned_by
 * @property Member $assignee
 * @property Member $user
 * @property string $hist_id
 * @property string $text
 */
class Comment extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date'     => 'datetime:Uv',
        'id'       => 'integer',
        'resolved' => 'boolean',
    ];

    /**
     * Path to API endpoint.
     *
     * @var string
     */
    protected $path = '/comment';

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
     * Accessor for AssignedBy.
     *
     * @param array $assigned_by
     *
     * @return Member
     * @throws NoClientException
     */
    public function getAssignedByAttribute($assigned_by): Member
    {
        return $this->givenOne(Member::class, $assigned_by);
    }

    /**
     * Accessor for User.
     *
     * @param array $user
     *
     * @return Member
     * @throws NoClientException
     */
    public function getUserAttribute($user): Member
    {
        return $this->givenOne(Member::class, $user);
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
    public function task(): ?ChildOf
    {
        return is_a($this->parentModel, Task::class) ? $this->childOf(Task::class) : null;
    }

    /**
     * @return ChildOf
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function view(): ?ChildOf
    {
        return is_a($this->parentModel, View::class) ? $this->childOf(View::class) : null;
    }
}
