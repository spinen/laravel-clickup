<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class MemberTest
 */
class MemberTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Member::class, new Member());
    }

    /**
     * @test
     */
    public function it_a_child_of_task_list_if_parent_is_a_task_list()
    {
        $this->model->parentModel = new TaskList();

        $this->assertInstanceOf(ChildOf::class, $this->model->list());
    }

    /**
     * @test
     */
    public function it_is_not_a_child_of_task_list_if_parent_is_not_a_task_list()
    {
        $this->model->parentModel = null;

        $this->assertNull($this->model->list());
    }

    /**
     * @test
     */
    public function it_a_child_of_task_if_parent_is_a_task()
    {
        $this->model->parentModel = new Task();

        $this->assertInstanceOf(ChildOf::class, $this->model->task());
    }

    /**
     * @test
     */
    public function it_is_not_a_child_of_task_if_parent_is_not_a_task()
    {
        $this->model->parentModel = null;

        $this->assertNull($this->model->task());
    }
}
