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
     * Create a new ClickUp filter middleware instance.
     */
    public function __construct(
        protected ClickUp $clickup,
        protected Redirector $redirector,
        protected UrlGenerator $url_generator
    ) {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()->clickup_token) {
            // Set intended route, so that after linking account, user is put where they were going
            $this->redirector->setIntendedUrl($request->path());

            return $this->redirector->to(
                $this->clickup->oauthUri((string) $this->url_generator->route('clickup.sso.redirect_url', $request->user()))
            );
        }

        return $next($request);
    }
}
