<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTasksTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_add_tasks_to_projects()
    {
        // create a project
        $project = Project::factory()->create();

        // attempt to post project without validation and be redirected to login
        $this->post($project->path() . '/tasks')->assertRedirect('login');
    }

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

    public function test_a_task_can_be_updated()
    { 
        $this->withoutExceptionHandling();
        // Creates a signed in user
        $this->signIn();

        // Authenticated user creates a project
        $project = auth()->user()->projects()->create(
            Project::factory()->raw()
        );

        // Add a task to project called test task
        $task = $project->addTask('Test Task');

        // Update task to body changed and completed to true in database
        $this->patch($project->path() . '/tasks/' . $task->id, [
            'body' => 'changed',
            'completed' => true
        ]);

        // Assert database has the new values
        $this->assertDatabaseHas('tasks', [
            'body' => 'changed',
            'completed' => true
        ]);
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

    public function test_only_owner_of_project_may_add_tasks()
    {
        // Sign in your user
        $this->signIn();

        // Create a project
        $project = Project::factory()->create();

        // Attempt to post a new task and receive a 403
        $this->post($project->path() . '/tasks', ['body' => 'Test task'])
            ->assertStatus(403);

        // Assert database doesn't contain the attempted task post
        $this->assertDatabaseMissing('tasks', ['body' => 'Test task']);    }
}
