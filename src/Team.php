<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\Support\Relations\HasMany;

/**
 * Class Team
 *
 * @property Collection $goals
 * @property Collection $members
 * @property Collection $shares
 * @property Collection $spaces
 * @property Collection $tasks
 * @property Collection $taskTemplates
 * @property Collection $views
 * @property Collection $webhooks
 * @property int $id
 * @property string $avatar
 * @property string $color
 * @property string $name
 */
class Team extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Path to API endpoint.
     */
    protected string $path = '/team';

    /**
     * Has many Goals
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    /**
     * Accessor for Members.
     *
     * @throws NoClientException
     */
    public function getMembersAttribute(?array $members): Collection
    {
        return $this->givenMany(Member::class, $members, true);
    }

    /**
     * Has many Shares
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function shares(): HasMany
    {
        return $this->hasMany(Share::class);
    }

    /**
     * Has many Spaces
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function spaces(): HasMany
    {
        return $this->hasMany(Space::class);
    }

    /**
     * Has many Tasks
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Has many TaskTemplates
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function taskTemplates(): HasMany
    {
        return $this->hasMany(TaskTemplate::class);
    }

    /**
     * Has many Views
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function views(): HasMany
    {
        return $this->hasMany(View::class);
    }

    /**
     * Has many Webhooks
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }
}
