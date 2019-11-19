<?php

namespace Spinen\ClickUp\Support\Stubs;

use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Model as BaseModel;
use Spinen\ClickUp\Support\Relations\HasMany;
use stdClass;

class Model extends BaseModel
{
    /**
     * Path to API endpoint.
     *
     * @var string
     */
    protected $path = 'some/path';

    /**
     * Mutator for Mutator.
     *
     * @param  $value
     */
    public function setMutatorAttribute($value)
    {
        $this->attributes['mutator'] = 'mutated: ' . $value;
    }

    public function setResponseCollectionKey($key)
    {
        $this->responseCollectionKey = $key;
    }

    public function setResponseKey($key)
    {
        $this->responseKey = $key;
    }

    /**
     * Allow swapping nested for test
     *
     * @param $nested
     *
     * @return Model
     */
    public function setNested($nested)
    {
        $this->nested = $nested;

        return $this;
    }

    /**
     * @return HasMany
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function related(): HasMany
    {
        return $this->hasMany(Model::class);
    }

    public function nonrealation()
    {
        return new stdClass();
    }

    public function nullrealation()
    {
        return null;
    }
}
