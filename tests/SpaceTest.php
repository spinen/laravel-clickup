<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Relations\ChildOf;
use Spinen\ClickUp\Support\Relations\HasMany;

/**
 * Class SpaceTest
 */
class SpaceTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Space::class, new Space());
    }

    /**
     * @test
     */
    public function it_casts_members_to_a_collection()
    {
        $this->model->members = [];

        $this->assertInstanceOf(Collection::class, $this->model->members);
    }

    /**
     * @test
     */
    public function it_casts_statuses_to_a_collection()
    {
        $this->model->statuses = [];

        $this->assertInstanceOf(Collection::class, $this->model->statuses);
    }

    /**
     * @test
     */
    public function it_has_many_folders()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->folders());
    }

    /**
     * @test
     */
    public function it_has_many_lists()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->lists());
    }

    /**
     * @test
     */
    public function it_has_many_tags()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->tags());
    }

    /**
     * @test
     */
    public function it_is_a_child_of_team()
    {
        $this->assertInstanceOf(ChildOf::class, $this->model->team());
    }

    /**
     * @test
     */
    public function it_has_many_views()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->views());
    }
}
