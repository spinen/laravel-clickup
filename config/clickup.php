<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ClickUp OAuth
    |--------------------------------------------------------------------------
    |
    | ID & secret to make OAuth requests
    |
    */
    'oauth' => [
        'id' => env('CLICKUP_CLIENT_ID'),

        'secret' => env('CLICKUP_CLIENT_SECRET'),

        'url' => env('CLICKUP_OAUTH_URL', 'https://app.clickup.com/api'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Route
    |--------------------------------------------------------------------------
    |
    | Setting for the routing
    |
    */
    'route' => [
        'enabled' => true,

        'middleware' => ['web'],

        'sso' => 'clickup/sso',
    ],

    /*
    |--------------------------------------------------------------------------
    | ClickUp URL
    |--------------------------------------------------------------------------
    |
    | The URL to the ClickUp server
    |
    */
    'url' => env('CLICKUP_URL', 'https://api.clickup.com/api/v2'),
];
