<?php

namespace Spinen\ClickUp\Concerns;

use Spinen\ClickUp\Api\Client;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\Support\Model;

trait HasClient
{
    /**
     * Client instance
     *
     * @var Client
     */
    protected $client;

    /**
     * Get the Client instance
     *
     * If there is no client assigned on the model, but it has a parent, then try to get the parent's client
     *
     * @throws NoClientException
     */
    public function getClient(): Client
    {
        if (! $this->client && $this->parentModel) {
            $this->client = $this->parentModel->getClient();
        }

        if ($this->client) {
            return $this->client;
        }

        throw new NoClientException();
    }

    /**
     * Set the client instance
     *
     * @param  Client  $client
     * @return $this
     */
    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
