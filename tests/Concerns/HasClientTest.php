<?php

namespace Spinen\ClickUp\Concerns;

use Mockery;
use Mockery\Mock;
use ReflectionClass;
use Spinen\ClickUp\Api\Client as ClickUp;
use Spinen\ClickUp\Concerns\Stubs\ItemNeedingClient;
use Spinen\ClickUp\Concerns\Stubs\User;
use Spinen\ClickUp\Exceptions\NoClientException;
use Spinen\ClickUp\TestCase;

class HasClientTest extends TestCase
{
    /**
     * @var Mock
     */
    protected $client_mock;

    /**
     * @var User
     */
    protected $trait;

    protected function setUp(): void
    {
        $this->client_mock = Mockery::mock(ClickUp::class);

        $this->trait = new ItemNeedingClient();
    }

    /**
     * @test
     */
    public function it_can_be_used()
    {
        $this->assertArrayHasKey(HasClient::class, (new ReflectionClass($this->trait))->getTraits());
    }

    /**
     * @test
     */
    public function it_can_set_the_client()
    {
        $this->assertEquals($this->trait, $this->trait->setClient($this->client_mock));
    }

    /**
     * @test
     */
    public function it_can_get_client()
    {
        $this->trait->setClient($this->client_mock);

        $this->assertEquals($this->client_mock, $this->trait->getClient());
    }

    /**
     * @test
     */
    public function it_will_get_client_from_parent_if_it_does_not_have_one()
    {
        $this->assertEquals($this->trait->parent_client_mock, $this->trait->getClient());
    }

    /**
     * @test
     */
    public function it_raises_exception_when_it_cannot_get_a_client()
    {
        $this->expectException(NoClientException::class);

        $this->trait->unsetParentModel();

        $this->trait->getClient();
    }
}
