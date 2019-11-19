<?php

namespace Spinen\ClickUp\Support\Relations;

use Spinen\ClickUp\Support\Model;

/**
 * Class ChildOf
 *
 * @package Spinen\ClickUp\Support\Relations
 */
class ChildOf extends BelongsTo
{
    /**
     * Get the results of the relationship.
     *
     * @return Model
     */
    public function getResults(): Model
    {
        // TODO: May need to deal with null relatedModel?
        return $this->getChild();
    }
}
