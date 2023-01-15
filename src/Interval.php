<?php

namespace Spinen\ClickUp;

use Carbon\Carbon;
use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class Interval
 *
 *
 * @property Carbon $date_added
 * @property Carbon $end
 * @property Carbon $start
 * @property int $time
 * @property string $id
 * @property string $source
 * @property Task $task
 */
class Interval extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date_added' => 'datetime:Uv',
        'end' => 'datetime:Uv',
        'id' => 'string',
        'start' => 'datetime:Uv',
        'time' => 'integer',
    ];

    // TODO: Figure out how to setup relation to task
    /**
     * @return ChildOf
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    /*public function task(): ChildOf
    {
        return $this->childOf(Task::class);
    }*/
}
