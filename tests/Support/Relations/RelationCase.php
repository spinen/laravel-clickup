<?php

namespace Spinen\ClickUp\Support\Relations;

use Mockery;
use Mockery\Mock;
use Spinen\ClickUp\Support\Builder;
use Spinen\ClickUp\Support\Model;
use Spinen\ClickUp\TestCase;

class RelationCase extends TestCase
{
    /**
     * @var Mock
     */
    protected $builder_mock;

    /**
     * @var Mock
     */
    protected $model_mock;

    /**
     * @var Mock
     */
    protected $parent_mock;

    protected function setUp(): void
    {
        $this->parent_mock = Mockery::mock(Model::class);

        $this->builder_mock = Mockery::mock(Builder::class);
        $this->builder_mock->shouldReceive('getModel')
                           ->andReturn($this->parent_mock);

        $this->model_mock = Mockery::mock(Model::class);
    }
}
