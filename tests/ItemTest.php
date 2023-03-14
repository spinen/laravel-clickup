<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Collection;

/**
 * Class ItemTest
 */
class ItemTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Item::class, new Item());
    }

    /**
     * @test
     */
    public function it_cast_assignee_to_a_member()
    {
        $this->assertInstanceOf(Member::class, $this->model->assignee);
    }

    /**
     * @test
     */
    public function it_cast_children_to_a_collection()
    {
        $this->model->children = [];

        $this->assertInstanceOf(Collection::class, $this->model->children);
    }
}
