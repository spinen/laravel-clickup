<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Relations\BelongsTo;
use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class GoalTest
 */
class GoalTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Goal::class, new Goal());
    }

    /**
     * @test
     */
    public function it_cast_owners_to_a_collection()
    {
        $this->model->owners = [];

        $this->assertInstanceOf(Collection::class, $this->model->owners);
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
    public function it_belongs_to_a_folder()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->model->folder());
    }

    /**
     * @test
     */
    public function it_is_a_child_of_team()
    {
        $this->assertInstanceOf(ChildOf::class, $this->model->team());
    }
}
