<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_project_can_have_tasks()
    {     
        // Creates a signed in user
        $this->signIn();

        $project = auth()->user()->projects()->create(
            Project::factory()->raw()
        );

        // Create a project using the factory class, with the id matching the owner_id
        $project = Project::factory()->create(['owner_id' => auth()->id()]);

        $this->post($project->path() . '/tasks', ['body' => 'Test task']);

        $this->get($project->path())
            ->assertSee('Test task');
    }

    public function test_a_task_requires_a_body()
    {
        // Sign in your user
        $this->signIn();

        $project = auth()->user()->projects()->create(
            Project::factory()->raw()
        );

        // You create the attributes with unvalidated description
        $attributes = Task::factory()->raw(['body' => '']);

        // Post request and assert errors due to unvalidated description
        $this->post($project->path() . '/tasks', $attributes)->assertSessionHasErrors('body');
    }
}
