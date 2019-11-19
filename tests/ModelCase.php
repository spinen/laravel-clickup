<?php

namespace Spinen\ClickUp;

use Mockery;
use Mockery\Mock;
use Spinen\ClickUp\Api\Client;
use Spinen\ClickUp\Support\Model;

abstract class ModelCase extends TestCase
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Mock
     */
    protected $client_mock;

    protected function setUp(): void
    {
        $this->client_mock = Mockery::mock(Client::class);

        $class = preg_replace('/(.*)Test$/u', '$1', get_class($this));

        $this->model = (new $class())->setClient($this->client_mock);
    }
}
