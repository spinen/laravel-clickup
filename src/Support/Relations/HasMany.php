<?php

namespace Spinen\ClickUp\Support\Relations;

use GuzzleHttp\Exception\GuzzleException;
use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Exceptions\TokenException;
use Spinen\ClickUp\Support\Collection;

/**
 * Class HasMany
 *
 * @package Spinen\ClickUp\Support\Relations
 */
class HasMany extends Relation
{
    /**
     * Get the results of the relationship.
     *
     * @return Collection
     * @throws GuzzleException
     * @throws InvalidRelationshipException
     * @throws NoClientException
     * @throws TokenException
     */
    public function getResults(): Collection
    {
        return $this->getBuilder()
                    ->get();
    }
}
