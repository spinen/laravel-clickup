<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class Tag
 *
 *
 * @property string $name
 * @property string $tag_fg
 * @property string $tag_bg
 */
class Tag extends Model
{
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
    protected $path = '/tag';

    /**
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function space(): ChildOf
    {
        return $this->childOf(Space::class);
    }
}
