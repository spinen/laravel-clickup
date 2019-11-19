<?php

namespace Spinen\ClickUp\Support\Relations;

use Spinen\ClickUp\Support\Relations\Stubs\Relation;

class RelationTest extends RelationCase
{
    /**
     * @var Relation
     */
    protected $relation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->relation = new Relation($this->builder_mock, $this->model_mock);
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Relation::class, $this->relation);
    }

    /**
     * @test
     */
    public function it_can_get_the_builder()
    {
        $this->assertEquals($this->builder_mock, $this->relation->getBuilder());
    }

    /**
     * @test
     */
    public function it_can_get_the_parent()
    {
        $this->assertEquals($this->parent_mock, $this->relation->getParent());
    }

    /**
     * @test
     */
    public function it_can_get_the_related()
    {
        $this->assertEquals($this->model_mock, $this->relation->getRelated());
    }

    /**
     * @test
     */
    public function it_passes_unknown_methods_to_the_builder()
    {
        $this->builder_mock->shouldReceive('passedMethod')
                           ->once()
                           ->withNoArgs()
                           ->andReturn('received');

        $this->assertEquals('received', $this->relation->passedMethod());
    }

    /**
     * @test
     */
    public function it_will_not_loop_proxied_calls()
    {
        $this->builder_mock->shouldReceive('returnsSelf')
                           ->once()
                           ->withNoArgs()
                           ->andReturnSelf();

        $this->assertEquals($this->relation, $this->relation->returnsSelf());
    }

    /**
     * @test
     */
    public function it_allows_for_macros()
    {
        Relation::macro(
            'newMacro',
            function () {
                return 'value';
            }
        );

        $this->assertEquals('value', $this->relation->newMacro());
    }
}
