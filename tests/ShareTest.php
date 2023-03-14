<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class ShareTest
 */
class ShareTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Share::class, new Share());
    }

    /**
     * @test
     */
    public function it_casts_folders_to_a_collection()
    {
        $this->model->folders = [];

        $this->assertInstanceOf(Collection::class, $this->model->folders);
    }

    /**
     * @test
     */
    public function it_casts_lists_to_a_collection()
    {
        $this->model->lists = [];

        $this->assertInstanceOf(Collection::class, $this->model->lists);
    }

    /**
     * @test
     */
    public function it_casts_tasks_to_a_collection()
    {
        $this->model->tasks = [];

        $this->assertInstanceOf(Collection::class, $this->model->tasks);
    }

    /**
     * @test
     */
    public function it_is_a_child_of_team()
    {
        $this->assertInstanceOf(ChildOf::class, $this->model->team());
    }
}
