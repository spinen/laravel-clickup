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
 * Class Space
 *
 * @package Spinen\ClickUp
 *
 * @property array $features
 * @property boolean $archived
 * @property boolean $multiple_assignees
 * @property boolean $private
 * @property Collection $folders
 * @property Collection $members
 * @property Collection $statuses
 * @property Collection $tags
 * @property Collection $taskLists
 * @property Collection $views
 * @property integer $id
 * @property string $name
 * @property Team $team
 */
class Space extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'archived'           => 'boolean',
        'id'                 => 'integer',
        'multiple_assignees' => 'boolean',
        'private'            => 'boolean',
    ];

    /**
     * Path to API endpoint.
     *
     * @var string
     */
    protected $path = '/team/{team-id}/space';

    /**
     * Accessor for Members.
     *
     * @param array $members
     *
     * @return Collection
     * @throws NoClientException
     */
    public function getMembersAttribute(array $members): Collection
    {
        return $this->givenMany(Member::class, $members, true);
    }

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
     * @return HasMany

     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    /**
     * @return HasMany

     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function lists(): HasMany
    {
        return $this->hasMany(TaskList::class);
    }

    /**
     * @return HasMany

     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * @return ChildOf
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function team(): ChildOf
    {
        return $this->childOf(Team::class);
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
