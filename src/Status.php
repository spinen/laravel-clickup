<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Model;

/**
 * Class Status
 *
 *
 * @property string $status
 * @property string $color
 * @property float $orderindex
 * @property string $type
 */
class Status extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'orderindex' => 'float',
    ];

    /**
     * Is the model readonly?
     *
     * @var bool
     */
    protected $readonlyModel = true;
}
