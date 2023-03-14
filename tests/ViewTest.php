<?php

namespace Spinen\ClickUp;

use Spinen\ClickUp\Exceptions\InvalidRelationshipException;
use Spinen\ClickUp\Support\Relations\ChildOf;
use Spinen\ClickUp\Support\Relations\HasMany;

/**
 * Class ViewTest
 */
class ViewTest extends ModelCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(View::class, new View());
    }

    /**
     * @test
     */
    public function it_has_many_comments_if_type_is_conversation()
    {
        $this->model->type = 'conversation';

        $this->assertInstanceOf(HasMany::class, $this->model->comments());
    }

    /**
     * @test
     */
    public function it_rasises_exception_when_asking_for_comments_on_a_non_converstation_type()
    {
        $this->model->type = 'something other than conversation';

        $this->expectException(InvalidRelationshipException::class);

        $this->model->comments();
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
    public function it_cast_protected_by_to_a_member()
    {
        $this->model->protected_by = [];

        $this->assertInstanceOf(Member::class, $this->model->protected_by);
    }

    /**
     * @test
     */
    public function it_a_child_of_task_list_if_parent_is_a_task_list()
    {
        $this->model->parentModel = new TaskList();

        $this->assertInstanceOf(ChildOf::class, $this->model->list());
    }

    /**
     * @test
     */
    public function it_is_not_a_child_of_task_list_if_parent_is_not_a_task_list()
    {
        $this->model->parentModel = null;

        $this->assertNull($this->model->list());
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
    public function it_has_many_tasks_if_type_is_not_conversation_or_doc_or_embed()
    {
        $this->model->type = 'other';

        $this->assertInstanceOf(HasMany::class, $this->model->tasks());
    }

    /**
     * @test
     */
    public function it_rasises_exception_when_asking_for_tasks_on_a_converstation_type()
    {
        $this->model->type = 'conversation';

        $this->expectException(InvalidRelationshipException::class);

        $this->model->tasks();
    }

    /**
     * @test
     */
    public function it_rasises_exception_when_asking_for_tasks_on_a_doc_type()
    {
        $this->model->type = 'doc';

        $this->expectException(InvalidRelationshipException::class);

        $this->model->tasks();
    }

    /**
     * @test
     */
    public function it_rasises_exception_when_asking_for_tasks_on_a_embed_type()
    {
        $this->model->type = 'embed';

        $this->expectException(InvalidRelationshipException::class);

        $this->model->tasks();
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
}
