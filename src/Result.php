<?php

namespace Spinen\ClickUp;

use Carbon\Carbon;
use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class Result
 *
 * @package Spinen\ClickUp
 *
 * @property array $last_action
 * @property boolean $completed
 * @property Carbon $date_created
 * @property Collection $owners
 * @property Collection $subcategory_ids
 * @property Collection $task_ids
 * @property float $percent_completed
 * @property Goal $goal
 * @property integer $creator
 * @property integer $goal_pretty_id
 * @property string $goal_id
 * @property string $id
 * @property string $name
 * @property string $type
 * @property string $unit
 */
class Result extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'completed'         => 'boolean',
        'creator'           => 'integer',
        'date_created'      => 'datetime:U',
        'goal_pretty_id'    => 'integer',
        'id'                => 'string',
        'percent_completed' => 'float',
    ];

    /**
     * Is resource nested behind parentModel
     *
     * Several of the endpoints are nested behind another model for relationship, but then to
     * interact with the specific model, then are not nested.  This property will know when to
     * keep the specific model nested.
     *
     * @var bool
     */
    protected $nested = true;

    /**
     * Path to API endpoint.
     *
     * @var string
     */
    protected $path = '/key_result';

    /**
     * Accessor for Owners.
     *
     * @param array $owners
     *
     * @return Collection
     * @throws NoClientException
     */
    public function getOwnersAttribute(array $owners): Collection
    {
        return $this->givenMany(Member::class, $owners);
    }

    /**
     * @return ChildOf
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function goal(): ChildOf
    {
        return $this->childOf(Goal::class);
    }
}
