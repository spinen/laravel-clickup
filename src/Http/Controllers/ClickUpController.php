<?php

namespace Spinen\ClickUp\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Spinen\ClickUp\Api\Client as ClickUp;

/**
 * Class ClickUpController
 */
class ClickUpController extends Controller
{
    /**
     * Process the code returned for the user & save as clickup_token
     *
     * @throws GuzzleException
     */
    public function processCode(ClickUp $clickup, Redirector $redirector, Request $request, User $user): RedirectResponse
    {
        // TODO: Deal with empty code
        $user->clickup_token = $clickup->oauthRequestTokenUsingCode((string) $request->get('code'));

        $user->save();

        return $redirector->intended();
    }
}
