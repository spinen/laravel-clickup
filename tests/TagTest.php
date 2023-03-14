<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class TagTest
 */
class TagTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Tag::class, new Tag());
    }

    /**
     * @test
     */
    public function it_is_a_child_of_space()
    {
        $this->assertInstanceOf(ChildOf::class, $this->model->space());
    }
}
