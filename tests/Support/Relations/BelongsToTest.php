<?php

namespace Spinen\ClickUp\Support\Relations;

use Mockery;
use Spinen\ClickUp\Support\Builder;
use Spinen\ClickUp\Support\Stubs\Model;

class BelongsToTest extends RelationCase
{
    /**
     * @var BelongsTo
     */
    protected $relation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder_mock->shouldReceive('whereId')
                           ->withArgs(
                               [
                                   1,
                               ]
                           )
                           ->andReturnSelf();

        $this->model_mock->shouldReceive('getAttribute')
                         ->withArgs(
                             [
                                 'id',
                             ]
                         )
                         ->andReturn(1);

        $this->relation = new BelongsTo($this->builder_mock, $this->model_mock, 'id');
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->relation);
    }

    /**
     * @test
     */
    public function it_can_get_the_child()
    {
        $this->assertEquals($this->model_mock, $this->relation->getChild());
    }

    /**
     * @test
     */
    public function it_can_get_the_value_of_the_foregin_key()
    {
        $this->assertEquals(1, $this->relation->getForeignKey());
    }

    /**
     * @test
     */
    public function it_can_get_the_name_of_the_foreign_key()
    {
        $this->assertEquals('id', $this->relation->getForeignKeyName());
    }

    /**
     * @test
     */
    public function it_gets_the_first_value_from_the_results_of_the_builder()
    {
        $results = [
            new Model(['name' => 'first']),
            new Model(['name' => 'second']),
        ];

        $this->builder_mock->shouldReceive('get')
                           ->once()
                           ->withNoArgs()
                           ->andReturn(collect($results));

        $results = $this->relation->getResults();

        $this->assertInstanceOf(Model::class, $results, 'Model instance');

        $this->assertEquals('first', $results->name, 'Correct one');
    }

    /**
     * @test
     */
    public function it_returns_null_if_foreign_key_is_null()
    {
        $builder_mock = Mockery::mock(Builder::class);
        $builder_mock->shouldReceive('getModel')
                     ->andReturn($this->parent_mock);
        $builder_mock->shouldReceive('whereId')
                     ->withArgs(
                         [
                             null,
                         ]
                     )
                     ->andReturnSelf();

        $model_mock = Mockery::mock(Model::class);
        $model_mock->shouldReceive('getAttribute')
                   ->withArgs(
                       [
                           'id',
                       ]
                   )
                   ->andReturn(null);

        $this->relation = new BelongsTo($builder_mock, $model_mock, 'id');

        $this->assertNull($this->relation->getResults());
    }
}
