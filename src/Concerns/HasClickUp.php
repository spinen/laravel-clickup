<?php

namespace Spinen\ClickUp\Concerns;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Crypt;
use Spinen\ClickUp\Api\Client as ClickUp;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\ClickUpBuilder;

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
    protected ?ClickUpBuilder $clickUpBuilder = null;

    /**
     * Return cached version of the ClickUp Builder for the user
     *
     * @throws BindingResolutionException
     */
    public function clickup(): ClickUpBuilder
    {
        if (is_null($this->clickUpBuilder)) {
            $this->clickUpBuilder = Container::getInstance()
                ->make(ClickUpBuilder::class)
                ->setClient(
                    Container::getInstance()
                    ->make(ClickUp::class)
                    ->setToken($this->clickup_token)
                );
        }

        return $this->clickUpBuilder;
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
            return unserialize(Crypt::decryptString($this->attributes['clickup_token']));
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
     * Mutator for ClickUpToken.
     *
     * @throws BindingResolutionException
     */
    public function setClickupTokenAttribute(?string $clickup_token): void
    {
        // If setting the password & already have a client, then empty the client to use new password in client
        if (! is_null($this->clickUpBuilder)) {
            $this->clickUpBuilder = null;
        }

        $this->attributes['clickup_token'] = is_null($clickup_token)
            ? null
            : Crypt::encryptString($clickup_token);
    }
}
