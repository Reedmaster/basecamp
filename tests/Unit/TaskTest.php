<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    function test_it_belongs_to_a_project()
    {
        // Create a task
        $task = Task::factory()->create();

        // A task is asserted to be an instance of a project
        $this->assertInstanceOf(Project::class, $task->project);
    }

    function test_it_has_a_path()
    {
        $task = Task::factory()->create();

        $this->assertEquals('/projects/' . $task->project_id . '/tasks/' . $task->id, $task->path());
    }

    function test_it_can_be_completed()
    {
        // Generate a task
        $task = Task::factory()->create();

        // Assert that at this stage the task is incomplete
        $this->assertFalse($task->completed);

        // Call the method task complete
        $task->complete();

        // Assert true the fresh task from the database is completed
        $this->assertTrue($task->fresh()->completed);
    }
}
