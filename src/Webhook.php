<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\Support\Relations\BelongsTo;
use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class Webhook
 *
 * @package Spinen\ClickUp
 *
 * @property array $events
 * @property integer $folder_id
 * @property integer $list_id
 * @property integer $space_id
 * @property integer $team_id
 * @property integer $userid
 * @property string $id
 */
class Webhook extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'folder_id' => 'integer',
        'id'        => 'string',
        'list_id'   => 'integer',
        'space_id'  => 'integer',
        'team_id'   => 'integer',
        'userid'    => 'integer',
    ];

    /**
     * Path to API endpoint.
     *
     * @var string
     */
    protected $path = '/webhook';

    /**
     * @return BelongsTo

     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * @return BelongsTo
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(TaskList::class);
    }

    /**
     * @return BelongsTo
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
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
     * @return BelongsTo
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'userid');
    }
}
