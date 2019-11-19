<?php

namespace Spinen\ClickUp;

/**
 * Class PriorityTest
 *
 * @package Spinen\ClickUp
 */
class PriorityTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Priority::class, new Priority());
    }
}
