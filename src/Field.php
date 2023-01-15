<?php

namespace Spinen\ClickUp;

use Carbon\Carbon;
use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class Field
 *
 *
 * @property array $type_config
 * @property bool $hide_from_guests
 * @property Carbon $date_created
 * @property string $id
 * @property string $name
 * @property string $type
 * @property TaskList $list
 */
class Field extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date_created' => 'datetime:Uv',
        'hide_from_guest' => 'boolean',
        'id' => 'string',
    ];

    /**
     * Is resource nested behind parentModel
     *
     * Several of the endpoints are nested behind another model for relationship, but then to
     * interact with the specific model, then are not nested.  This property will know when to
     * keep the specific model nested.
     *
     * @var bool
     */
    protected $nested = true;

    /**
     * Path to API endpoint.
     *
     * @var string
     */
    protected $path = '/field';

    /**
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function list(): ChildOf
    {
        return $this->childOf(TaskList::class);
    }
}
