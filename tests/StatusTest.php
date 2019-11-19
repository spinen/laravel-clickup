<?php

namespace Spinen\ClickUp;

/**
 * Class StatusTest
 *
 * @package Spinen\ClickUp
 */
class StatusTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Status::class, new Status());
    }
}
