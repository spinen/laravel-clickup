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
 *
 * @property array $features
 * @property bool $archived
 * @property bool $multiple_assignees
 * @property bool $private
 * @property Collection $folders
 * @property Collection $members
 * @property Collection $statuses
 * @property Collection $tags
 * @property Collection $taskLists
 * @property Collection $views
 * @property int $id
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
        'archived' => 'boolean',
        'id' => 'integer',
        'multiple_assignees' => 'boolean',
        'private' => 'boolean',
    ];

    /**
     * Path to API endpoint.
     *
     * @var string
     */
    protected $path = '/space';

    /**
     * Accessor for Members.
     *
     *
     * @throws NoClientException
     */
    public function getMembersAttribute(array $members): Collection
    {
        return $this->givenMany(Member::class, $members, true);
    }

    /**
     * Accessor for Statuses.
     *
     *
     * @throws NoClientException
     */
    public function getStatusesAttribute(array $statuses): Collection
    {
        return $this->givenMany(Status::class, $statuses);
    }

    /**
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    /**
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function lists(): HasMany
    {
        return $this->hasMany(TaskList::class);
    }

    /**
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function team(): ChildOf
    {
        return $this->childOf(Team::class);
    }

    /**
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function views(): HasMany
    {
        return $this->hasMany(View::class);
    }
}
