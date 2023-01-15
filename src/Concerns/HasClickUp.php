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
 *
 * @property ClickUp $clickup
 * @property string $clickup_token
 */
trait HasClickUp
{
    /**
     * ClickUp Builder instance
     *
     * @var Builder
     */
    protected $builder = null;

    /**
     * Return cached version of the ClickUp Builder for the user
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function clickup()
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
     * @return ClickUp
     *
     * @throws BindingResolutionException
     * @throws NoClientException
     */
    public function getClickupAttribute()
    {
        return $this->clickup()
                    ->getClient();
    }

    /**
     * Accessor for ClickUpToken.
     *
     * @return string|null
     *
     * @throws BindingResolutionException
     */
    public function getClickupTokenAttribute()
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
    public function initializeHasClickUp()
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
     * @return Encrypter
     *
     * @throws BindingResolutionException
     */
    protected function resolveEncrypter()
    {
        return Container::getInstance()
                        ->make(Encrypter::class);
    }

    /**
     * Mutator for ClickUpToken.
     *
     * @param  string  $clickup_token
     *
     * @throws BindingResolutionException
     */
    public function setClickupTokenAttribute($clickup_token)
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
