<?php

namespace Spinen\ClickUp;

use Carbon\Carbon;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Model;

/**
 * Class Item
 *
 * @property bool $resolved
 * @property bool $unresolved
 * @property Carbon $date_created
 * @property Collection $children
 * @property float $orderindex
 * @property Member $assignee
 * @property string $id
 * @property string $name
 * @property string $parent // TODO: Swap to Item when relationship addessed
 */
class Item extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date_created' => 'datetime:Uv',
        'id' => 'string',
        'orderindex' => 'float',
        'resolved' => 'boolean',
        'unresolved' => 'boolean',
    ];

    /**
     * Is resource nested behind parentModel
     *
     * Several of the endpoints are nested behind another model for relationship, but then to
     * interact with the specific model, then are not nested.  This property will know when to
     * keep the specific model nested.
     */
    protected bool $nested = true;

    /**
     * Path to API endpoint.
     */
    protected string $path = '/checklist_item';

    /**
     * Accessor for Assignee.
     *
     * @throws NoClientException
     */
    public function getAssigneeAttribute(?array $assignee): Member
    {
        return $this->givenOne(Member::class, $assignee);
    }

    /**
     * Accessor for Children.
     *
     * @throws NoClientException
     */
    public function getChildrenAttribute(?array $children): Collection
    {
        return $this->givenMany(Item::class, $children);
    }

    /**
     * Accessor for Parent.
     */
    // TODO: Figure out how to make this relationship work
    /*public function getParentAttribute(string $parent): Item
    {
        return $this->parentModel;
    }*/
}
