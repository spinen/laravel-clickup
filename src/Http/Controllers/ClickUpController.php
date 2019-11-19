<?php

namespace Spinen\ClickUp\Http\Controllers;

use App\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Spinen\ClickUp\Api\Client as ClickUp;

/**
 * Class ClickUpController
 *
 * @package Spinen\ClickUp\Http\Controllers
 */
class ClickUpController extends Controller
{
    /**
     * Process the code returned for the user & save as clickup_token
     *
     * @param ClickUp $clickup
     * @param Redirector $redirector
     * @param Request $request
     * @param User $user
     *
     * @return RedirectResponse
     * @throws GuzzleException
     */
    public function processCode(ClickUp $clickup, Redirector $redirector, Request $request, User $user)
    {
        $user->clickup_token = $clickup->oauthRequestTokenUsingCode($request->get('code'));

        $user->save();

        return $redirector->intended();
    }
}
