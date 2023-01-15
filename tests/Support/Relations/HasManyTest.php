<?php

namespace Spinen\ClickUp\Support\Relations;

use Spinen\ClickUp\Support\Collection;

class HasManyTest extends RelationCase
{
    /**
     * @var HasMany
     */
    protected $relation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->relation = new HasMany($this->builder_mock, $this->model_mock);
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(HasMany::class, $this->relation);
    }

    /**
     * @test
     */
    public function it_gets_the_child_as_the_result()
    {
        $results = new Collection([]);

        $this->builder_mock->shouldReceive('get')
                           ->once()
                           ->withNoArgs()
                           ->andReturn($results);

        $this->assertEquals($results, $this->relation->getResults());
    }
}
