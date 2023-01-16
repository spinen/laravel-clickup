<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\Support\Relations\ChildOf;
use Spinen\ClickUp\Support\Relations\HasMany;

/**
 * Class Folder
 *
 * @property bool $archived
 * @property bool $hidden
 * @property bool $override_statuses
 * @property Collection $lists
 * @property Collection $statuses
 * @property Collection $views
 * @property float $orderindex
 * @property int $id
 * @property int $task_count
 * @property Space $space
 * @property string $name
 */
class Folder extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'archived' => 'boolean',
        'hidden' => 'boolean',
        'id' => 'integer',
        'orderindex' => 'float',
        'override_statuses' => 'boolean',
        'task_count' => 'integer',
    ];

    /**
     * Path to API endpoint.
     */
    protected string $path = '/folder';

    /**
     * Accessor for Statuses.
     *
     * @throws NoClientException
     */
    public function getStatusesAttribute(?array $statuses): Collection
    {
        return $this->givenMany(Status::class, $statuses);
    }

    /**
     * Accessor for Lists.
     *
     * @throws NoClientException
     */
    public function getListsAttribute(?array $lists): Collection
    {
        return $this->givenMany(TaskList::class, $lists);
    }

    /**
     * Child of Space
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function space(): ChildOf
    {
        return $this->childOf(Space::class);
    }

    /**
     * HasMany Views
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function views(): HasMany
    {
        return $this->hasMany(View::class);
    }
}
