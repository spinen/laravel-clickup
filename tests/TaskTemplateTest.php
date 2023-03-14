<?php

namespace Spinen\ClickUp;

/**
 * Class TaskTemplateTest
 */
class TaskTemplateTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(TaskTemplate::class, new TaskTemplate());
    }
}
