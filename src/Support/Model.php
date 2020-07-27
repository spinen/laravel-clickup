<?php

namespace Spinen\ClickUp\Support;

use ArrayAccess;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HidesAttributes;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use JsonSerializable;
use LogicException;
use Spinen\ClickUp\Concerns\HasClient;
use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\ModelReadonlyException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Exceptions\TokenException;
use Spinen\ClickUp\Exceptions\UnableToSaveException;
use Spinen\ClickUp\Support\Relations\BelongsTo;
use Spinen\ClickUp\Support\Relations\ChildOf;
use Spinen\ClickUp\Support\Relations\HasMany;
use Spinen\ClickUp\Support\Relations\Relation;

/**
 * Class Model
 *
 * @package Spinen\ClickUp\Support
 */
abstract class Model implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasAttributes {
        asDateTime as originalAsDateTime;
    }
    use HasClient, HasTimestamps, HidesAttributes;

    /**
     * Indicates if the model exists.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Is resource nested behind parentModel
     *
     * Several of the endpoints are nested behind another model for relationship, but then to
     * interact with the specific model, then are not nested.  This property will know when to
     * keep the specific model nested.
     *
     * @var bool
     */
    protected $nested = false;

    /**
     * Optional parentModel instance
     *
     * @var Model $parentModel
     */
    public $parentModel;

    /**
     * Path to API endpoint.
     *
     * @var string
     */
    protected $path = null;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Is the model readonly?
     *
     * @var bool
     */
    protected $readonlyModel = false;

    /**
     * The loaded relationships for the model.
     *
     * @var array
     */
    protected $relations = [];

    /**
     * Some of the responses have the collections under a property
     *
     * @var string|null
     */
    protected $responseCollectionKey = null;

    /**
     * Some of the responses have the data under a property
     *
     * @var string|null
     */
    protected $responseKey = null;

    /**
     * Are timestamps in milliseconds?
     *
     * @var boolean
     */
    protected $timestampsInMilliseconds = true;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Model constructor.
     *
     * @param array|null $attributes
     * @param Model|null $parentModel
     */
    public function __construct(array $attributes = [], Model $parentModel = null)
    {
        // All dates from API comes as epoch with milliseconds
        $this->dateFormat = 'Uv';
        // None of this  models will use timestamps, but need the date casting
        $this->timestamps = false;

        $this->syncOriginal();

        $this->fill($attributes);
        $this->parentModel = $parentModel;
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     * @throws ModelReadonlyException
     */
    public function __set($key, $value)
    {
        if ($this->readonlyModel) {
            throw new ModelReadonlyException();
        }

        $this->setAttribute($key, $value);
    }

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Unset an attribute on the model.
     *
     * @param string $key
     *
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value
     * @return \Illuminate\Support\Carbon
     */
    protected function asDateTime($value)
    {
        if (is_numeric($value) && $this->timestampsInMilliseconds) {
            return Date::createFromTimestampMs($value);
        }

        return $this->originalAsDateTime($value);
    }

    /**
     * Assume foreign key
     *
     * @param string $related
     *
     * @return string
     */
    protected function assumeForeignKey($related): string
    {
        return Str::snake((new $related())->getResponseKey()) . '_id';
    }

    /**
     * Relationship that makes the model belongs to another model
     *
     * @param string $related
     * @param string|null $foreignKey
     *
     * @return BelongsTo
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function belongsTo($related, $foreignKey = null): BelongsTo
    {
        $foreignKey = $foreignKey ?? $this->assumeForeignKey($related);

        $builder = (new Builder())->setClass($related)
                                  ->setClient($this->getClient());

        return new BelongsTo($builder, $this, $foreignKey);
    }

    /**
     * Relationship that makes the model child to another model
     *
     * @param string $related
     * @param string|null $foreignKey
     *
     * @return ChildOf
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function childOf($related, $foreignKey = null): ChildOf
    {
        $foreignKey = $foreignKey ?? $this->assumeForeignKey($related);

        $builder = (new Builder())->setClass($related)
                                  ->setClient($this->getClient())
                                  ->setParent($this);

        return new ChildOf($builder, $this, $foreignKey);
    }

    /**
     * Delete the model from ClickUp
     *
     * @return boolean
     * @throws NoClientException
     * @throws TokenException
     */
    public function delete(): bool
    {
        // TODO: Make sure that the model supports being deleted
        if ($this->readonlyModel) {
            return false;
        }

        try {
            $this->getClient()
                 ->delete($this->getPath());

            return true;
        } catch (GuzzleException $e) {
            // TODO: Do something with the error

            return false;
        }
    }

    /**
     * Fill the model with the supplied properties
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function fill(array $attributes = []): self
    {
        foreach ($attributes as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }

        return $this;
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing(): bool
    {
        return $this->incrementing;
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName(): string
    {
        return $this->primaryKey;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType(): string
    {
        return $this->keyType;
    }

    /**
     * Build API path
     *
     * Put anything on the end of the URI that is passed in
     *
     * @param string|null $extra
     * @param array|null $query
     *
     * @return string
     */
    public function getPath($extra = null, array $query = []): ?string
    {
        // Start with path to resource without "/" on end
        $path = rtrim($this->path, '/');

        // If have an id, then put it on the end
        if ($this->getKey()) {
            $path .= '/' . $this->getKey();
        }

        // Stick any extra things on the end
        if (!is_null($extra)) {
            $path .= '/' . ltrim($extra, '/');
        }

        // Convert query to querystring format and put on the end
        if (!empty($query)) {
            $path .= '?' . http_build_query($query);
        }

        // If there is a parentModel & not have an id (unless for nested), then prepend parentModel
        if (!is_null($this->parentModel) && (!$this->getKey() || $this->isNested())) {
            return $this->parentModel->getPath($path);
        }

        return $path;
    }

    /**
     * Get a relationship value from a method.
     *
     * @param string $method
     *
     * @return mixed
     *
     * @throws LogicException
     */
    public function getRelationshipFromMethod($method)
    {
        $relation = $this->{$method}();

        if (!$relation instanceof Relation) {
            $exception_message = is_null($relation)
                ? '%s::%s must return a relationship instance, but "null" was returned. Was the "return" keyword used?'
                : '%s::%s must return a relationship instance.';

            throw new LogicException(
                sprintf($exception_message, static::class, $method)
            );
        }

        return tap(
            $relation->getResults(),
            function ($results) use ($method) {
                $this->setRelation($method, $results);
            }
        );
    }

    /**
     * Name of the wrapping key when response is a collection
     *
     * If none provided, assume plural version responseKey
     *
     * @return string|null
     */
    public function getResponseCollectionKey(): ?string
    {
        return $this->responseCollectionKey ?? Str::plural($this->getResponseKey());
    }

    /**
     * Name of the wrapping key of response
     *
     * If none provided, assume camelCase of class name
     *
     * @return string|null
     */
    public function getResponseKey(): ?string
    {
        return $this->responseKey ?? Str::camel(class_basename(static::class));
    }

    /**
     * Many of the results include collection of related data, so cast it
     *
     * @param string $related
     * @param array $given
     * @param bool $reset Some of the values are nested under a property, so peel it off
     *
     * @return Collection
     * @throws NoClientException
     */
    public function givenMany($related, $given, $reset = false): Collection
    {
        /** @var Model $model */
        $model = (new $related([], $this->parentModel))->setClient($this->getClient());

        return (new Collection($given))->map(
            function ($attributes) use ($model, $reset) {
                return $model->newFromBuilder($reset ? reset($attributes) : $attributes);
            }
        );
    }

    /**
     * Many of the results include related data, so cast it to object
     *
     * @param string $related
     * @param array $attributes
     * @param bool $reset Some of the values are nested under a property, so peel it off
     *
     * @return Model
     * @throws NoClientException
     */
    public function givenOne($related, $attributes, $reset = false): Model
    {
        return (new $related([], $this->parentModel))->setClient($this->getClient())
                                                     ->newFromBuilder($reset ? reset($attributes) : $attributes);
    }

    /**
     * Relationship that makes the model have a collection of another model
     *
     * @param string $related
     *
     * @return HasMany
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function hasMany($related): HasMany
    {
        $builder = (new Builder())->setClass($related)
                                  ->setClient($this->getClient())
                                  ->setParent($this);

        return new HasMany($builder, $this);
    }

    /**
     * Is endpoint nested behind another endpoint
     *
     * @return bool
     */
    public function isNested(): bool
    {
        return $this->nested ?? false;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Create a new model instance that is existing.
     *
     * @param array $attributes
     *
     * @return static
     */
    public function newFromBuilder($attributes = []): self
    {
        $model = $this->newInstance([], true);

        $model->setRawAttributes((array)$attributes, true);

        return $model;
    }

    /**
     * Create a new instance of the given model.
     *
     * Provides a convenient way for us to generate fresh model instances of this current model.
     * It is particularly useful during the hydration of new objects via the builder.
     *
     * @param array $attributes
     * @param bool $exists
     *
     * @return static
     */
    public function newInstance(array $attributes = [], $exists = false): self
    {
        $model = (new static($attributes, $this->parentModel))->setClient($this->client);

        $model->exists = $exists;

        return $model;
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return !is_null($this->getAttribute($offset));
    }

    /**
     * Get the value for a given offset.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * Set the value for a given offset.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     * @throws ModelReadonlyException
     */
    public function offsetSet($offset, $value): void
    {
        if ($this->readonlyModel) {
            throw new ModelReadonlyException();
        }

        $this->setAttribute($offset, $value);
    }

    /**
     * Unset the value for a given offset.
     *
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset], $this->relations[$offset]);
    }

    /**
     * Determine if the given relation is loaded.
     *
     * @param string $key
     *
     * @return bool
     */
    public function relationLoaded($key): bool
    {
        return array_key_exists($key, $this->relations);
    }

    /**
     * Save the model in ClickUp
     *
     * @return bool
     * @throws NoClientException
     * @throws TokenException
     */
    public function save(): bool
    {
        // TODO: Make sure that the model supports being saved
        if ($this->readonlyModel) {
            return false;
        }

        try {
            if (!$this->isDirty()) {
                return true;
            }

            if ($this->exists) {
                // TODO: If we get null from the PUT, throw/handle exception
                $response = $this->getClient()
                                 ->put($this->getPath(), $this->getDirty());

                // Record the changes
                $this->syncChanges();

                // Reset the model with the results as we get back the full model
                $this->setRawAttributes($response, true);

                return true;
            }

            $response = $this->getClient()
                             ->post($this->getPath(), $this->toArray());

            $this->exists = true;

            // Reset the model with the results as we get back the full model
            $this->setRawAttributes($response, true);

            return true;

        } catch (GuzzleException $e) {
            // TODO: Do something with the error

            return false;
        }
    }

    /**
     * Save the model in ClickUp, but raise error if fail
     *
     * @return bool
     * @throws NoClientException
     * @throws TokenException
     * @throws UnableToSaveException
     */
    public function saveOrFail(): bool
    {
        if (!$this->save()) {
            throw new UnableToSaveException();
        }

        return true;
    }


    /**
     * Set the readonly
     *
     * @param bool $readonly
     *
     * @return $this
     */
    public function setReadonly($readonly = true): self
    {
        $this->readonlyModel = $readonly;

        return $this;
    }

    /**
     * Set the given relationship on the model.
     *
     * @param string $relation
     * @param mixed $value
     *
     * @return $this
     */
    public function setRelation($relation, $value): self
    {
        $this->relations[$relation] = $value;

        return $this;
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge($this->attributesToArray(), $this->relationsToArray());
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param int $options
     *
     * @return string
     *
     * @throws JsonEncodingException
     */
    public function toJson($options = 0): string
    {
        $json = json_encode($this->jsonSerialize(), $options);

        // @codeCoverageIgnoreStart
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw JsonEncodingException::forModel($this, json_last_error_msg());
        }
        // @codeCoverageIgnoreEnd

        return $json;
    }
}
