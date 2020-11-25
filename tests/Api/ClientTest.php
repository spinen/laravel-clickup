<?php

namespace Spinen\ClickUp\Api;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\InvalidArgumentException;
use Mockery;
use Mockery\Mock;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Spinen\ClickUp\Exceptions\TokenException;
use Spinen\ClickUp\TestCase;
use TypeError;

/**
 * Class ClientTest
 *
 * @package Spinen\ClickUp\Api
 */
class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $configs = [];

    /**
     * @var Mock
     */
    protected $guzzle_mock;

    /**
     * @var Mock
     */
    protected $response_mock;

    /**
     * @var Mock
     */
    protected $stream_interface_mock;

    /**
     * @var Mock
     */
    protected $user_mock;

    protected function setUp(): void
    {
        $this->configs = require(__DIR__ . '/../../config/clickup.php');

        $this->guzzle_mock = Mockery::mock(Guzzle::class);


        $this->stream_interface_mock = Mockery::mock(StreamInterface::class);
        $this->stream_interface_mock->shouldReceive('getContents')
                                    ->withNoArgs()
                                    ->andReturn('{}');

        $this->response_mock = Mockery::mock(ResponseInterface::class);
        $this->response_mock->shouldReceive('getBody')
                            ->withNoArgs()
                            ->andReturn($this->stream_interface_mock);

        $this->client = new Client($this->configs, $this->guzzle_mock);
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Client::class, $this->client);
    }

    /**
     * @test
     */
    public function it_expects_the_first_argument_tobe_an_array()
    {
        $this->expectException(TypeError::class);

        new Client('', $this->guzzle_mock);
    }

    /**
     * @test
     */
    public function it_expects_the_second_argument_tobe_a_guzzle()
    {
        $this->expectException(TypeError::class);

        new Client($this->configs, '');
    }

    /**
     * @test
     */
    public function it_takes_the_token_as_third_argument()
    {
        $token = 'pk_token';

        try {
            $this->client->request('resource');

            $this->fail('Must require token before allowing request');
        } catch (TokenException $e) {
            $this->client = $this->client = new Client($this->configs, $this->guzzle_mock, $token);

            $this->guzzle_mock->shouldReceive('request')
                              ->once()
                              ->withArgs(
                                  [
                                      Mockery::any(),
                                      Mockery::any(),
                                      Mockery::on(
                                          function ($options) use ($token) {
                                              return $options['headers']['Authorization'] === $token;
                                          }
                                      ),
                                  ]
                              )
                              ->andReturn($this->response_mock);

            $this->client->request('resource');
        }
    }

    /**
     * @test
     */
    public function it_allows_setting_the_configs()
    {
        // NOTE: The url key for the uri is the only exposes config, so using it to see if configs are changed
        $default_uri = $this->client->uri();

        $return = $this->client->setConfigs(
            [
                'url' => 'changed',
            ]
        );

        $this->assertNotSame($default_uri, $this->client->uri());

        $this->assertInstanceOf(Client::class, $return);
    }

    /**
     * @test
     */
    public function it_sets_url_to_default_value_if_not_passed_in()
    {
        $this->client->setConfigs(
            [
                'url' => null,
            ]
        );

        $this->assertEquals('https://api.clickup.com/api/v2/', $this->client->uri());
    }

    /**
     * @test
     */
    public function it_allows_setting_the_token()
    {
        $token = 'pk_token';

        $this->guzzle_mock->shouldReceive('request')
                          ->once()
                          ->withArgs(
                              [
                                  Mockery::any(),
                                  Mockery::any(),
                                  Mockery::on(
                                      function ($options) use ($token) {
                                          return $options['headers']['Authorization'] === $token;
                                      }
                                  ),
                              ]
                          )
                          ->andReturn($this->response_mock);

        try {
            $this->client->request('resource');

            $this->fail('Must require token before allowing request');
        } catch (TokenException $e) {
            $return = $this->client->setToken($token);

            $this->client->request('resource');

            $this->assertInstanceOf(Client::class, $return);
        }
    }

    /**
     * @test
     */
    public function it_builds_correct_uri()
    {
        $this->client->setConfigs(
            [
                'url' => 'http://some/place',
            ]
        );

        $this->assertEquals('http://some/place/', $this->client->uri(), 'slash on end of URL');
        $this->assertEquals('http://some/place/resource', $this->client->uri('resource'), 'simple URI');
        $this->assertEquals('http://some/place/resource', $this->client->uri('/resource'), 'no double slash');
        $this->assertEquals('http://some/place/resource/', $this->client->uri('resource/'), 'leaves end slash');
        $this->assertEquals(
            'http://some/place?paramater=value',
            $this->client->uri('?paramater=value'),
            'query string'
        );
        $this->assertEquals(
            'http://other/url/resource/',
            $this->client->uri('resource/', 'http://other/url/'),
            'url as second parameter'
        );
    }

    /**
     * @test
     * @dataProvider requestProvider
     */
    public function it_makes_expected_request_to_api($function, $method, $data, $parameter = null)
    {
        $token = 'pk_token';

        $this->guzzle_mock->shouldReceive('request')
                          ->once()
                          ->withArgs(
                              [
                                  $method,
                                  $this->configs['url'] . '/resource',
                                  [
                                      'headers' => [
                                          'Authorization' => $token,
                                          'Content-Type'  => 'application/json',
                                      ],
                                      'body'    => json_encode($data),
                                  ],
                              ]
                          )
                          ->andReturn($this->response_mock);

        $this->client->setToken($token);

        $response = is_null($parameter)
            ? $this->client->{$function}('resource')
            : $this->client->{$function}(
                'resource',
                $parameter
            );

        $this->assertEquals([], $response);
    }

    public function requestProvider()
    {
        return [
            'raw request' => [
                'function' => 'request',
                'method'   => 'GET',
                'data'     => [],
            ],
            'delete'      => [
                'function' => 'delete',
                'method'   => 'DELETE',
                'data'     => [],
            ],
            'get'         => [
                'function' => 'get',
                'method'   => 'GET',
                'data'     => null,
            ],
            'post'        => [
                'function'  => 'post',
                'method'    => 'POST',
                'data'      => [],
                'parameter' => [],
            ],
            'put'         => [
                'function'  => 'put',
                'method'    => 'PUT',
                'data'      => [],
                'parameter' => [],
            ],
        ];
    }

    /**
     * @test
     */
    public function it_raises_exception_when_guzzle_error()
    {
        $this->expectException(GuzzleException::class);

        $this->guzzle_mock->shouldReceive('request')
                          ->once()
                          ->withAnyArgs()
                          ->andThrow(new InvalidArgumentException());

        $this->client->setToken('pk_token')
                     ->request('/bad_request');
    }

    /**
     * @test
     * @dataProvider responseProvider
     */
    public function it_returns_expected_responses($json, $expected)
    {
        $this->stream_interface_mock = Mockery::mock(StreamInterface::class);
        $this->stream_interface_mock->shouldReceive('getContents')
                                    ->withNoArgs()
                                    ->andReturn($json);

        $this->response_mock = Mockery::mock(ResponseInterface::class);
        $this->response_mock->shouldReceive('getBody')
                            ->withNoArgs()
                            ->andReturn($this->stream_interface_mock);

        $this->guzzle_mock->shouldReceive('request')
                          ->once()
                          ->withAnyArgs()
                          ->andReturn($this->response_mock);

        $this->assertEquals(
            $expected,
            $this->client->setToken('pk_token')
                         ->request('resource')
        );
    }

    public function responseProvider()
    {
        return [
            'null'         => [
                'json'     => 'null',
                'expected' => null,
            ],
            'empty_object' => [
                'json'     => '{}',
                'expected' => [],
            ],
            'empty_array'  => [
                'json'     => '[]',
                'expected' => [],
            ],
            'object'       => [
                'json'     => '{"some": "key"}',
                'expected' => [
                    'some' => 'key',
                ],
            ],
            'array'        => [
                'json'     => '[{"some": 1}, {"some": 2}]',
                'expected' => [
                    [
                        'some' => 1,
                    ],
                    [
                        'some' => 2,
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function it_builds_expected_oauth_uri()
    {
        // No default oauth configs, so set some for test
        $this->configs['oauth']['id'] = 'client_id';
        $this->configs['oauth']['secret'] = 'client_secret';

        $this->client->setConfigs($this->configs);

        $this->assertEquals(
            'https://app.clickup.com/api?client_id=client_id&redirect_uri=http%3A%2F%2Fhost%2Fredirect%2Furi',
            $this->client->oauthUri('http://host/redirect/uri')
        );
    }

    /**
     * @test
     */
    public function it_will_swap_oauth_code_for_token()
    {
        // No default oauth configs, so set some for test
        $this->configs['oauth']['id'] = 'client_id';
        $this->configs['oauth']['secret'] = 'client_secret';

        $this->client->setConfigs($this->configs);

        $this->stream_interface_mock = Mockery::mock(StreamInterface::class);
        $this->stream_interface_mock->shouldReceive('getContents')
                                    ->withNoArgs()
                                    ->andReturn(
                                        json_encode(
                                            [
                                                'access_token' => 'oauth_token',
                                            ]
                                        )
                                    );

        $this->response_mock = Mockery::mock(ResponseInterface::class);
        $this->response_mock->shouldReceive('getBody')
                            ->withNoArgs()
                            ->andReturn($this->stream_interface_mock);

        $this->guzzle_mock->shouldReceive('request')
                          ->once()
                          ->withArgs(
                              [
                                  'POST',
                                  $this->configs['url'] .
                                  '/oauth/token?client_id=client_id&client_secret=client_secret&code=oauth_code',
                                  [
                                      'headers' => [
                                          'Content-Type' => 'application/json',
                                      ],
                                  ],
                              ]
                          )
                          ->andReturn($this->response_mock);

        $this->assertEquals('oauth_token', $this->client->oauthRequestTokenUsingCode('oauth_code'));
    }

    /**
     * @test
     */
    public function it_raises_exception_when_guzzle_error_while_getting_token()
    {
        $this->expectException(GuzzleException::class);

        $this->guzzle_mock->shouldReceive('request')
                          ->once()
                          ->withAnyArgs()
                          ->andThrow(new InvalidArgumentException());

        $this->client->oauthRequestTokenUsingCode('oauth_code');
    }
}
