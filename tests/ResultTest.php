<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class ResultTest
 */
class ResultTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Result::class, new Result());
    }

    /**
     * @test
     */
    public function it_casts_owners_to_a_collection()
    {
        $this->model->owners = [];

        $this->assertInstanceOf(Collection::class, $this->model->owners);
    }

    /**
     * @test
     */
    public function it_is_a_child_of_goal()
    {
        $this->assertInstanceOf(ChildOf::class, $this->model->goal());
    }
}
