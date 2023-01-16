<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class Share
 *
 * @property Collection $folders
 * @property Collection $lists
 * @property Collection $tasks
 * @property Team $team
 */
class Share extends Model
{
    /**
     * Path to API endpoint.
     */
    protected string $path = '/share';

    /**
     * Accessor for Folders.
     *
     * @throws NoClientException
     */
    public function getFoldersAttribute(?array $folders): Collection
    {
        return $this->givenMany(Folder::class, $folders);
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
     * Accessor for Tasks.
     *
     * @throws NoClientException
     */
    public function getTasksAttribute(?array $tasks): Collection
    {
        return $this->givenMany(Task::class, $tasks);
    }

    /**
     * Child of Team
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function team(): ChildOf
    {
        return $this->childOf(Team::class);
    }
}
