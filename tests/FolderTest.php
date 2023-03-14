<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Collection;
use Spinen\ClickUp\Support\Relations\ChildOf;
use Spinen\ClickUp\Support\Relations\HasMany;

/**
 * Class FolderTest
 */
class FolderTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Folder::class, $this->model);
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
    public function it_cast_lists_to_a_collection()
    {
        $this->model->lists = [];

        $this->assertInstanceOf(Collection::class, $this->model->lists);
    }

    /**
     * @test
     */
    public function it_is_a_child_of_space()
    {
        $this->assertInstanceOf(ChildOf::class, $this->model->space());
    }

    /**
     * @test
     */
    public function it_has_many_views()
    {
        $this->assertInstanceOf(HasMany::class, $this->model->views());
    }
}
