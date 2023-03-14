<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class BuilderTest
 */
class ChecklistTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Checklist::class, $this->model);
    }

    /**
     * @test
     */
    public function it_casts_items_to_a_collection()
    {
        $this->model->items = [];

        $this->assertInstanceOf(Collection::class, $this->model->items);
    }

    /**
     * @test
     */
    public function it_is_a_child_of_task()
    {
        $this->assertInstanceOf(ChildOf::class, $this->model->task());
    }
}
