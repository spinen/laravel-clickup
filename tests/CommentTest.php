<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class CommentTest
 */
class CommentTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Comment::class, $this->model);
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
    public function it_cast_assigned_by_to_a_member()
    {
        $this->assertInstanceOf(Member::class, $this->model->assigned_by);
    }

    /**
     * @test
     */
    public function it_cast_user_to_a_member()
    {
        $this->assertInstanceOf(Member::class, $this->model->user);
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

    /**
     * @test
     */
    public function it_a_child_of_view_if_parent_is_a_view()
    {
        $this->model->parentModel = new View();

        $this->assertInstanceOf(ChildOf::class, $this->model->view());
    }

    /**
     * @test
     */
    public function it_is_not_a_child_of_view_if_parent_is_not_a_view()
    {
        $this->model->parentModel = null;

        $this->assertNull($this->model->view());
    }
}
