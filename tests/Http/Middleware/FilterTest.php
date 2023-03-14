<?php

namespace Spinen\ClickUp\Http\Middleware;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Mockery;
use Mockery\Mock;
use Spinen\ClickUp\Api\Client as ClickUp;
use Spinen\ClickUp\TestCase;

/**
 * Class FilterTest
 */
class FilterTest extends TestCase
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Mock
     */
    protected $clickup_mock;

    /**
     * @var Mock
     */
    protected $redirector_mock;

    /**
     * @var Mock
     */
    protected $request_mock;

    /**
     * @var Mock
     */
    protected $response_mock;

    /**
     * @var Mock
     */
    protected $url_generator_mock;

    /**
     * @var Mock
     */
    protected $user_mock;

    protected function setUp(): void
    {
        $this->clickup_mock = Mockery::mock(ClickUp::class);
        $this->redirector_mock = Mockery::mock(Redirector::class);
        $this->request_mock = Mockery::mock(Request::class);
        $this->response_mock = Mockery::mock(RedirectResponse::class);
        $this->url_generator_mock = Mockery::mock(UrlGenerator::class);
        $this->user_mock = Mockery::mock(User::class);

        $this->request_mock->shouldReceive('user')
                           ->withNoArgs()
                           ->andReturn($this->user_mock);

        $this->filter = new Filter($this->clickup_mock, $this->redirector_mock, $this->url_generator_mock);
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Filter::class, $this->filter);
    }

    /**
     * @test
     */
    public function it_calls_next_middleware_if_user_has_a_clickup_token()
    {
        $next_middleware = function ($request) {
            $this->assertEquals($this->request_mock, $request);
        };

        $this->mockUserAttributeMutators('token');
        $this->user_mock->clickup_token = 'token';

        $this->filter->handle($this->request_mock, $next_middleware);
    }

    /**
     * @test
     */
    public function it_does_not_call_next_middleware_if_user_does_not_have_a_clickup_token()
    {
        $next_middleware = function ($request) {
            // If this is called, then fail test
            $this->assertTrue(false);
        };

        $this->mockUserAttributeMutators();
        $this->user_mock->clickup_token = null;

        $this->clickup_mock->shouldIgnoreMissing();

        $this->redirector_mock->shouldIgnoreMissing();

        $this->request_mock->shouldIgnoreMissing();

        $this->url_generator_mock->shouldIgnoreMissing();

        $this->filter->handle($this->request_mock, $next_middleware);
    }

    /**
     * @test
     */
    public function it_sets_intended_url_when_user_does_not_have_a_clickup_token()
    {
        $next_middleware = function ($request) {
            // If this is called, then fail test
            $this->assertTrue(false);
        };

        $this->mockUserAttributeMutators();
        $this->user_mock->clickup_token = null;

        $this->clickup_mock->shouldIgnoreMissing();

        $this->url_generator_mock->shouldIgnoreMissing();

        $this->request_mock->shouldReceive('path')
                           ->once()
                           ->withNoArgs()
                           ->andReturn('some/path');

        $this->redirector_mock->shouldReceive('setIntendedUrl')
                              ->once()
                              ->withArgs(
                                  [
                                      'some/path',
                                  ]
                              )
                              ->andReturnNull();

        $this->redirector_mock->shouldIgnoreMissing();

        $this->filter->handle($this->request_mock, $next_middleware);
    }

    /**
     * @test
     */
    public function it_redirects_user_to_correct_uri_if_it_does_not_have_a_clickup_token()
    {
        $next_middleware = function ($request) {
            // If this is called, then fail test
            $this->assertTrue(false);
        };

        $this->mockUserAttributeMutators();
        $this->user_mock->clickup_token = null;

//        $this->clickup_mock->shouldIgnoreMissing();
//
//        $this->url_generator_mock->shouldIgnoreMissing();

        $this->request_mock->shouldIgnoreMissing();

        $this->redirector_mock->shouldIgnoreMissing();

        $this->url_generator_mock->shouldReceive('route')
                                 ->once()
                                 ->withArgs(
                                     [
                                         'clickup.sso.redirect_url',
                                         $this->user_mock,
                                     ]
                                 )
                                 ->andReturn('some/route');

        $this->clickup_mock->shouldReceive('oauthUri')
                           ->once()
                           ->withArgs(
                               [
                                   'some/route',
                               ]
                           )
                           ->andReturn('oauth/uri');

        $this->redirector_mock->shouldReceive('to')
                              ->once()
                              ->withArgs(
                                  [
                                      'oauth/uri',
                                  ]
                              )
                              ->andReturn($this->response_mock);

        $this->filter->handle($this->request_mock, $next_middleware);
    }

    /**
     * Mock out the models setAttribute and getAttribute mutators with the given token
     *
     * @param  string|null  $token
     */
    protected function mockUserAttributeMutators($token = null): void
    {
        $this->user_mock->shouldReceive('setAttribute')
                        ->with('clickup_token', $token)
                        ->once()
                        ->andReturn($this->user_mock);

        $this->user_mock->shouldReceive('getAttribute')
                        ->with('clickup_token')
                        ->once()
                        ->andReturn($token);
    }
}
