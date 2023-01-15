<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Model;

/**
 * Class Project
 *
 *
 * @property int $id
 * @property string $name
 * @property bool $hidden
 * @property bool $access
 */
class Project extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'hidden' => 'boolean',
        'access' => 'boolean',
    ];

    /**
     * Is the model readonly?
     *
     * @var bool
     */
    protected $readonlyModel = true;
}
