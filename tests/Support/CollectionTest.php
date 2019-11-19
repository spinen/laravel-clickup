<?php

namespace Spinen\ClickUp\Support;

use Spinen\ClickUp\TestCase;

/**
 * Class CollectionTestTest
 *
 * @package Spinen\ClickUp\Support
 */
class CollectionTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Collection::class, new Collection());
    }
}
