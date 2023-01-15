<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class Time
 *
 *
 * @property Collection $intervals
 * @property int $time
 * @property Member $user
 * @property Task $task
 */
class Time extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'time' => 'integer',
    ];

    /**
     * Path to API endpoint.
     *
     * @var string
     */
    protected $path = '/time';

    /**
     * Some of the responses have the data under a property
     *
     * @var string|null
     */
    protected $responseKey = 'data';

    /**
     * Accessor for Intervals.
     *
     *
     * @throws NoClientException
     */
    public function getIntervalsAttribute(array $intervals): Collection
    {
        return $this->givenMany(Interval::class, $intervals);
    }

    /**
     * Accessor for User.
     *
     * @param  array  $user
     *
     * @throws NoClientException
     */
    public function getUserAttribute($user): Member
    {
        return $this->givenOne(Member::class, $user);
    }

    /**
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function task(): ChildOf
    {
        return $this->childOf(Task::class);
    }
}
