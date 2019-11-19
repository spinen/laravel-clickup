<?php

return [

    'client' => [
// TODO: Add these back when supporting OAUTH connections
//        'id' => env('CLICKUP_CLIENT_ID'),
//
//        'secret' => env('CLICKUP_CLIENT_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | ClickUp URL
    |--------------------------------------------------------------------------
    |
    | The URL to the ClickUp server
    |
    */
    'url'    => env('CLICKUP_URL', 'https://api.clickup.com/api/v2'),

];
