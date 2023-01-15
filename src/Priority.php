<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Model;

/**
 * Class Priority
 *
 *
 * @property int $id
 * @property string $priority
 * @property string $color
 * @property float $orderindex
 */
class Priority extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'orderindex' => 'float',
    ];

    /**
     * Is the model readonly?
     *
     * @var bool
     */
    protected $readonlyModel = true;
}
