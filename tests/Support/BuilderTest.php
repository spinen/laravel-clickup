<?php

namespace Spinen\ClickUp\Support;

use BadMethodCallException;
use Mockery;
use Mockery\Mock;
use Spinen\ClickUp\Api\Client;
use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Exceptions\ModelNotFoundException;
use Spinen\ClickUp\Space;
use Spinen\ClickUp\Task;
use Spinen\ClickUp\Team;
use Spinen\ClickUp\TestCase;
use Spinen\ClickUp\User;
use Spinen\ClickUp\View;

/**
 * Class BuilderTest
 */
class BuilderTest extends TestCase
{
    /**
     * @var ClickUpBuilder
     */
    protected $clickUpBuilder;

    /**
     * @var Mock
     */
    protected $client_mock;

    /**
     * @var array
     */
    protected $team_response = [
        [
            'color' => '#000000',
            'id' => 1,
            'name' => 'Team 1',
        ],
        [
            'color' => '#ffffff',
            'id' => 2,
            'name' => 'Team 2',
        ],
    ];

    protected function setUp(): void
    {
        $this->client_mock = Mockery::mock(Client::class);

        $this->clickUpBuilder = new ClickUpBuilder();
        $this->clickUpBuilder->setClient($this->client_mock);
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Builder::class, $this->builder);
    }

    /**
     * @test
     *
     * @dataProvider rootModels
     */
    public function it_returns_builder_for_root_models($model)
    {
        $this->assertInstanceOf(Builder::class, $this->builder->{$model}(), 'Builder');

        $this->client_mock->shouldReceive('request')
                          ->withAnyArgs()
                          ->andReturn([]);

        $this->assertInstanceOf(Collection::class, $this->builder->{$model}, 'Collection');
    }

    public function rootModels()
    {
        return [
            'spaces' => [
                'model' => 'spaces',
            ],
            'tasks' => [
                'model' => 'tasks',
            ],
            'teams' => [
                'model' => 'teams',
            ],
            'workspaces' => [
                'model' => 'workspaces',
            ],
        ];
    }

    /**
     * @test
     */
    public function it_returns_a_user_for_current_token()
    {
        $this->client_mock->shouldReceive('request')
                          ->once()
                          ->withAnyArgs()
                          ->andReturn([['name' => 'User']]);

        $this->assertInstanceOf(User::class, $this->builder->user);
    }

    /**
     * @test
     */
    public function it_raises_exception_to_unknown_method()
    {
        $this->expectException(BadMethodCallException::class);

        $this->builder->something();
    }

    /**
     * @test
     */
    public function it_returns_null_for_unknown_property()
    {
        $this->assertNull($this->builder->something);
    }

    /**
     * @test
     */
    public function it_will_create_a_model_and_save_via_api_call()
    {
        $this->client_mock->shouldReceive('post')
                          ->withArgs(
                              [
                                  Mockery::any(),
                                  ['some' => 'property'],
                              ]
                          )
                          ->once()
                          ->andReturn([]);

        $this->builder->setClass(Task::class);

        $this->assertInstanceOf(Model::class, $this->builder->create(['some' => 'property']));
    }

    /**
     * @test
     */
    public function it_raises_exception_when_trying_to_create_unknown_model()
    {
        $this->expectException(InvalidRelationshipException::class);

        $this->assertInstanceOf(Model::class, $this->builder->create(['some' => 'property']));
    }

    /**
     * @test
     */
    public function it_gets_expected_results()
    {
        $this->client_mock->shouldReceive('request')
                          ->once()
                          ->withAnyArgs()
                          ->andReturn($this->team_response);

        $results = $this->builder->setClass(Team::class)
                                 ->get();

        $this->assertInstanceOf(Collection::class, $results, 'Collection');

        $this->assertCount(2, $results, '2 Teams');

        $this->assertInstanceOf(Team::class, $results[0], 'Result 1');
        $this->assertInstanceOf(Team::class, $results[1], 'Result 2');

        $this->assertArrayHasKey('color', $results[0]->toArray(), 'color property');
        $this->assertArrayHasKey('id', $results[0]->toArray(), 'id property');
        $this->assertArrayHasKey('name', $results[0]->toArray(), 'name property');
    }

    /**
     * @test
     */
    public function it_get_only_specified_properties()
    {
        $this->client_mock->shouldReceive('request')
                          ->once()
                          ->withAnyArgs()
                          ->andReturn($this->team_response);

        $results = $this->builder->setClass(Team::class)
                                 ->get(['color']);

        $this->assertInstanceOf(Collection::class, $results, 'Collection');

        $this->assertCount(2, $results, '2 Teams');

        $this->assertInstanceOf(Team::class, $results[0], 'Result 1');
        $this->assertInstanceOf(Team::class, $results[1], 'Result 2');

        $this->arrayHasKey('color', $results[0]->toArray(), 'color property');
        $this->assertArrayNotHasKey('id', $results[0]->toArray(), 'id property missing');
        $this->assertArrayNotHasKey('name', $results[0]->toArray(), 'name property missing');
    }

    /**
     * @test
     */
    public function it_finds_expected_model()
    {
        $this->client_mock->shouldReceive('request')
                          ->once()
                          ->withAnyArgs()
                          ->andReturn($this->team_response);

        $results = $this->builder->setClass(Team::class)
                                 ->find(1);

        $this->assertInstanceOf(Team::class, $results);
    }

    /**
     * @test
     */
    public function it_will_make_a_model_without_saving_via_api_call()
    {
        $this->client_mock->shouldNotHaveBeenCalled();

        $this->builder->setClass(Task::class);

        $this->assertInstanceOf(Model::class, $this->builder->make(['some' => 'property']));
    }

    /**
     * @test
     */
    public function it_sets_expected_properties_when_making_a_new_instance()
    {
        $parent_mock = Mockery::mock(Model::class);

        $this->builder->setClass(Space::class)
                      ->setParent($parent_mock);

        $new = $this->builder->newInstance();

        $this->assertInstanceOf(Space::class, $new->getModel(), 'class');

        $this->assertEquals($this->client_mock, $new->getClient(), 'client');

        // TODO: Figure out way to assert that parentModel was set
        //$this->assertEquals($parent_mock, $new->parentModel, 'parent');
    }

    /**
     * @test
     */
    public function it_allows_setting_model_for_new_instance()
    {
        $this->builder->setClass(Space::class);

        $new = $this->builder->newInstanceForModel(Task::class);

        $this->assertInstanceOf(Task::class, $new->getModel());
    }

    /**
     * @test
     */
    public function it_peels_off_single_response_keys()
    {
        $this->client_mock->shouldReceive('request')
                          ->once()
                          ->withAnyArgs()
                          ->andReturn(
                              [
                                  'team' => [
                                      'id' => 1,
                                  ],
                              ]
                          );

        $results = $this->builder->setClass(Team::class)
                                 ->get();

        $this->assertArrayHasKey('id', $results[0]->toArray(), 'id');
        $this->assertArrayNotHasKey('team', $results[0]->toArray(), 'team');
    }

    /**
     * @test
     */
    public function it_peels_off_collection_response_keys()
    {
        $this->client_mock->shouldReceive('request')
                          ->once()
                          ->withAnyArgs()
                          ->andReturn(
                              [
                                  'teams' => [
                                      [
                                          'id' => 1,
                                      ],
                                      [
                                          'id' => 2,
                                      ],
                                  ],
                              ]
                          );

        $results = $this->builder->setClass(Team::class)
                                 ->get();

        $this->assertCount(2, $results, 'both teams');

        $this->assertArrayHasKey('id', $results[0]->toArray(), 'id');
        $this->assertArrayNotHasKey('team', $results[0]->toArray(), 'teams');
    }

    /**
     * @test
     */
    public function it_allows_setting_class()
    {
        $this->builder->setClass(View::class);

        $this->assertInstanceOf(View::class, $this->builder->getModel());
    }

    /**
     * @test
     */
    public function it_raises_exception_to_setting_unknown_class()
    {
        $this->expectException(ModelNotFoundException::class);

        $this->builder->setClass('Some\\Unknown\\Class');
    }

    /**
     * @test
     */
    public function it_where_filters_to_reuest_as_query_string_parameters()
    {
        $this->builder->setClass(Team::class);

        $this->builder->where('some', 'value');

        $this->assertEquals('/team?some=value', $this->builder->getPath(), 'simple');

        $this->builder->where('other', 'different');

        $this->assertEquals('/team?some=value&other=different', $this->builder->getPath(), 'multiple');

        $this->builder->where('array', collect(['one', 'two']));

        $this->assertEquals(
            '/team?some=value&other=different&array%5B0%5D=one&array%5B1%5D=two',
            $this->builder->getPath(),
            'collection'
        );

        $this->builder->where('boolean');

        $this->assertEquals(
            '/team?some=value&other=different&array%5B0%5D=one&array%5B1%5D=two&boolean=1',
            $this->builder->getPath(),
            'boolean'
        );

        $this->builder->whereNot('negative');

        $this->assertEquals(
            '/team?some=value&other=different&array%5B0%5D=one&array%5B1%5D=two&boolean=1&negative=0',
            $this->builder->getPath(),
            'where not'
        );

        $this->builder->whereId(1);

        $this->assertEquals(
            '/team/1?some=value&other=different&array%5B0%5D=one&array%5B1%5D=two&boolean=1&negative=0',
            $this->builder->getPath(),
            'id'
        );
    }
}
