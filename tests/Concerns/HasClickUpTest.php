<?php

namespace Spinen\ClickUp\Concerns;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Crypt;
use Mockery;
use Mockery\Mock;
use ReflectionClass;
use Spinen\ClickUp\Api\Client as ClickUp;
use Spinen\ClickUp\Concerns\Stubs\User;
use Spinen\ClickUp\Support\ClickUpBuilder;
use Spinen\ClickUp\TestCase;

class HasClickUpTest extends TestCase
{
    /**
     * @var Mock
     */
    protected $builder_mock;

    /**
     * @var Mock
     */
    protected $client_mock;

    /**
     * @var Mock
     */
    protected $encrypter_mock;

    /**
     * @var User
     */
    protected $trait;

    protected function setUp(): void
    {
        $this->trait = new User();

        $this->client_mock = Mockery::mock(ClickUp::class);
        $this->client_mock->shouldReceive('setToken')
            ->withArgs(
                [
                    Mockery::any(),
                ]
            )
            ->andReturnSelf();

        $this->builder_mock = Mockery::mock(ClickUpBuilder::class);
        $this->builder_mock->shouldReceive('getClient')
            ->withNoArgs()
            ->andReturn($this->client_mock);
        $this->builder_mock->shouldReceive('setClient')
            ->withArgs(
                [
                    $this->client_mock,
                ]
            )
            ->andReturnSelf();

        Container::getInstance()
            ->instance(ClickUpBuilder::class, $this->builder_mock);

        Container::getInstance()
            ->instance(ClickUp::class, $this->client_mock);
    }

    /**
     * @test
     */
    public function it_can_be_used()
    {
        $this->assertArrayHasKey(HasClickUp::class, (new ReflectionClass($this->trait))->getTraits());
    }

    /**
     * @test
     */
    public function it_returns_a_builder_for_clickup_method()
    {
        $this->assertInstanceOf(Builder::class, $this->trait->clickup());
    }

    /**
     * @test
     */
    public function it_caches_the_builder()
    {
        $this->assertNull($this->trait->getBuilder(), 'baseline');

        $this->trait->clickup();

        $this->assertInstanceOf(Builder::class, $this->trait->getBuilder());
    }

    /**
     * @test
     */
    public function it_initializes_the_trait_as_expected()
    {
        $this->assertEmpty($this->trait->fillable, 'Baseline fillable');
        $this->assertEmpty($this->trait->hidden, 'Baseline hidden');

        $this->trait->initializeHasClickUp();

        $this->assertContains('clickup_token', $this->trait->fillable, 'Fillable with clickup_token');
        $this->assertContains('clickup', $this->trait->hidden, 'Hide clickup');
        $this->assertContains('clickup_token', $this->trait->hidden, 'Hide clickup_token');
    }

    /**
     * @test
     */
    public function it_has_an_accessor_to_get_the_client()
    {
        $this->assertInstanceOf(ClickUp::class, $this->trait->getClickupAttribute());
    }

    /**
     * @test
     */
    public function it_has_an_accessor_to_decrypt_clickup_token()
    {
        Crypt::shouldReceive('decryptString')
            ->once()
            ->with($this->trait->attributes['clickup_token'])
            ->andReturn('decrypted');

        $this->trait->getClickupTokenAttribute();
    }

    /**
     * @test
     */
    public function it_does_not_try_to_decrypt_null_clickup_token()
    {
        $this->trait->attributes['clickup_token'] = null;

        Crypt::shouldReceive('decryptString')
            ->never()
            ->withAnyArgs();

        $this->assertNull($this->trait->getClickupTokenAttribute());
    }

    /**
     * @test
     */
    public function it_has_mutator_to_encypt_clickup_token()
    {
        Crypt::shouldReceive('encryptString')
            ->once()
            ->withArgs(
                [
                    'unencrypted',
                ]
            )
            ->andReturn('encrypted');

        $this->trait->setClickupTokenAttribute('unencrypted');

        $this->assertEquals('encrypted', $this->trait->attributes['clickup_token']);
    }

    /**
     * @test
     */
    public function it_does_not_mutate_a_null_clickup_token()
    {
        Crypt::shouldReceive('encryptString')
            ->never()
            ->withAnyArgs();

        $this->trait->setClickupTokenAttribute(null);

        $this->assertNull($this->trait->attributes['clickup_token']);
    }

    /**
     * @test
     */
    public function it_invalidates_builder_cache_when_setting_clickup_token()
    {
        Crypt::shouldReceive('encryptString')
            ->withAnyArgs();

        // Force cache
        $this->trait->clickup();

        $this->assertNotNull($this->trait->getBuilder(), 'Baseline that cache exist');

        $this->trait->setClickupTokenAttribute('changed');

        $this->assertNull($this->trait->getBuilder(), 'Cache was invalidated');
    }
}
