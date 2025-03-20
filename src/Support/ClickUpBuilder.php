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
 * Class ClickUpBuilder
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
class ClickUpBuilder
{
    use HasClient;

    /**
     * Class to cast the response
     */
    protected string $class;

    /**
     * Model instance
     */
    protected Model $model;

    /**
     * Parent model instance
     */
    protected ?Model $parentModel = null;

    /**
     * Map of potential parents with class name
     *
     * @var array
     */
    protected $rootModels = [
        'spaces' => Space::class,
        'tasks' => Task::class,
        'teams' => Team::class,
        'workspaces' => Team::class,
    ];

    /**
     * Properties to filter the response
     */
    protected array $wheres = [];

    /**
     * Magic method to make builders for root models
     *
     * @throws BadMethodCallException
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function __call(string $name, array $arguments)
    {
        if (! isset($this->parentModel) && array_key_exists($name, $this->rootModels)) {
            return $this->newInstanceForModel($this->rootModels[$name]);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method [%s]', $name));
    }

    /**
     * Magic method to make builders appears as properties
     *
     * @throws GuzzleException
     * @throws InvalidRelationshipException
     * @throws ModelNotFoundException
     * @throws NoClientException
     * @throws TokenException
     */
    public function __get(string $name): Collection|Model|null
    {
        if ($name === 'user') {
            return $this->newInstanceForModel(User::class)
                ->get()
                ->first();
        }

        // Only return builders as properties, when not a child
        if (! $this->parentModel && array_key_exists($name, $this->rootModels)) {
            return $this->{$name}()
                ->get();
        }

        return null;
    }

    /**
     * Create instance of class and save via API
     *
     * @throws InvalidRelationshipException
     */
    public function create(array $attributes): Model
    {
        return tap(
            $this->make($attributes),
            fn (Model $model): bool => $model->save()
        );
    }

    /**
     * Get Collection of class instances that match query
     *
     * @throws GuzzleException
     * @throws InvalidRelationshipException
     * @throws NoClientException
     * @throws TokenException
     */
    public function get(array|string $properties = ['*']): Collection|Model
    {
        $properties = Arr::wrap($properties);

        // Call API to get the response
        $response = $this->getClient()
            ->request($this->getPath());

        // Peel off the key if exist
        $response = $this->peelWrapperPropertyIfNeeded(Arr::wrap($response));

        // Convert to a collection of filtered objects casted to the class
        return (new Collection((array_values($response) === $response) ? $response : [$response]))->map(
            // Cast to class with only the requested, properties
            fn ($items) => $this->getModel()
                ->newFromBuilder(
                    $properties === ['*']
                        ? (array) $items
                        : collect($items)
                            ->only($properties)
                            ->toArray()
                )
                ->setClient($this->getClient())
        );
    }

    /**
     * Get the model instance being queried.
     *
     * @throws InvalidRelationshipException
     */
    public function getModel(): Model
    {
        if (! isset($this->class)) {
            throw new InvalidRelationshipException();
        }

        if (! isset($this->model)) {
            $this->model = (new $this->class([], $this->parentModel))->setClient($this->client);
        }

        return $this->model;
    }

    /**
     * Get the path for the resource with the where filters
     *
     * @throws InvalidRelationshipException
     */
    public function getPath(?string $extra = null): ?string
    {
        return $this->getModel()
            ->getPath($extra, $this->wheres);
    }

    /**
     * Find specific instance of class
     *
     * @throws GuzzleException
     * @throws InvalidRelationshipException
     * @throws NoClientException
     * @throws TokenException
     */
    public function find(int|string $id, array|string $properties = ['*']): Model
    {
        return $this->where($this->getModel()->getKeyName(), $id)
            ->get($properties)
            ->first();
    }

    /**
     * New up a class instance, but not saved
     *
     * @throws InvalidRelationshipException
     */
    public function make(?array $attributes = []): Model
    {
        // TODO: Make sure that the model supports "creating"
        return $this->getModel()
            ->newInstance($attributes);
    }

    /**
     * Create new Builder instance
     *
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function newInstance(): self
    {
        return isset($this->class)
            ? (new static())
                ->setClass($this->class)
                ->setClient($this->getClient())
                ->setParent($this->parentModel)
            : (new static())
                ->setClient($this->getClient())
                ->setParent($this->parentModel);
    }

    /**
     * Create new Builder instance for a specific model
     *
     * @throws ModelNotFoundException
     * @throws NoClientException
     */
    public function newInstanceForModel(string $model): self
    {
        return $this->newInstance()
            ->setClass($model);
    }

    /**
     * Peel of the wrapping property if it exist.
     *
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
     * @throws ModelNotFoundException
     */
    public function setClass(string $class): self
    {
        if (! class_exists($class)) {
            throw new ModelNotFoundException(sprintf('The model [%s] not found.', $class));
        }

        $this->class = $class;

        return $this;
    }

    /**
     * Set the parent model
     */
    public function setParent(?Model $parent): self
    {
        $this->parentModel = $parent;

        return $this;
    }

    /**
     * Add property to filter the collection
     *
     * @throws InvalidRelationshipException
     */
    public function where(string $property, $value = true): self
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
     * @throws InvalidRelationshipException
     */
    public function whereId(int|string|null $id): self
    {
        return $this->where($this->getModel()->getKeyName(), $id);
    }

    /**
     * Shortcut to where property is false
     *
     * @throws InvalidRelationshipException
     */
    public function whereNot(string $property): self
    {
        return $this->where($property, false);
    }
}
