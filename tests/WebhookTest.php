<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Support\Relations\BelongsTo;

/**
 * Class WebhookTest
 */
class WebhookTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(Webhook::class, new Webhook());
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
    public function it_belongs_to_a_list()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->model->list());
    }

    /**
     * @test
     */
    public function it_belongs_to_a_space()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->model->space());
    }

    /**
     * @test
     */
    public function it_belongs_to_a_team()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->model->team());
    }

    /**
     * @test
     */
    public function it_belongs_to_a_user()
    {
        $this->assertInstanceOf(BelongsTo::class, $this->model->user());
    }
}
