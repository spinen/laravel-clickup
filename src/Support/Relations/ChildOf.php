<?php

namespace Spinen\ClickUp\Support\Relations;

use Spinen\ClickUp\Support\Model;

/**
 * Class ChildOf
 */
class ChildOf extends BelongsTo
{
    /**
     * Get the results of the relationship.
     */
    public function getResults(): Model
    {
        // TODO: May need to deal with null relatedModel?
        return $this->getChild();
    }
}
