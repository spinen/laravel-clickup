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
 * Class Checklist
 *
 *
 * @property bool $resolved
 * @property bool $unresolved
 * @property Carbon $date_created
 * @property Collection $items
 * @property float $orderindex
 * @property string $id
 * @property string $name
 * @property string $task_id
 * @property Task $task
 */
class Checklist extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date_created' => 'datetime:Uv',
        'id' => 'string',
        'orderindex' => 'float',
        'resolved' => 'boolean',
        'task_id' => 'string',
        'unresolved' => 'boolean',
    ];

    /**
     * Path to API endpoint.
     *
     * @var string
     */
    protected $path = '/checklist';

    /**
     * Accessor for Items.
     *
     *
     * @throws NoClientException
     */
    public function getItemsAttribute(array $items): Collection
    {
        return $this->givenMany(Item::class, $items);
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
