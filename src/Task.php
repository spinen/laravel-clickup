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
 * @property TaskList $list
 * @property Team $team
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
     *
     * @var string
     */
    protected $path = '/task';

    /**
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
     *
     * @throws NoClientException
     */
    public function getAssigneesAttribute(array $assignees): Collection
    {
        return $this->givenMany(Member::class, $assignees);
    }

    /**
     * Accessor for Checklists.
     *
     *
     * @throws NoClientException
     */
    public function getChecklistsAttribute(array $checklists): Collection
    {
        return $this->givenMany(Checklist::class, $checklists);
    }

    /**
     * Accessor for Creator.
     *
     * @param  array  $creator
     *
     * @throws NoClientException
     */
    public function getCreatorAttribute($creator): Member
    {
        return $this->givenOne(Member::class, $creator);
    }

    /**
     * Accessor for CustomFields.
     *
     *
     * @throws NoClientException
     */
    public function getCustomFieldsAttribute(array $custom_fields): Collection
    {
        return $this->givenMany(Field::class, $custom_fields);
    }

    /**
     * Accessor for Folder.
     *
     * @param  array  $folder
     *
     * @throws NoClientException
     */
    public function getFolderAttribute($folder): Folder
    {
        return $this->givenOne(Folder::class, $folder);
    }

    /**
     * Accessor for Parent.
     *
     *
     * @return Task
     */
    // TODO: Figure out how to make this relationship work
    /*public function getParentAttribute($parent): Task
    {
        return $this->parentModel;
    }*/

    /**
     * Accessor for Priority.
     *
     * @param  array  $priority
     *
     * @throws NoClientException
     */
    public function getPriorityAttribute($priority): Priority
    {
        return $this->givenOne(Priority::class, $priority);
    }

    /**
     * Accessor for Project.
     *
     * @param  array  $project
     *
     * @throws NoClientException
     */
    public function getProjectAttribute($project): Project
    {
        // TODO: This is not documented. I think it is a hold over from v1?
        return $this->givenOne(Project::class, $project);
    }

    /**
     * Accessor for Space.
     *
     * @param  array  $space
     *
     * @throws NoClientException
     */
    public function getSpaceAttribute($space): Space
    {
        // TODO: Look into making this a relationship
        return $this->givenOne(Space::class, $space);
    }

    /**
     * Accessor for Status.
     *
     * @param  array  $status
     *
     * @throws NoClientException
     */
    public function getStatusAttribute($status): Status
    {
        return $this->givenOne(Status::class, $status);
    }

    /**
     * Accessor for Tags.
     *
     *
     * @throws NoClientException
     */
    public function getTagsAttribute(array $tags): Collection
    {
        return $this->givenMany(Tag::class, $tags);
    }

    /**
     * @return ChildOf
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
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
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
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function times(): HasMany
    {
        return $this->hasMany(Time::class);
    }

    /**
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function view(): BelongsTo
    {
        return $this->belongsTo(View::class);
    }
}
