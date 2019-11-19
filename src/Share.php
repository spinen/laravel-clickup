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
 * @package Spinen\ClickUp
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
     *
     * @var string
     */
    protected $path = '/share';

    /**
     * Accessor for Folders.
     *
     * @param array $folders
     *
     * @return Collection
     * @throws NoClientException
     */
    public function getFoldersAttribute(array $folders): Collection
    {
        return $this->givenMany(Folder::class, $folders);
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
     * Accessor for Tasks.
     *
     * @param array $tasks
     *
     * @return Collection
     * @throws NoClientException
     */
    public function getTasksAttribute(array $tasks): Collection
    {
        return $this->givenMany(Task::class, $tasks);
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
}
