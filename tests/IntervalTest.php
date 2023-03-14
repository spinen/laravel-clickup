<?php

namespace Spinen\ClickUp;

/**
 * Class IntervalTest
 */
class IntervalTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Interval::class, new Interval());
    }
}
