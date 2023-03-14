<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Relations\ChildOf;
use Spinen\ClickUp\Support\Relations\HasMany;

/**
 * Class TaskListTest
 */
class TaskListTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(TaskList::class, new TaskList());
    }

    /**
     * @test
     */
    public function it_has_many_comments()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->comments());
    }

    /**
     * @test
     */
    public function it_has_many_fields()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->fields());
    }

    /**
     * @test
     */
    public function it_a_child_of_folder_if_parent_is_a_folder()
    {
        $this->model->parentModel = new Folder();

        $this->assertInstanceOf(ChildOf::class, $this->model->folder());
    }

    /**
     * @test
     */
    public function it_is_not_a_child_of_folder_if_parent_is_not_a_folder()
    {
        $this->model->parentModel = null;

        $this->assertNull($this->model->folder());
    }

    /**
     * @test
     */
    public function it_cast_assignee_to_a_member()
    {
        $this->model->assignee = [];

        $this->assertInstanceOf(Member::class, $this->model->assignee);
    }

    /**
     * @test
     */
    public function it_cast_priority_to_a_priority()
    {
        $this->model->priority = [];

        $this->assertInstanceOf(Priority::class, $this->model->priority);
    }

    /**
     * @test
     */
    public function it_cast_status_to_a_status()
    {
        $this->model->status = [];

        $this->assertInstanceOf(Status::class, $this->model->status);
    }

    /**
     * @test
     */
    public function it_cast_statuses_to_a_collection()
    {
        $this->model->statuses = [];

        $this->assertInstanceOf(Collection::class, $this->model->statuses);
    }

    /**
     * @test
     */
    public function it_has_many_members()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->members());
    }

    /**
     * @test
     */
    public function it_a_child_of_space_if_parent_is_a_space()
    {
        $this->model->parentModel = new Space();

        $this->assertInstanceOf(ChildOf::class, $this->model->space());
    }

    /**
     * @test
     */
    public function it_is_not_a_child_of_space_if_parent_is_not_a_space()
    {
        $this->model->parentModel = null;

        $this->assertNull($this->model->space());
    }

    /**
     * @test
     */
    public function it_has_many_tasks()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->tasks());
    }

    /**
     * @test
     */
    public function it_has_many_task_templates()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->taskTemplates());
    }

    /**
     * @test
     */
    public function it_has_many_views()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->views());
    }
}
