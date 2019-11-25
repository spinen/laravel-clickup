# SPINEN's Laravel ClickUp

[![Latest Stable Version](https://poser.pugx.org/spinen/laravel-clickup/v/stable)](https://packagist.org/packages/spinen/laravel-clickup)
[![Latest Unstable Version](https://poser.pugx.org/spinen/laravel-clickup/v/unstable)](https://packagist.org/packages/spinen/laravel-clickup)
[![Total Downloads](https://poser.pugx.org/spinen/laravel-clickup/downloads)](https://packagist.org/packages/spinen/laravel-clickup)
[![License](https://poser.pugx.org/spinen/laravel-clickup/license)](https://packagist.org/packages/spinen/laravel-clickup)

PHP package to interface with [ClickUp](https://clickup.com).

We solely use [Laravel](https://www.laravel.com) for our applications, so this package is written with Laravel in mind. We have tried to make it work outside of Laravel. If there is a request from the community to split this package into 2 parts, then we will consider doing that work.

## Build Status

| Branch | Status | Coverage | Code Quality |
| ------ | :----: | :------: | :----------: |
| Develop | [![Build Status](https://travis-ci.org/spinen/laravel-clickup.svg?branch=develop)](https://travis-ci.org/spinen/laravel-clickup) | [![Code Coverage](https://scrutinizer-ci.com/g/spinen/laravel-clickup/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/spinen/laravel-clickup/?branch=develop) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/spinen/laravel-clickup/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/spinen/laravel-clickup/?branch=develop) |
| Master | [![Build Status](https://travis-ci.org/spinen/laravel-clickup.svg?branch=master)](https://travis-ci.org/spinen/laravel-clickup) | [![Code Coverage](https://scrutinizer-ci.com/g/spinen/laravel-clickup/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/spinen/laravel-clickup/?branch=master) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/spinen/laravel-clickup/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/spinen/laravel-clickup/?branch=master) |

## Table of Contents
 * [Installation](#installation)
 * [Laravel Setup](#laravel-setup)
    * [Configuration](#configuration)
        * [Optional Keys](#optional-keys)
 * [Generic PHP Setup](#generic-php-setup)
    * [Examples](#examples)
 * [Authentication](#authentication)
    * [OAuth](#oauth)
    * [Personal Token](#personal-token)
 * [Usage](#usage)
    * [Supported Actions](#supported-actions)
    * [Using the Client](#using-the-client)
        * [Getting the Client object](#getting-the-client-object)
        * [Models](#models)
        * [Relationships](#relationships)
        * [Advanced filtering using "where"](#advanced-filtering-using-where)
    * [More Examples](#more-examples)
 * [Known Issues](#known-issues)

## Installation

Install ClickUp PHP Package via Composer:

```bash
$ composer require spinen/laravel-clickup
```

The package uses the [auto registration feature](https://laravel.com/docs/master/packages#package-discovery) of Laravel.

## Laravel Setup

### Configuration

1. You will need to make your `User` object implement includes the `Spinen\ClickUp\Concerns\HasClickUp` trait which will allow it to access the Client as an attribute like this: `$user->clickup`

    ```php
    <?php

    namespace App;

    use Illuminate\Contracts\Auth\MustVerifyEmail;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Spinen\ClickUp\Concerns\HasClickUp;

    class User extends Authenticatable
    {
        use HasClickUp, Notifiable;

        // ...
    }
    ```

2. Add the appropriate values to your ```.env``` file

    #### Optional Keys
    ```bash
    CLICKUP_CLIENT_ID=<Application ID, if using OAuth to generate user tokens>
    CLICKUP_CLIENT_SECRET=<Application Secret, if using OAuth to generate user tokens>
    CLICKUP_OAUTH_URL=<url to clickup OAuth flow, default is v2>
    CLICKUP_URL=<url to clickup API, default is v2>
    ```

3. _[Optional]_ Publish config & migration

    #### Config
    A configuration file named ```clickup.php``` can be published to ```config/``` by running...

    ```bash
    php artisan vendor:publish --tag=clickup-config
    ```

    #### Migration
    Migrations files can be published by running...

    ```bash
    php artisan vendor:publish --tag=clickup-migrations
    ```

    You'll need the migration to set the ClickUp API token on your `User` model.

## Generic PHP Setup

### Examples

To get a `Spinen\ClickUp\Api\Client` instance...

```bash
$ psysh
Psy Shell v0.9.9 (PHP 7.3.11 — cli) by Justin Hileman
>>> $configs = [
     "oauth" => [
       "id" => "<client_id>", // if using OAuth
       "secret" => "<client_secret>", // If using OAuth
       "url" => "https://app.clickup.com/api",
     ],
     "route" => [
       "enabled" => true,
       "middleware" => [
         "web",
       ],
       "sso" => "clickup/sso",
     ],
     "url" => "https://api.clickup.com/api/v2",
   ];
>>> $guzzle = new GuzzleHttp\Client();
=> GuzzleHttp\Client {#2379}
>>> $clickup = new Spinen\ClickUp\Api\Client($configs, $guzzle) // Optionally, pass the token as 3rd parameter
=> Spinen\ClickUp\Api\Client {#2363}
>>> $clickup->setToken('<a token>') // Skip if passed in via constructor
=> Spinen\ClickUp\Api\Client {#2363}
```

The `$clickup` instance will work exaclty like all of the examples below, so if you are not using Laravel, then you can use the package once you bootstrap the client.


## Authentication

ClickUp has 2 ways to authenticate when making API calls... 1) OAuth token or 2) Personal Token.  Either method uses a token that is saved to the `clickup_token` property on the `User` model.

### OAuth

There is a middleware named `clickup` that you can apply to any route that verifies that the user has a `clickup_token`, and if the user does not, then it redirects the user to ClickUp's OAuth page with the `client_id` where the user selectes the team(s) to link with your applciation.  Upon selecting the team(s), the user is rediected to `/clickup/sso/<user_id>?code=<OAuth Code>` where the system converts the `code` to a token & saves it to the user.  Upon saving the `clickup_token`, the user is redirected to the inital page that was proteted by the middleware.

> NOTE: You will need to have the `auth` middleware on the routes as the `User` is needed to see if there is a `clickup_token`.

If you do not want to use the `clickup` middleware to start the OAuth flow, then you can use the `oauthUri` on the `Client` to generate the link for the user...

```bash
$ php artisan tinker
Psy Shell v0.9.9 (PHP 7.3.11 — cli) by Justin Hileman
>>> $clickup = app(Spinen\ClickUp\Api\Client::class)
=> Spinen\ClickUp\Api\Client {#3035}
>>> $clickup->oauthUri(route('clickup.sso.redirect_url', <user_id>))
=> "https://app.clickup.com/api?client_id=<client_id>&redirect_uri=https%3A%2F%2F<your.host>2Fclickup%2Fsso%2F<user_id>"
>>>
```

> NOTE: At this time, there is not a way to remove a token that has been invalidated, so you will need to delete the `clickup_token` on the user to restart the flow.

### Personal Token

If you do not what to use the OAuth flow, then you can allow the user to provide you a personal token that you can save on the `User`.

```bash
$ php artisan tinker
Psy Shell v0.9.9 (PHP 7.3.11 — cli) by Justin Hileman
>>> $user = App\User::find(1)
=> App\User {#3040
     id: 1,
     first_name: "Bob",
     last_name: "Tester",
     email: "bob.tester@example.com",
     email_verified_at: null,
     created_at: "2019-11-15 19:49:01",
     updated_at: "2019-11-15 19:49:01",
     logged_in_at: "2019-11-15 19:49:01",
     deleted_at: null,
   }
>>> $user->clickup_token = '<personal token>';
=> "<personal token>"
>>> $user->save()
=> true
```

## Usage

### Supported Actions

The primary class is `Spinen\ClickUp\Client`.  It gets constructed with 3 parameters...

* `array $configs` - Configuration properties.  See the `clickup.php` file in the `./config` directory for a documented list of options.

* `Guzzle $guzzle` - Instance of `GuzzleHttp\Client`

* `Token $token` - _[Optional]_ String of the user's token

Once you new up a `Client` instance, you have the following methods...

* `delete($path)` - Shortcut to the `request()` method with 'DELETE' as the last parameter

* `get($path)` - Shortcut to the `request()` method with 'GET' as the last parameter

* `oauthRequestTokenUsingCode($code)` - Request a token from the OAuth code

* `oauthUri($url)` - Build the URI to the OAuth page with the redirect_url set to `$url`

* `post($path, array $data)` - Shortcut to the `request()` method with 'POST' as the last parameter

* `put($path, array $data)` - Shortcut to the `request()` method with 'PUT' as the last parameter

* `request($path, $data = [], $method = 'GET')` - Make an [API call to ClickUp](https://clickup.com/api) to `$path` with the `$data` using the JWT for the logged in user.

* `setConfigs(array $configs)` - Allow overriding the `$configs` on the `Client` instance.

* `setToken($token)` - Set the token for the ClickUp API

* `uri($path = null, $url = null)` - Generate a full uri for the path to the ClickUp API.

### Using the Client

The Client is meant to emulate [Laravel's models with Eloquent](https://laravel.com/docs/master/eloquent#retrieving-models). When working with ClickUp resources, you can access properties and relationships [just like you would in Laravel](https://laravel.com/docs/master/eloquent-relationships#querying-relations).

#### Getting the Client object

By running the migration included in this package, your `User` class will have a `clickup_token` column on it. When you set the user's token, it is encrypted in your database with [Laravel's encryption methods](https://laravel.com/docs/master/encryption#using-the-encrypter). After setting the ClickUp API token, you can access the Client object through `$user->clickup`.

```php
$ php artisan tinker
Psy Shell v0.9.9 (PHP 7.2.19 — cli) by Justin Hileman
>>> $user = User::find(1);
=> App\User {#3631
     id: 1,
     first_name: "Bob",
     last_name: "Tester",
     email: "bob.tester@example.com",
     email_verified_at: null,
     created_at: "2019-11-15 19:49:01",
     updated_at: "2019-11-15 19:49:01",
     logged_in_at: "2019-11-15 19:49:01",
     deleted_at: null,
   }
>>> // NOTE: Must have a clickup_token via one of the 2 ways in the Authentication section
>>> $user->clickup;
=> Spinen\ClickUp\Api\Client {#3635}
```

#### Models

The API responses are cast into models with the properties cast into the types as defined in the [ClickUp API documentation](https://clickup.com/api).  You can review the models in the `src/` folder.  There is a property named `casts` on each model that instructs the Client on how to cast the properties from the API response.  If the `casts` property is empty, then the properties are not defined in the API docs, so an array is returned.

```php
>>> $team = $user->clickUp()->teams->first();
=> Spinen\ClickUp\Team {#3646
     +exists: true,
     +incrementing: false,
     +parentModel: null,
     +timestamps: false,
   }
>>> $team->toArray(); // Calling toArray() is allowed just like in Laravel
=> [
     "id" => <7 digit ClickUp ID>,
     "name" => "SPINEN",
     "color" => "#2980B9",
     "avatar" => <URL to avatar>,
     "members" => [
       [
    // Keeps going
```

#### Relationships

Some of the responses have links to the related resources.  If a property has a relationship, you can call it as a method and the additional calls are automatically made & returned.  The value is stored in place of the original data, so once it is loaded it is cached.

```php
$folder = $team->spaces->first()->folders->first();
=> Spinen\ClickUp\Folder {#3632
     +exists: true,
     +incrementing: false,
     +parentModel: Spinen\ClickUp\Space {#3658
       +exists: true,
       +incrementing: false,
       +parentModel: Spinen\ClickUp\Team {#3645
         +exists: true,
         +incrementing: false,
         +parentModel: null,
         +timestamps: false,
       },
       +timestamps: false,
     },
     +timestamps: false,
   }
>>> $folder->lists->count();
=> 5
>>> $folder->lists->first()->name;
=> "Test Folder"
```

You may also call these relationships as attributes, and the Client will return a `Collection` for you (just like Eloquent).

```php
>>> $folder->lists;
=> Spinen\ClickUp\Support\Collection {#3650
     all: [
       Spinen\ClickUp\TaskList {#3636
         +exists: true,
         +incrementing: false,
         +parentModel: Spinen\ClickUp\Space {#3658
           +exists: true,
           +incrementing: false,
           +parentModel: Spinen\ClickUp\Team {#3645
             +exists: true,
             +incrementing: false,
             +parentModel: null,
             +timestamps: false,
           },
           +timestamps: false,
         },
         +timestamps: false,
       },
       // Keeps going
```

#### Advanced filtering using "where"

You can do advanced filters by using `where` on the models

```php
>>> $team->tasks()->where('space_ids', ['space_id_1', 'space_id_2'])->where('assignees', ['assignee1', 'assignee2'])->get()->count();
=> 100
// If there are more than 100 results, they will be paginated. Pass in another parameter to get another page:
>>> $team->tasks()->where....->where('page', 2)->get();
```

> NOTE: The API has a page size of `100` records, so to get to the next page you use the `where` method... ```->where('page', 3)```

### More Examples

```php
>>> $team = $user->clickUp()->teams->first();
=> Spinen\ClickUp\Team {#3646
     +exists: true,
     +incrementing: false,
     +parentModel: null,
     +timestamps: false,
   }
>>> $first_space = $team->spaces->first();
=> Spinen\ClickUp\Space {#3695
     +exists: true,
     +incrementing: false,
     +parentModel: Spinen\ClickUp\Team {#3646
       +exists: true,
       +incrementing: false,
       +parentModel: null,
       +timestamps: false,
     },
     +timestamps: false,
   }
>>> $folder = $first_space->folders->first()->toArray();
=> [
     "id" => <7 digit ClickUp ID>,
     "name" => "Test folder",
     "orderindex" => 3.0,
     "override_statuses" => true,
     "hidden" => false,
     "task_count" => 79,
     "archived" => false,
     "lists" => [
         // Keeps going
```

## Known Issues

// TODO: Document known issues as we find them
