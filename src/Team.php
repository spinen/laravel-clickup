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
 * @package Spinen\ClickUp
 *
 * @property Collection $goals
 * @property Collection $members
 * @property Collection $shares
 * @property Collection $spaces
 * @property Collection $tasks
 * @property Collection $taskTemplates
 * @property Collection $views
 * @property Collection $webhooks
 * @property integer $id
 * @property string $avatar
 * @property string $color
 * @property string $name
 *
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
     *
     * @var string
     */
    protected $path = '/team';

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
     * @return HasMany
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    /**
     * @return HasMany
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function shares(): HasMany
    {
        return $this->hasMany(Share::class);
    }

    /**
     * @return HasMany
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function spaces(): HasMany
    {
        return $this->hasMany(Space::class);
    }

    /**
     * @return HasMany
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * @return HasMany
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function taskTemplates(): HasMany
    {
        return $this->hasMany(TaskTemplate::class);
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

    /**
     * @return HasMany
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }
}
