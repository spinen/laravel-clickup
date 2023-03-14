<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class TimeTest
 */
class TimeTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Time::class, new Time());
    }

    /**
     * @test
     */
    public function it_cast_intervals_to_a_collection()
    {
        $this->model->intervals = [];

        $this->assertInstanceOf(Collection::class, $this->model->intervals);
    }

    /**
     * @test
     */
    public function it_cast_user_to_a_member()
    {
        $this->model->user = [];

        $this->assertInstanceOf(Member::class, $this->model->user);
    }

    /**
     * @test
     */
    public function it_is_a_child_of_task()
    {
        $this->assertInstanceOf(ChildOf::class, $this->model->task());
    }
}
