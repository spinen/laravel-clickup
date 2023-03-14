<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::get(rtrim(Config::get('clickup.route.sso', '/clickup/sso'), '/').'/{user}', 'ClickUpController@processCode')
     ->name('clickup.sso.redirect_url');
