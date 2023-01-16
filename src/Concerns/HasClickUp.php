<?php

namespace Spinen\ClickUp\Concerns;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Encryption\Encrypter;
use Spinen\ClickUp\Api\Client as ClickUp;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Builder;

/**
 * Trait HasClickUp
 *
 * @property ClickUp $clickup
 * @property string $clickup_token
 */
trait HasClickUp
{
    /**
     * ClickUp Builder instance
     */
    protected ?Builder $builder = null;

    /**
     * Return cached version of the ClickUp Builder for the user
     *
     * @throws BindingResolutionException
     */
    public function clickup(): Builder
    {
        if (is_null($this->builder)) {
            $this->builder = Container::getInstance()
                                      ->make(Builder::class)
                                      ->setClient(
                                          Container::getInstance()
                                                   ->make(ClickUp::class)
                                                   ->setToken($this->clickup_token)
                                      );
        }

        return $this->builder;
    }

    /**
     * Accessor for ClickUp Client.
     *
     * @throws BindingResolutionException
     * @throws NoClientException
     */
    public function getClickupAttribute(): ClickUp
    {
        return $this->clickup()
                    ->getClient();
    }

    /**
     * Accessor for ClickUpToken.
     *
     * @throws BindingResolutionException
     */
    public function getClickupTokenAttribute(): ?string
    {
        if (! is_null($this->attributes['clickup_token'])) {
            return $this->resolveEncrypter()
                        ->decrypt($this->attributes['clickup_token']);
        }

        return null;
    }

    /**
     * Make sure that the clickup_token is fillable & protected
     */
    public function initializeHasClickUp(): void
    {
        $this->fillable[] = 'clickup_token';
        $this->hidden[] = 'clickup';
        $this->hidden[] = 'clickup_token';
    }

    /**
     * Resolve the encrypter from the IoC
     *
     * We are staying away from the Crypt facade, so that we can support PHP 7.4 with Laravel 5.x
     *
     * @throws BindingResolutionException
     */
    protected function resolveEncrypter(): Encrypter
    {
        return Container::getInstance()
                        ->make(Encrypter::class);
    }

    /**
     * Mutator for ClickUpToken.
     *
     * @throws BindingResolutionException
     */
    public function setClickupTokenAttribute(?string $clickup_token): void
    {
        // If setting the password & already have a client, then empty the client to use new password in client
        if (! is_null($this->builder)) {
            $this->builder = null;
        }

        $this->attributes['clickup_token'] = is_null($clickup_token)
            ? null
            : $this->resolveEncrypter()
                   ->encrypt($clickup_token);
    }
}
