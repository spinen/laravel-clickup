<?php

namespace Spinen\ClickUp\Concerns;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Crypt;
use Spinen\ClickUp\Api\Client as ClickUp;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Builder;

/**
 * Trait HasClickUp
 *
 * @package Spinen\ClickUp
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
     * @return string
     */
    public function getClickupTokenAttribute()
    {
        return is_null($this->attributes['clickup_token'] ?? null)
            ? null
            : Crypt::decrypt($this->attributes['clickup_token']);
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
     * Mutator for ClickUpToken.
     *
     * @param string $clickup_token
     */
    public function setClickupTokenAttribute($clickup_token)
    {
        // If setting the password & already have a client, then empty the client to use new password in client
        if (!is_null($this->builder)) {
            $this->builder = null;
        }

        $this->attributes['clickup_token'] = is_null($clickup_token) ? null : Crypt::encrypt($clickup_token);
    }
}
