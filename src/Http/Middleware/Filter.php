<?php

namespace Spinen\ClickUp\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Spinen\ClickUp\Api\Client as ClickUp;

/**
 * Class Filter
 */
class Filter
{
    /**
     * The ClickUp client instance.
     *
     * @var ClickUp
     */
    protected $clickup;

    /**
     * The redirector instance.
     *
     * @var Redirector
     */
    protected $redirector;

    /**
     * The UrlGenerator instance.
     *
     * @var UrlGenerator
     */
    protected $url_generator;

    /**
     * Create a new ClickUp filter middleware instance.
     */
    public function __construct(ClickUp $clickup, Redirector $redirector, UrlGenerator $url_generator)
    {
        $this->clickup = $clickup;
        $this->redirector = $redirector;
        $this->url_generator = $url_generator;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request Request
     * @param  Closure  $next Closure
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()->clickup_token) {
            // Set intended route, so that after linking account, user is put where they were going
            $this->redirector->setIntendedUrl($request->path());

            return $this->redirector->to(
                $this->clickup->oauthUri($this->url_generator->route('clickup.sso.redirect_url', $request->user()))
            );
        }

        return $next($request);
    }
}
