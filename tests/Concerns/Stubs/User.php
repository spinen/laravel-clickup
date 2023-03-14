<?php

namespace Spinen\ClickUp\Concerns\Stubs;

use Spinen\ClickUp\Concerns\HasClickUp;

class User
{
    use HasClickUp;

    public $attributes = [
        'clickup_token' => 'encrypted',
    ];

    public $fillable = [];

    public $hidden = [];

    /**
     * @var string
     */
    protected $clickup_token = 'pk_token';

    public function getBuilder()
    {
        return $this->builder;
    }
}
