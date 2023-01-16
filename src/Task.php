<?php

namespace Spinen\ClickUp;

use Carbon\Carbon;
use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\Support\Relations\BelongsTo;
use Spinen\ClickUp\Support\Relations\ChildOf;
use Spinen\ClickUp\Support\Relations\HasMany;

/**
 * Class Task
 *
 * @property bool $archived
 * @property array $attachments
 * @property array $dependencies
 * @property Carbon $date_closed
 * @property Carbon $date_created
 * @property Carbon $date_updated
 * @property Carbon $due_date
 * @property Carbon $start_date
 * @property Collection $assignees
 * @property Collection $checklists
 * @property Collection $comments
 * @property Collection $custom_fields
 * @property Collection $members
 * @property Collection $tags
 * @property Collection $times
 * @property float $orderindex
 * @property Folder $folder
 * @property int $team_id
 * @property int $time_estimate
 * @property int $time_spent
 * @property Member $creator
 * @property Priority $priority
 * @property Project $project
 * @property Space $space
 * @property Status $status
 * @property string $id
 * @property string $name
 * @property string $url
 * @property Task $parent
 * @property TaskList|null $list
 * @property Team|null $team
 * @property View $view
 */
class Task extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'archived' => 'boolean',
        'date_closed' => 'datetime:Uv',
        'date_created' => 'datetime:Uv',
        'date_updated' => 'datetime:Uv',
        'due_date' => 'datetime:Uv',
        'id' => 'string',
        'orderindex' => 'float',
        'start_date' => 'datetime:Uv',
        'team_id' => 'integer',
        'time_estimate' => 'integer',
        'time_spent' => 'integer',
    ];

    /**
     * Path to API endpoint.
     */
    protected string $path = '/task';

    /**
     * Has many Comments
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Accessor for Assignees.
     *
     * @throws NoClientException
     */
    public function getAssigneesAttribute(?array $assignees): Collection
    {
        return $this->givenMany(Member::class, $assignees);
    }

    /**
     * Accessor for Checklists.
     *
     * @throws NoClientException
     */
    public function getChecklistsAttribute(?array $checklists): Collection
    {
        return $this->givenMany(Checklist::class, $checklists);
    }

    /**
     * Accessor for Creator.
     *
     * @throws NoClientException
     */
    public function getCreatorAttribute(?array $creator): Member
    {
        return $this->givenOne(Member::class, $creator);
    }

    /**
     * Accessor for CustomFields.
     *
     * @throws NoClientException
     */
    public function getCustomFieldsAttribute(?array $custom_fields): Collection
    {
        return $this->givenMany(Field::class, $custom_fields);
    }

    /**
     * Accessor for Folder.
     *
     * @throws NoClientException
     */
    public function getFolderAttribute(?array $folder): Folder
    {
        return $this->givenOne(Folder::class, $folder);
    }

    /**
     * Accessor for Parent.
     *
     * @throws NoClientException
     */
    // TODO: Figure out how to make this relationship work
    /*public function getParentAttribute(?array $parent): Task
    {
        return $this->parentModel;
    }*/

    /**
     * Accessor for Priority.
     *
     * @throws NoClientException
     */
    public function getPriorityAttribute(?array $priority): Priority
    {
        return $this->givenOne(Priority::class, $priority);
    }

    /**
     * Accessor for Project.
     *
     * @throws NoClientException
     */
    public function getProjectAttribute(?array $project): Project
    {
        // TODO: This is not documented. I think it is a hold over from v1?
        return $this->givenOne(Project::class, $project);
    }

    /**
     * Accessor for Space.
     *
     * @throws NoClientException
     */
    public function getSpaceAttribute(?array $space): Space
    {
        // TODO: Look into making this a relationship
        return $this->givenOne(Space::class, $space);
    }

    /**
     * Accessor for Status.
     *
     * @throws NoClientException
     */
    public function getStatusAttribute(?array $status): Status
    {
        return $this->givenOne(Status::class, $status);
    }

    /**
     * Accessor for Tags.
     *
     * @throws NoClientException
     */
    public function getTagsAttribute(?array $tags): Collection
    {
        return $this->givenMany(Tag::class, $tags);
    }

    /**
     * Optional Child of TaskList
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function list(): ?ChildOf
    {
        return is_a($this->parentModel, TaskList::class) ? $this->childOf(TaskList::class) : null;
    }

    /**
     * Has many Members
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Optional Child of Team
     *
     * @return ChildOf
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function team(): ?ChildOf
    {
        return is_a($this->parentModel, Team::class) ? $this->childOf(Team::class) : null;
    }

    /**
     * Has many Times
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function times(): HasMany
    {
        return $this->hasMany(Time::class);
    }

    /**
     * Belongs to View
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function view(): BelongsTo
    {
        return $this->belongsTo(View::class);
    }
}
