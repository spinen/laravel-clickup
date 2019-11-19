<?php

namespace Spinen\ClickUp\Support;

use BadMethodCallException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as LaravelCollection;
use Spinen\ClickUp\Concerns\HasClient;
use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Exceptions\TokenException;
use Spinen\ClickUp\Space;
use Spinen\ClickUp\Task;
use Spinen\ClickUp\Team;
use Spinen\ClickUp\User;

/**
 * Class Builder
 *
 * @package Spinen\ClickUp\Support
 *
 * @property Collection $spaces
 * @property Collection $tasks
 * @property Collection $teams
 * @property Collection $workspaces
 * @property User $user
 *
 * @method spaces
 * @method tasks
 * @method teams
 * @method workspaces
 */
class Builder
{
    use HasClient;

    /**
     * Class to cast the response
     *
     * @var string
     */
    protected $class;

    /**
     * Model instance
     *
     * @var Model
     */
    protected $model;

    /**
     * Parent model instance
     *
     * @var Model
     */
    protected $parentModel;

    /**
     * Map of potential parents with class name
     *
     * @var array
     */
    protected $rootModels = [
        'spaces'     => Space::class,
        'tasks'      => Task::class,
        'teams'      => Team::class,
        'workspaces' => Team::class,
    ];

    /**
     * Properties to filter the response
     *
     * @var array
     */
    protected $wheres = [];

    /**
     * Magic method to make builders for root models
     *
     * @param string $name
     * @param $arguments
     *
     * @return mixed
     * @throws BadMethodCallException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function __call($name, $arguments)
    {
        if (!$this->parentModel && array_key_exists($name, $this->rootModels)) {
            return $this->newInstanceForModel($this->rootModels[$name]);
        }

        throw new BadMethodCallException(sprintf("Call to undefined method [%s]", $name));
    }

    /**
     * Magic method to make builders appears as properties
     *
     * @param string $name
     *
     * @return Collection|Model|null
     * @throws GuzzleException
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     * @throws TokenException
     */
    public function __get($name)
    {
        if ($name === 'user') {
            return $this->newInstanceForModel(User::class)
                        ->get()
                        ->first();
        }

        // Only return builders as properties, when not a child
        if (!$this->parentModel && array_key_exists($name, $this->rootModels)) {
            return $this->{$name}()
                        ->get();
        }

        return null;
    }

    /**
     * Create instance of class and save via API
     *
     * @param array $attributes
     *
     * @return Model
     * @throws InvalidRelationshipException
     */
    public function create(array $attributes): Model
    {
        return tap(
            $this->make($attributes),
            function (Model $model) {
                $model->save();
            }
        );
    }

    /**
     * Get Collection of class instances that match query
     *
     * @param array|string $properties to pull
     *
     * @return Collection|Model
     * @throws GuzzleException
     * @throws InvalidRelationshipException
     * @throws NoClientException
     * @throws TokenException
     */
    public function get($properties = ['*'])
    {
        $properties = Arr::wrap($properties);

        // Call API to get the response
        $response = $this->getClient()
                         ->request($this->getPath());

        // Peel off the key if exist
        $response = $this->peelWrapperPropertyIfNeeded(Arr::wrap($response));

        // Convert to a collection of filtered objects casted to the class
        return (new Collection((array_values($response) === $response) ? $response : [$response]))->map(
            function ($items) use ($properties) {
                    // Cast to class with only the requested, properties
                    return $this->getModel()
                                ->newFromBuilder(
                                    $properties === ['*']
                                        ? (array)$items
                                        : collect($items)
                                            ->only($properties)
                                            ->toArray()
                                )
                                ->setClient($this->getClient());
            }
        );
    }

    /**
     * Get the model instance being queried.
     *
     * @return Model
     * @throws InvalidRelationshipException
     */
    public function getModel(): Model
    {
        if (!$this->class) {
            throw new InvalidRelationshipException();
        }

        if (!$this->model) {
            $this->model = (new $this->class([], $this->parentModel))->setClient($this->client);
        }

        return $this->model;
    }


    /**
     * Get the path for the resource with the where filters
     *
     * @param string|null $extra
     *
     * @return string|null
     * @throws InvalidRelationshipException
     */
    public function getPath($extra = null): ?string
    {
        return $this->getModel()
                    ->getPath($extra, $this->wheres);
    }

    /**
     * Find specific instance of class
     *
     * @param integer|string $id
     * @param array|string $properties to pull
     *
     * @return Model
     * @throws GuzzleException
     * @throws InvalidRelationshipException
     * @throws NoClientException
     * @throws TokenException
     */
    public function find($id, $properties = ['*']): Model
    {
        return $this->where($this->getModel()->getKeyName(), $id)
                    ->get($properties)
                    ->first();
    }

    /**
     * New up a class instance, but not saved
     *
     * @param array|null $attributes
     *
     * @return Model
     * @throws InvalidRelationshipException
     */
    public function make(array $attributes = []): Model
    {
        // TODO: Make sure that the model supports "creating"
        return $this->getModel()
                    ->newInstance($attributes);
    }

    /**
     * Create new Builder instance
     *
     * @return $this
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function newInstance(): self
    {
        return (new static())->setClass($this->class)
                             ->setClient($this->getClient())
                             ->setParent($this->parentModel);
    }

    /**
     * Create new Builder instance for a specific model
     *
     * @param string $model
     *
     * @return $this
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function newInstanceForModel($model): self
    {
        return $this->newInstance()
                    ->setClass($model);
    }

    /**
     * Peel of the wrapping property if it exist.
     *
     * @param array $properties
     *
     * @return array
     * @throws InvalidRelationshipException
     */
    protected function peelWrapperPropertyIfNeeded(array $properties): array
    {
        // Check for single response
        if (array_key_exists(
            $this->getModel()
                 ->getResponseKey(),
            $properties
        )) {
            return $properties[$this->getModel()
                                    ->getResponseKey()];
        }

        // Check for collection of responses
        if (array_key_exists(
            $this->getModel()
                 ->getResponseCollectionKey(),
            $properties
        )) {
            return $properties[$this->getModel()
                                    ->getResponseCollectionKey()];
        }

        return $properties;
    }

    /**
     * Set the class to cast the response
     *
     * @param string $class
     *
     * @return $this
     * @throws ModelNotFoundException
     */
    public function setClass($class): self
    {
        $this->class = $class;

        if (!is_null($class) && !class_exists($this->class)) {
            throw new ModelNotFoundException(sprintf("The model [%s] not found.", $this->class));
        }

        return $this;
    }

    /**
     * Set the parent model
     *
     * @param Model $parent
     *
     * @return $this
     */
    public function setParent(?Model $parent): self
    {
        $this->parentModel = $parent;

        return $this;
    }

    /**
     * Add property to filter the collection
     *
     * @param string $property
     * @param mixed $value
     *
     * @return $this
     * @throws InvalidRelationshipException
     */
    public function where($property, $value = true): self
    {
        $value = is_a($value, LaravelCollection::class) ? $value->toArray() : $value;

        // If looking for a specific model, then set the id
        if ($property === $this->getModel()->getKeyName()) {
            $this->getModel()->{$property} = $value;

            return $this;
        }

        $this->wheres[$property] = $value;

        return $this;
    }

    /**
     * Shortcut to where property id
     *
     * @param integer|string $id
     *
     * @return $this
     * @throws InvalidRelationshipException
     */
    public function whereId($id): self
    {
        return $this->where($this->getModel()->getKeyName(), $id);
    }

    /**
     * Shortcut to where property is false
     *
     * @param string $property
     *
     * @return $this
     * @throws InvalidRelationshipException
     */
    public function whereNot($property): self
    {
        return $this->where($property, false);
    }
}
