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
     */
    protected array $configs;

    /**
     * Guzzle instance
     */
    protected Guzzle $guzzle;

    /**
     * API Token
     */
    protected ?string $token;

    /**
     * Client constructor.
     */
    public function __construct(array $configs, Guzzle $guzzle, ?string $token = null)
    {
        $this->setConfigs($configs);
        $this->guzzle = $guzzle;
        $this->setToken($token);
    }

    /**
     * Shortcut to 'DELETE' request
     *
     * @throws GuzzleException
     * @throws TokenException
     */
    public function delete(string $path): ?array
    {
        return $this->request($path, [], 'DELETE');
    }

    /**
     * Shortcut to 'GET' request
     *
     * @throws GuzzleException
     * @throws TokenException
     */
    public function get(string $path): ?array
    {
        return $this->request($path, [], 'GET');
    }

    /**
     * Convert OAuth code to token for user
     *
     * @throws GuzzleException
     */
    public function oauthRequestTokenUsingCode(string $code): string
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
     */
    public function oauthUri(string $url): string
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
     * @throws GuzzleException
     * @throws TokenException
     */
    public function post(string $path, array $data): ?array
    {
        return $this->request($path, $data, 'POST');
    }

    /**
     * Shortcut to 'PUT' request
     *
     * @throws GuzzleException
     * @throws TokenException
     */
    public function put(string $path, array $data): ?array
    {
        return $this->request($path, $data, 'PUT');
    }

    /**
     * Make an API call to ClickUp
     *
     * @throws GuzzleException
     * @throws TokenException
     */
    public function request(?string $path, ?array $data = [], ?string $method = 'GET'): ?array
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
     */
    public function setConfigs(array $configs): self
    {
        // Replace empty strings with nulls in config values
        $this->configs = array_map(fn ($v) => $v === '' ? null : $v, $configs);

        return $this;
    }

    /**
     * Set the token
     */
    public function setToken(?string $token): self
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
     */
    public function uri(?string $path = null, ?string $url = null): string
    {
        $path = ltrim($path ?? '/', '/');

        return rtrim($url ?? $this->configs['url'] ?? 'https://api.clickup.com/api/v2', '/')
            .($path ? (Str::startsWith($path, '?') ? null : '/').$path : '/');
    }
}
