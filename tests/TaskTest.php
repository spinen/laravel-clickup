<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Relations\BelongsTo;
use Spinen\ClickUp\Support\Relations\ChildOf;
use Spinen\ClickUp\Support\Relations\HasMany;

/**
 * Class TaskTest
 */
class TaskTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Task::class, new Task());
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
    public function it_casts_assignees_to_a_collection()
    {
        $this->model->assignees = [];

        $this->assertInstanceOf(Collection::class, $this->model->assignees);
    }

    /**
     * @test
     */
    public function it_casts_checklists_to_a_collection()
    {
        $this->model->checklists = [];

        $this->assertInstanceOf(Collection::class, $this->model->checklists);
    }

    /**
     * @test
     */
    public function it_casts_creator_to_a_member()
    {
        $this->model->creator = [];

        $this->assertInstanceOf(Member::class, $this->model->creator);
    }

    /**
     * @test
     */
    public function it_casts_custom_fields_to_a_collection()
    {
        $this->model->custom_fields = [];

        $this->assertInstanceOf(Collection::class, $this->model->custom_fields);
    }

    /**
     * @test
     */
    public function it_casts_folder_to_a_folder()
    {
        $this->model->folder = [];

        $this->assertInstanceOf(Folder::class, $this->model->folder);
    }

    /**
     * @test
     */
    public function it_casts_priority_to_a_priority()
    {
        $this->model->priority = [];

        $this->assertInstanceOf(Priority::class, $this->model->priority);
    }

    /**
     * @test
     */
    public function it_casts_project_to_a_project()
    {
        $this->model->project = [];

        $this->assertInstanceOf(Project::class, $this->model->project);
    }

    /**
     * @test
     */
    public function it_casts_space_to_a_space()
    {
        $this->model->space = [];

        $this->assertInstanceOf(Space::class, $this->model->space);
    }

    /**
     * @test
     */
    public function it_casts_status_to_a_status()
    {
        $this->model->status = [];

        $this->assertInstanceOf(Status::class, $this->model->status);
    }

    /**
     * @test
     */
    public function it_casts_tags_to_a_collection()
    {
        $this->model->tags = [];

        $this->assertInstanceOf(Collection::class, $this->model->tags);
    }

    /**
     * @test
     */
    public function it_a_child_of_list_if_parent_is_a_list()
    {
        $this->model->parentModel = new TaskList();

        $this->assertInstanceOf(ChildOf::class, $this->model->list());
    }

    /**
     * @test
     */
    public function it_is_not_a_child_of_list_if_parent_is_not_a_list()
    {
        $this->model->parentModel = null;

        $this->assertNull($this->model->list());
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
    public function it_a_child_of_team_if_parent_is_a_team()
    {
        $this->model->parentModel = new Team();

        $this->assertInstanceOf(ChildOf::class, $this->model->team());
    }

    /**
     * @test
     */
    public function it_is_not_a_child_of_team_if_parent_is_not_a_team()
    {
        $this->model->parentModel = null;

        $this->assertNull($this->model->team());
    }

    /**
     * @test
     */
    public function it_has_many_times()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->times());
    }

    /**
     * @test
     */
    public function it_belongs_to_a_view()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->model->view());
    }
}
