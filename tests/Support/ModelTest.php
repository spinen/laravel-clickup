<?php

namespace Spinen\ClickUp\Support;

use GuzzleHttp\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\JsonEncodingException;
use LogicException;
use Mockery;
use Mockery\Mock;
use Spinen\ClickUp\Api\Client;
use Spinen\ClickUp\Exceptions\ModelReadonlyException;
use Spinen\ClickUp\Exceptions\UnableToSaveException;
use Spinen\ClickUp\Support\Relations\BelongsTo;
use Spinen\ClickUp\Support\Relations\ChildOf;
use Spinen\ClickUp\Support\Relations\HasMany;
use Spinen\ClickUp\Support\Stubs\Model;
use Spinen\ClickUp\Team;
use Spinen\ClickUp\TestCase;

/**
 * Class ModelTest
 *
 * @package Spinen\ClickUp\Support
 */
class ModelTestTest extends TestCase
{
    /**
     * @var Mock
     */
    protected $client_mock;

    /**
     * @var Model
     */
    protected $model;

    protected function setUp(): void
    {
        $this->client_mock = Mockery::mock(Client::class);

        $this->model = (new Model(['some' => 'property']))->setClient($this->client_mock);
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Model::class, $this->model);
    }

    /**
     * @test
     */
    public function it_exposes_attributes_as_properties()
    {
        $this->assertIsString($this->model->some);
    }

    /**
     * @test
     */
    public function it_exposes_attributes_as_array_keys()
    {
        $this->assertIsString($this->model['some']);
    }

    /**
     * @test
     */
    public function it_knows_if_model_has_properties()
    {
        $this->assertTrue(isset($this->model->some), 'set');
        $this->assertFalse(isset($this->model->missing), 'missing');
    }

    /**
     * @test
     */
    public function it_knows_if_model_has_array_keys()
    {
        $this->assertTrue(isset($this->model['some']), 'set');
        $this->assertFalse(isset($this->model['missing']), 'missing');
    }

    /**
     * @test
     */
    public function it_allows_setting_properties()
    {
        $this->model->other = 'new';

        $this->assertEquals('new', $this->model->other);
    }

    /**
     * @test
     */
    public function it_allows_setting_properties_as_an_array()
    {
        $this->model['other'] = 'new';

        $this->assertEquals('new', $this->model['other']);
    }

    /**
     * @test
     */
    public function it_raises_exception_when_setting_property_on_readonly_model()
    {
        $this->model->setReadonly();

        $this->expectException(ModelReadonlyException::class);

        $this->model->some = 'changed';
    }

    /**
     * @test
     */
    public function it_raises_exception_when_setting_array_key_on_readonly_model()
    {
        $this->model->setReadonly();

        $this->expectException(ModelReadonlyException::class);

        $this->model['some'] = 'changed';
    }

    /**
     * @test
     */
    public function it_cast_the_model_as_json_when_used_as_a_string()
    {
        $this->assertJson((string)$this->model);
    }

    /**
     * @test
     */
    public function it_unsets_a_property()
    {
        unset($this->model->some);

        $this->assertFalse(isset($this->model->some));
    }

    /**
     * @test
     */
    public function it_unsets_an_array_key()
    {
        unset($this->model['some']);

        $this->assertFalse(isset($this->model['some']));
    }

    /**
     * @test
     */
    public function it_gets_a_realtionship_via_method_of_name_to_relationship()
    {
        $this->client_mock->shouldReceive('request')
                          ->once()
                          ->withArgs(
                              [
                                  'some/path/some/path',
                              ]
                          )
                          ->andReturn([]);

        $this->assertInstanceOf(Collection::class, $this->model->related);
    }

    /**
     * @test
     */
    public function it_caches_the_relationship()
    {
        $this->client_mock->shouldReceive('request')
                          ->once()
                          ->withArgs(
                              [
                                  'some/path/some/path',
                              ]
                          )
                          ->andReturn([]);

        $this->assertFalse($this->model->relationLoaded('related'), 'Baseline');

        $this->model->related;

        $this->assertTrue($this->model->relationLoaded('related'), 'Cached');
    }

    /**
     * @test
     */
    public function it_raises_exception_to_using_an_non_relation_as_a_relation()
    {
        $this->expectException(LogicException::class);

        $this->model->nonrealation;
    }

    /**
     * @test
     */
    public function it_raises_exception_to_using_a_null_as_a_relation()
    {
        $this->expectException(LogicException::class);

        $this->model->nullrealation;
    }

    /**
     * @test
     */
    public function it_returns_proper_belongs_to_builder()
    {
        $belongs_to = $this->model->belongsTo(Team::class);

        $this->assertInstanceOf(BelongsTo::class, $belongs_to, 'BelongsTo instance');

        $this->assertEquals($this->model, $belongs_to->getParent(), 'Parent');

        $this->assertInstanceOf(
            Team::class,
            $belongs_to->getBuilder()
                       ->getModel(),
            'Parent set'
        );

        $this->assertEquals('team_id', $belongs_to->getForeignKeyName(), 'Foreign Key');

        $this->assertEquals(
            $this->client_mock,
            $belongs_to->getBuilder()
                       ->getClient(),
            'Client'
        );
    }

    /**
     * @test
     */
    public function it_allows_passing_foreign_key_to_belongs_to()
    {
        $this->assertEquals(
            'other',
            $this->model->belongsTo(Team::class, 'other')
                        ->getForeignKeyName()
        );
    }

    /**
     * @test
     */
    public function it_returns_proper_child_of_builder()
    {
        $child_of = $this->model->childOf(Team::class);

        $this->assertInstanceOf(ChildOf::class, $child_of, 'ChildOf instance');

        $this->assertEquals($this->model, $child_of->getParent(), 'Parent');

        $this->assertInstanceOf(
            Team::class,
            $child_of->getBuilder()
                     ->getModel(),
            'Parent set'
        );

        $this->assertEquals('team_id', $child_of->getForeignKeyName(), 'Foreign Key');

        $this->assertEquals(
            $this->client_mock,
            $child_of->getBuilder()
                     ->getClient(),
            'Client'
        );
    }

    /**
     * @test
     */
    public function it_allows_passing_foreign_key_to_child_of()
    {
        $this->assertEquals(
            'other',
            $this->model->childOf(Team::class, 'other')
                        ->getForeignKeyName()
        );
    }

    /**
     * @test
     */
    public function it_deletes_model_via_api_if_not_readonly()
    {
        $this->model->id = 1;

        $this->client_mock->shouldReceive('delete')
                          ->once()
                          ->withArgs(
                              [
                                  'some/path/1',
                              ]
                          )
                          ->andReturn([]);

        $this->assertTrue($this->model->delete());
    }

    /**
     * @test
     */
    public function it_does_not_delete_readonly_models()
    {
        $this->model->id = 1;

        $this->model->setReadonly();

        $this->client_mock->shouldNotReceive('delete');

        $this->assertFalse($this->model->delete());
    }

    /**
     * @test
     */
    public function it_returns_false_when_unable_to_delete_model_via_api()
    {
        $this->model->id = 1;

        $this->client_mock->shouldReceive('delete')
                          ->withAnyArgs()
                          ->andThrow(new InvalidArgumentException());

        $this->assertFalse($this->model->delete());
    }

    /**
     * @test
     */
    public function it_fills_attributes()
    {
        $this->assertArrayNotHasKey('filled', $this->model, 'Baseline');

        $properties = [
            'filled' => 'value',
        ];

        $this->model->fill($properties);

        $this->assertArrayHasKey('filled', $this->model, 'Filled');
    }

    /**
     * @test
     */
    public function it_calls_setters_when_filling_properties()
    {
        $this->assertNull($this->model->mutator, 'Baseline');

        $properties = [
            'mutator' => 'value',
        ];

        $this->model->fill($properties);

        $this->assertEquals('mutated: value', $this->model->mutator, 'Mutated');
    }

    /**
     * @test
     */
    public function it_defaults_to_not_incrementing_ids()
    {
        $this->assertFalse($this->model->getIncrementing());
    }

    /**
     * @test
     */
    public function it_returns_the_expected_key()
    {
        $this->model->id = 1;

        $this->assertEquals(1, $this->model->getKey());
    }

    /**
     * @test
     */
    public function it_returns_the_expected_key_name()
    {
        $this->assertEquals('id', $this->model->getKeyName());
    }

    /**
     * @test
     */
    public function it_returns_the_expected_key_type()
    {
        $this->assertEquals('int', $this->model->getKeyType());
    }

    /**
     * @test
     */
    public function it_gets_the_expected_path()
    {
        $this->assertEquals('some/path', $this->model->getPath(), 'simple');

        $this->model->id = 1;

        $this->assertEquals('some/path/1', $this->model->getPath(), 'specific id');

        $this->assertEquals('some/path/1/extra/', $this->model->getPath('/extra/'), 'extra');

        $this->assertEquals(
            'some/path/1?query=string',
            $this->model->getPath(null, ['query' => 'string']),
            'query string'
        );

        $this->model->parentModel = new Model();

        $this->assertEquals('some/path/1', $this->model->getPath(), 'parent, but not nested');

        $this->model->setNested(true);

        $this->assertEquals('some/path/some/path/1', $this->model->getPath(), 'parent, and nested');

        unset($this->model->parentModel);

        $this->assertEquals('some/path/1', $this->model->getPath(), 'no parent, and nested');

        unset($this->model->id);

        $this->assertEquals('some/path', $this->model->getPath(), 'no parent, no id, and nested');
    }

    /**
     * @test
     */
    public function it_gets_the_response_collection_key()
    {
        $this->assertEquals('models', $this->model->getResponseCollectionKey());
    }

    /**
     * @test
     */
    public function it_will_use_response_collection_key_property_if_set()
    {
        $this->model->setResponseCollectionKey('overwrote');

        $this->assertEquals('overwrote', $this->model->getResponseCollectionKey());
    }

    /**
     * @test
     */
    public function it_gets_the_response_key()
    {
        $this->assertEquals('model', $this->model->getResponseKey());
    }

    /**
     * @test
     */
    public function it_will_use_response_key_property_if_set()
    {
        $this->model->setResponseKey('overwrote');

        $this->assertEquals('overwrote', $this->model->getResponseKey());
    }

    /**
     * @test
     */
    public function it_returns_collection_when_given_many_related_properties()
    {
        $given_many = $this->model->givenMany(
            Team::class,
            [
                ['name' => 'Team 1'],
                ['name' => 'Team 2'],
            ]
        );

        $this->assertInstanceOf(Collection::class, $given_many, 'Collection instance');

        $this->assertEquals(
            $this->client_mock,
            $given_many->first()
                       ->getClient(),
            'Client'
        );
    }

    /**
     * @test
     */
    public function it_returns_a_model_when_given_one_related_property()
    {
        $given_one = $this->model->givenOne(
            Team::class,
            ['name' => 'Team 1']
        );

        $this->assertInstanceOf(Team::class, $given_one, 'Model instance');

        $this->assertEquals($this->client_mock, $given_one->getClient(), 'Client');
    }

    /**
     * @test
     */
    public function it_returns_proper_has_many_builder()
    {
        $has_many = $this->model->hasMany(Team::class);

        $this->assertInstanceOf(HasMany::class, $has_many, 'HasMany instance');

        $this->assertEquals($this->model, $has_many->getParent(), 'Parent');

        $this->assertInstanceOf(
            Team::class,
            $has_many->getBuilder()
                     ->getModel(),
            'Parent set'
        );

        $this->assertEquals(
            $this->client_mock,
            $has_many->getBuilder()
                     ->getClient(),
            'Client'
        );
    }

    /**
     * @test
     */
    public function it_knows_if_model_is_nested_by_using_nested_property()
    {
        $this->assertFalse($this->model->isNested(), 'Baseline');

        $this->model->setNested(true);

        $this->assertTrue($this->model->isNested(), 'Set');
    }

    /**
     * @test
     */
    public function it_can_make_a_new_model_with_properties_synced()
    {
        $new_properties = ['new' => 'properties'];

        $new = $this->model->newFromBuilder($new_properties);

        $this->assertInstanceOf(get_class($this->model), $new, 'Correct instance');

        $this->assertArrayNotHasKey('some', $new->toArray(), 'Old keys gone');

        $this->assertEquals('properties', $new->new, 'New key preset');

        $this->assertEquals($new_properties, $new->getOriginal(), 'Synced');

        $this->assertTrue($new->exists, 'Exist');

        $this->assertEquals($this->client_mock, $new->getClient(), 'Client');
    }

    /**
     * @test
     */
    public function it_can_make_a_new_model_without_syncing_properties()
    {
        $new_properties = ['new' => 'properties'];

        $new = $this->model->newInstance($new_properties);

        $this->assertInstanceOf(get_class($this->model), $new, 'Correct instance');

        $this->assertArrayNotHasKey('some', $new->toArray(), 'Old keys gone');

        $this->assertEquals('properties', $new->new, 'New key preset');

        $this->assertEmpty($new->getOriginal(), 'Not synced');

        $this->assertFalse($new->exists, 'Exist');

        $this->assertEquals($this->client_mock, $new->getClient(), 'Client');
    }

    /**
     * @test
     */
    public function it_can_make_a_new_model_without_syncing_properties_but_marking_as_exist()
    {
        $new_properties = ['new' => 'properties'];

        $new = $this->model->newInstance($new_properties, true);

        $this->assertInstanceOf(get_class($this->model), $new, 'Correct instance');

        $this->assertArrayNotHasKey('some', $new->toArray(), 'Old keys gone');

        $this->assertEquals('properties', $new->new, 'New key preset');

        $this->assertEmpty($new->getOriginal(), 'Not synced');

        $this->assertTrue($new->exists, 'Exist');

        $this->assertEquals($this->client_mock, $new->getClient(), 'Client');
    }

    /**
     * @test
     */
    public function it_knows_if_a_relationship_is_loaded()
    {
        $this->client_mock->shouldReceive('request')
                          ->once()
                          ->withArgs(
                              [
                                  'some/path/some/path',
                              ]
                          )
                          ->andReturn([]);

        $this->assertFalse($this->model->relationLoaded('related'));

        $this->model->related;

        $this->assertTrue($this->model->relationLoaded('related'));
    }

    /**
     * @test
     */
    public function it_does_not_try_to_save_a_readonly_model()
    {
        $this->model->setReadonly(true);

        $this->assertFalse($this->model->save());
    }

    /**
     * @test
     */
    public function it_does_not_try_to_save_an_unchanged_model()
    {
        // Make a new one as the setup one has properties on the construct, so it is dirty
        $this->model = $this->model->newInstance([], true);

        $this->assertTrue($this->model->save());
    }

    /**
     * @test
     */
    public function it_will_post_when_saving_and_put_when_updating_a_model()
    {
        $this->client_mock->shouldReceive('post')
                          ->once()
                          ->withArgs(
                              [
                                  'some/path',
                                  [
                                      'some' => 'property',
                                  ],
                              ]
                          )
                          ->andReturn(
                              [
                                  'id'   => 1,
                                  'some' => 'property',
                              ]
                          );

        $this->client_mock->shouldReceive('put')
                          ->once()
                          ->withArgs(
                              [
                                  'some/path/1',
                                  [
                                      'some' => 'changed',
                                  ],
                              ]
                          )
                          ->andReturn(
                              [
                                  'id'      => 1,
                                  'some'    => 'changed',
                                  'updated' => true,
                              ]
                          );

        $this->assertFalse($this->model->exists, 'Exist');

        $this->assertTrue($this->model->isDirty(), 'Dirty');

        $this->assertTrue($this->model->save(), 'Save');

        $this->assertTrue($this->model->exists, 'Exist after save');

        $this->assertFalse($this->model->isDirty(), 'Not dirty after save');

        $this->assertEquals(1, $this->model->id, 'Saved model');

        $this->assertEmpty($this->model->getChanges(), 'No changes');

        $this->model->some = 'changed';

        $this->assertTrue($this->model->isDirty(), 'Dirty after changed');

        $this->assertTrue($this->model->save(), 'Updated');

        $this->assertFalse($this->model->isDirty(), 'Not dirty after update');

        $this->assertEquals(true, $this->model->updated, 'Updated model');

        $this->assertArrayHasKey('some', $this->model->getChanges(), 'Changes');
    }

    /**
     * @test
     */
    public function it_returns_false_when_saving_new_model_fails_api_call()
    {
        $this->client_mock->shouldReceive('post')
                          ->once()
                          ->withAnyArgs()
                          ->andThrow(new InvalidArgumentException());

        $this->assertFalse($this->model->save());
    }

    /**
     * @test
     */
    public function it_returns_true_when_save_or_fail_saves()
    {
        $this->client_mock->shouldReceive('post')
                          ->once()
                          ->withAnyArgs()
                          ->andReturn([]);

        $this->assertTrue($this->model->saveOrFail());
    }

    /**
     * @test
     */
    public function it_raises_exception_when_save_or_fail_fails()
    {
        $this->client_mock->shouldReceive('post')
                          ->once()
                          ->withAnyArgs()
                          ->andThrow(new InvalidArgumentException());

        $this->expectException(UnableToSaveException::class);

        $this->model->saveOrFail();
    }
}
