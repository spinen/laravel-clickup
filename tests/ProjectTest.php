<?php

namespace Spinen\ClickUp;

/**
 * Class ProjectTest
 */
class ProjectTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Project::class, new Project());
    }
}
