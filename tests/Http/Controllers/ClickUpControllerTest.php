<?php

namespace Spinen\ClickUp\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Mockery;
use Mockery\Mock;
use Spinen\ClickUp\Api\Client as ClickUp;
use Spinen\ClickUp\TestCase;

class ClickUpControllerTest extends TestCase
{
    /**
     * @var Mock
     */
    protected $clickup_mock;

    /**
     * @var ClickUpController
     */
    protected $controller;

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
    protected $user_mock;

    protected function setUp(): void
    {
        $this->clickup_mock = Mockery::mock(ClickUp::class);

        $this->redirector_mock = Mockery::mock(Redirector::class);

        $this->request_mock = Mockery::mock(Request::class);

        $this->user_mock = Mockery::mock('App\User');

        $this->controller = new ClickUpController();
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(ClickUpController::class, $this->controller);
    }

    /**
     * @test
     */
    public function it_has_the_client_convert_code_to_token()
    {
        $this->request_mock->shouldReceive('get')
                           ->once()
                           ->withArgs(
                               [
                                   'code',
                               ]
                           )
                           ->andReturn('oauth_code');

        $this->clickup_mock->shouldReceive('oauthRequestTokenUsingCode')
                           ->once()
                           ->withArgs(
                               [
                                   'oauth_code',
                               ]
                           )
                           ->andReturn('oauth_token');

        $this->user_mock->shouldIgnoreMissing();

        $this->redirector_mock->shouldIgnoreMissing();

        $this->controller->processCode(
            $this->clickup_mock,
            $this->redirector_mock,
            $this->request_mock,
            $this->user_mock
        );
    }

    /**
     * @test
     */
    public function it_saves_the_users_clickup_token_to_the_oauth_token()
    {
        $this->request_mock->shouldIgnoreMissing();

        $this->clickup_mock->shouldReceive('oauthRequestTokenUsingCode')
                           ->once()
                           ->withAnyArgs()
                           ->andReturn('oauth_token');

        $this->user_mock->shouldReceive('save')
                        ->once()
                        ->withNoArgs()
                        ->andReturnTrue();

        $this->redirector_mock->shouldIgnoreMissing();

        $this->controller->processCode(
            $this->clickup_mock,
            $this->redirector_mock,
            $this->request_mock,
            $this->user_mock
        );

        $this->assertEquals('oauth_token', $this->user_mock->clickup_token);
    }

    /**
     * @test
     */
    public function it_redirects_the_user_to_the_intended_route()
    {
        $this->request_mock->shouldIgnoreMissing();

        $this->clickup_mock->shouldIgnoreMissing();

        $this->user_mock->shouldIgnoreMissing();

        $redirect_mock = Mockery::mock(RedirectResponse::class);

        $this->redirector_mock->shouldReceive('intended')
                              ->once()
                              ->withNoArgs()
                              ->andReturn($redirect_mock);

        $response = $this->controller->processCode(
            $this->clickup_mock,
            $this->redirector_mock,
            $this->request_mock,
            $this->user_mock
        );

        $this->assertEquals($redirect_mock, $response);
    }
}
