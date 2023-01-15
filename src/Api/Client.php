<?php

namespace Spinen\ClickUp\Api;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;
use Spinen\ClickUp\Exceptions\TokenException;

/**
 * Class Client
 */
class Client
{
    /**
     * Configs for the client
     *
     * @var array
     */
    protected $configs;

    /**
     * Guzzle instance
     *
     * @var Guzzle
     */
    protected $guzzle;

    /**
     * API Token
     *
     * @var string
     */
    protected $token;

    /**
     * Client constructor.
     *
     * @param  string|null  $token
     */
    public function __construct(array $configs, Guzzle $guzzle, $token = null)
    {
        $this->setConfigs($configs);
        $this->guzzle = $guzzle;
        $this->setToken($token);
    }

    /**
     * Shortcut to 'DELETE' request
     *
     * @param  string  $path
     *
     * @throws GuzzleException
     * @throws TokenException
     */
    public function delete($path): ?array
    {
        return $this->request($path, [], 'DELETE');
    }

    /**
     * Shortcut to 'GET' request
     *
     * @param  string  $path
     *
     * @throws GuzzleException
     * @throws TokenException
     */
    public function get($path): ?array
    {
        return $this->request($path, [], 'GET');
    }

    /**
     * Convert OAuth code to token for user
     *
     * @param  string  $code
     *
     * @throws GuzzleException
     */
    public function oauthRequestTokenUsingCode($code): string
    {
        $path = 'oauth/token?'.http_build_query(
            [
                'client_id' => $this->configs['oauth']['id'],
                'client_secret' => $this->configs['oauth']['secret'],
                'code' => $code,
            ]
        );

        try {
            return json_decode(
                $this->guzzle->request(
                    'POST',
                    $this->uri($path),
                    [
                        'headers' => [
                            'Content-Type' => 'application/json',
                        ],
                    ]
                )
                             ->getBody()
                             ->getContents(),
                true
            )['access_token'];
        } catch (GuzzleException $e) {
            // TODO: Figure out what to do with this error
            // TODO: Consider returning [] for 401's?

            throw $e;
        }
    }

    /**
     * Build the uri to redirect the user to start the OAuth process
     *
     * @param  string  $url
     */
    public function oauthUri($url): string
    {
        return $this->uri(
            '?'.http_build_query(
                [
                    'client_id' => $this->configs['oauth']['id'],
                    'redirect_uri' => $url,
                ]
            ),
            $this->configs['oauth']['url']
        );
    }

    /**
     * Shortcut to 'POST' request
     *
     * @param  string  $path
     *
     * @throws GuzzleException
     * @throws TokenException
     */
    public function post($path, array $data): ?array
    {
        return $this->request($path, $data, 'POST');
    }

    /**
     * Shortcut to 'PUT' request
     *
     * @param  string  $path
     *
     * @throws GuzzleException
     * @throws TokenException
     */
    public function put($path, array $data): ?array
    {
        return $this->request($path, $data, 'PUT');
    }

    /**
     * Make an API call to ClickUp
     *
     * @param  string  $path
     * @param  array|null  $data
     * @param  string|null  $method
     *
     * @throws GuzzleException
     * @throws TokenException
     */
    public function request($path, $data = [], $method = 'GET'): ?array
    {
        if (! $this->token) {
            throw new TokenException('Must set token before making a request');
        }

        try {
            return json_decode(
                $this->guzzle->request(
                    $method,
                    $this->uri($path),
                    [
                        'headers' => [
                            'Authorization' => $this->token,
                            'Content-Type' => 'application/json',
                        ],
                        'body' => empty($data) ? null : json_encode($data),
                    ]
                )
                             ->getBody()
                             ->getContents(),
                true
            );
        } catch (GuzzleException $e) {
            // TODO: Figure out what to do with this error
            // TODO: Consider returning [] for 401's?

            throw $e;
        }
    }

    /**
     * Set the configs
     *
     *
     * @return $this
     */
    public function setConfigs(array $configs): self
    {
        // Replace empty strings with nulls in config values
        $this->configs = array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $configs);

        return $this;
    }

    /**
     * Set the token
     *
     * @param  string  $token
     * @return $this
     */
    public function setToken($token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * URL to ClickUp
     *
     * If path is passed in, then append it to the end. By default, it will use the url
     * in the configs, but if a url is passed in as a second parameter then it is used.
     * If no url is found it will use the hard-coded v2 ClickUp API URL.
     *
     * @param  string|null  $path
     * @param  string|null  $url
     */
    public function uri($path = null, $url = null): string
    {
        $url = $url ?? $this->configs['url'] ?? 'https://api.clickup.com/api/v2';

        return rtrim($url, '/').(Str::startsWith($path, '?') ? null : '/').ltrim($path, '/');
    }
}
