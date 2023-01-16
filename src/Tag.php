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
 * @property Space $space
 * @property string $name
 * @property string $tag_bg
 * @property string $tag_fg
 */
class Tag extends Model
{
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
    protected string $path = '/tag';

    /**
     * Child of Space
     *
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function space(): ChildOf
    {
        return $this->childOf(Space::class);
    }
}
