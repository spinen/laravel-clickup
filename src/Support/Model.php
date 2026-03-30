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
 */
abstract class Model implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasAttributes {
        asDateTime as originalAsDateTime;
    }
    use HasClient, HasTimestamps, HidesAttributes;

    /**
     * Indicates if the model exists.
     */
    public bool $exists = false;

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public bool $incrementing = false;

    /**
     * The "type" of the primary key ID.
     */
    protected string $keyType = 'int';

    /**
     * Is resource nested behind parentModel
     *
     * Several of the endpoints are nested behind another model for relationship, but then to
     * interact with the specific model, then are not nested.  This property will know when to
     * keep the specific model nested.
     */
    protected bool $nested = false;

    /**
     * Optional parentModel instance
     */
    public ?Model $parentModel;

    /**
     * Path to API endpoint.
     */
    protected string $path;

    /**
     * The primary key for the model.
     */
    protected string $primaryKey = 'id';

    /**
     * Is the model readonly?
     */
    protected bool $readonlyModel = false;

    /**
     * The loaded relationships for the model.
     */
    protected array $relations = [];

    /**
     * Some of the responses have the collections under a property
     */
    protected ?string $responseCollectionKey = null;

    /**
     * Some of the responses have the data under a property
     */
    protected ?string $responseKey = null;

    /**
     * Are timestamps in milliseconds?
     */
    protected bool $timestampsInMilliseconds = true;

    /**
     * Indicates if the model was inserted during the object's lifecycle.
     * 
     * This is here for Laravel 9+ compatibility, even though we never use this property.
     *
     * @var bool
     */
    public $wasRecentlyCreated = false;

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
     */
    public function __construct(?array $attributes = [], Model $parentModel = null)
    {
        // All dates from API comes as epoch with milliseconds
        $this->dateFormat = 'Uv';
        // None of these models will use timestamps, but need the date casting
        $this->timestamps = false;

        $this->syncOriginal();

        $this->fill($attributes);
        $this->parentModel = $parentModel;
    }

    /**
     * Dynamically retrieve attributes on the model.
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Determine if an attribute or relation exists on the model.
     */
    public function __isset(string $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @return void
     *
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
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @return Carbon
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
     * @param  string  $related
     */
    protected function assumeForeignKey($related): string
    {
        return Str::snake((new $related())->getResponseKey()).'_id';
    }

    /**
     * Relationship that makes the model belongs to another model
     *
     * @param  string  $related
     * @param  string|null  $foreignKey
     *
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
     * @param  string  $related
     * @param  string|null  $foreignKey
     *
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
     */
    public function fill(?array $attributes = []): self
    {
        foreach ((array) $attributes as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }

        return $this;
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     */
    public function getIncrementing(): bool
    {
        return $this->incrementing;
    }

    /**
     * Get the value of the model's primary key.
     */
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }

    /**
     * Get the primary key for the model.
     */
    public function getKeyName(): string
    {
        return $this->primaryKey;
    }

    /**
     * Get the auto-incrementing key type.
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
     * @param  string|null  $extra
     * @param  array|null  $query
     * @return string
     */
    public function getPath($extra = null, array $query = []): ?string
    {
        // Start with path to resource without "/" on end
        $path = rtrim($this->path, '/');

        // If have an id, then put it on the end
        if ($this->getKey()) {
            $path .= '/'.$this->getKey();
        }

        // Stick any extra things on the end
        if (! is_null($extra)) {
            $path .= '/'.ltrim($extra, '/');
        }

        // Convert query to querystring format and put on the end
        if (! empty($query)) {
            $path .= '?'.http_build_query($query);
        }

        // If there is a parentModel & not have an id (unless for nested), then prepend parentModel
        if (! is_null($this->parentModel) && (! $this->getKey() || $this->isNested())) {
            return $this->parentModel->getPath($path);
        }

        return $path;
    }

    /**
     * Get a relationship value from a method.
     *
     * @param  string  $method
     *
     * @throws LogicException
     */
    public function getRelationshipFromMethod($method)
    {
        $relation = $this->{$method}();

        if (! $relation instanceof Relation) {
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
     */
    public function getResponseCollectionKey(): ?string
    {
        return $this->responseCollectionKey ?? Str::plural($this->getResponseKey());
    }

    /**
     * Name of the wrapping key of response
     *
     * If none provided, assume camelCase of class name
     */
    public function getResponseKey(): ?string
    {
        return $this->responseKey ?? Str::camel(class_basename(static::class));
    }

    /**
     * Many of the results include collection of related data, so cast it
     *
     * @param  string  $related
     * @param  array  $given
     * @param  bool  $reset Some of the values are nested under a property, so peel it off
     *
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
     * @param  string  $related
     * @param  array  $attributes
     * @param  bool  $reset Some of the values are nested under a property, so peel it off
     *
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
     * @param  string  $related
     *
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
     */
    public function isNested(): bool
    {
        return $this->nested ?? false;
    }

    /**
     * Convert the object into something JSON serializable.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Create a new model instance that is existing.
     *
     * @param  array  $attributes
     * @return static
     */
    public function newFromBuilder($attributes = []): self
    {
        $model = $this->newInstance([], true);

        $model->setRawAttributes((array) $attributes, true);

        return $model;
    }

    /**
     * Create a new instance of the given model.
     *
     * Provides a convenient way for us to generate fresh model instances of this current model.
     * It is particularly useful during the hydration of new objects via the builder.
     *
     * @param  bool  $exists
     * @return static
     */
    public function newInstance(array $attributes = [], $exists = false): self
    {
        $model = (new static($attributes, $this->parentModel))->setClient($this->client);

        $model->exists = $exists;

        return $model;
    }

    /**
     * Determine if accessing missing attributes is disabled.
     *
     *
     * @return bool
     */
    public static function preventsAccessingMissingAttributes()
    {
        // NOTE: Needed for HasAttributes, just return false
        return false;
    }

    /**
     * Determine if the given attribute exists.
     */
    public function offsetExists($offset): bool
    {
        return ! is_null($this->getAttribute($offset));
    }

    /**
     * Get the value for a given offset.
     */
    public function offsetGet($offset): mixed
    {
        return $this->getAttribute($offset);
    }

    /**
     * Set the value for a given offset.
     *
     *
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
     */
    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset], $this->relations[$offset]);
    }

    /**
     * Determine if the given relation is loaded.
     *
     * @param  string  $key
     */
    public function relationLoaded($key): bool
    {
        return array_key_exists($key, $this->relations);
    }

    /**
     * Laravel allows the resolver to be set at runtime, so we just return null
     *
     * @param  string  $class
     * @param  string  $key
     * @return null
     */
    public function relationResolver($class, $key)
    {
        return null;
    }

    /**
     * Save the model in ClickUp
     *
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
            if (! $this->isDirty()) {
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
     * @throws NoClientException
     * @throws TokenException
     * @throws UnableToSaveException
     */
    public function saveOrFail(): bool
    {
        if (! $this->save()) {
            throw new UnableToSaveException();
        }

        return true;
    }

    /**
     * Set the readonly
     *
     * @param  bool  $readonly
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
     * @param  string  $relation
     * @return $this
     */
    public function setRelation($relation, $value): self
    {
        $this->relations[$relation] = $value;

        return $this;
    }

    /**
     * Convert the model instance to an array.
     */
    public function toArray(): array
    {
        return array_merge($this->attributesToArray(), $this->relationsToArray());
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param  int  $options
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
