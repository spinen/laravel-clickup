<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Model;

/**
 * Class User
 *
 *
 * @property bool $global_font_support
 * @property int $id
 * @property int $week_start_day
 * @property string $color
 * @property string $email
 * @property string $initials
 * @property string $profilePicture
 * @property string $user
 * @property string $username
 */
class User extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'global_font_support' => 'boolean',
        'id' => 'integer',
        'week_start_day' => 'integer',
    ];

    /**
     * Path to API endpoint.
     *
     * @var string
     */
    protected $path = '/user';

    /**
     * Is the model readonly?
     *
     * @var bool
     */
    protected $readonlyModel = true;
}
