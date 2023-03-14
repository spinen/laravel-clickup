<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Relations\HasMany;

/**
 * Class TeamTest
 */
class TeamTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Team::class, new Team());
    }

    /**
     * @test
     */
    public function it_cast_members_to_a_collection()
    {
        $this->model->members = [];

        $this->assertInstanceOf(Collection::class, $this->model->members);
    }

    /**
     * @test
     */
    public function it_has_many_goals()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->goals());
    }

    /**
     * @test
     */
    public function it_has_many_shares()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->shares());
    }

    /**
     * @test
     */
    public function it_has_many_spaces()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->spaces());
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

    /**
     * @test
     */
    public function it_has_many_webhooks()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->webhooks());
    }
}
