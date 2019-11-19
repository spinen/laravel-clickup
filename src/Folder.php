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
 * @package Spinen\ClickUp
 *
 * @property boolean $archived
 * @property boolean $hidden
 * @property boolean $override_statuses
 * @property Collection $lists
 * @property Collection $statuses
 * @property Collection $views
 * @property float $orderindex
 * @property integer $id
 * @property integer $task_count
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
        'archived'          => 'boolean',
        'hidden'            => 'boolean',
        'id'                => 'integer',
        'orderindex'        => 'float',
        'override_statuses' => 'boolean',
        'task_count'        => 'integer',
    ];

    /**
     * Path to API endpoint.
     *
     * @var string
     */
    protected $path = '/folder';

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
     * Accessor for Lists.
     *
     * @param array $lists
     *
     * @return Collection
     * @throws NoClientException
     */
    public function getListsAttribute(array $lists): Collection
    {
        return $this->givenMany(TaskList::class, $lists);
    }

    /**
     * @return ChildOf
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function space(): ChildOf
    {
        return $this->childOf(Space::class);
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
