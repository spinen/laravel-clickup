<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Relations\ChildOf;

/**
 * Class FieldTest
 */
class FieldTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Field::class, $this->model);
    }

    /**
     * @test
     */
    public function it_is_a_child_of_list()
    {
        $this->assertInstanceOf(ChildOf::class, $this->model->list());
    }
}
