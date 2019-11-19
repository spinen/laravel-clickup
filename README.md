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

// TODO: Add regular PHP examples

## Usage

### Supported Actions

The primary class is `Spinen\ClickUp\Client`.  It gets constructed with 3 parameters...

* `array $configs` - Configuration properties.  See the `clickup.php` file in the `./config` directory for a documented list of options.

* `Guzzle $guzzle` - Instance of `GuzzleHttp\Client`

* `Token $token` - _[Optional]_ String of the user's token

Once you new up a `Client` instance, you have the following methods...

* `delete($path)` - Shortcut to the `request()` method with 'DELETE' as the last parameter

* `get($path)` - Shortcut to the `request()` method with 'GET' as the last parameter

* `post($path, array $data)` - Shortcut to the `request()` method with 'POST' as the last parameter

* `put($path, array $data)` - Shortcut to the `request()` method with 'PUT' as the last parameter

* `request($path, $data = [], $method = 'GET')` - Make an [API call to ClickUp](https://clickup.com/api) to `$path` with the `$data` using the JWT for the logged in user.

* `setConfigs(array $configs)` - Allow overriding the `$configs` on the `Client` instance.

* `setToken($token)` - Set the token for the ClickUp API

* `uri($path = null)` - Generate a full uri for the path to the ClickUp API.

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
>>> $user->clickup_token = 'your_token_here';
=> "your_token_here"
>>> $user->save();
=> true
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
// TODO: Address advanced calls, like the API's Get Filtered Team Tasks. /team/team_id/task?...
```php
>>> $team->tasks()->where('space_ids', ['space_id_1', 'space_id_2'])->where('assignees', ['assignee1', 'assignee2'])->get()->count();
=> 100
// If there are more than 100 results, they will be paginated. Pass in another parameter to get another page:
>>> $team->tasks()->where....->where('page', 2)->get();
```


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
