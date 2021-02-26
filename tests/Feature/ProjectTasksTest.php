<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Setup\ProjectFactory;
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
        $project = app(ProjectFactory::class)
            ->create();

        $this->actingAs($project->owner)
            ->post($project->path() . '/tasks', ['body' => 'Test task']);

        $this->get($project->path())
            ->assertSee('Test task');
    }

    public function test_a_task_can_be_updated()
    { 
        // Calls project factory, creates a project with a single task, persists it, saves it in a variable
        $project = app(ProjectFactory::class)
            ->withTasks(1)
            ->create();

        // Acting as owner to the project, update task to body changed and completed to true in database
        $this->actingAs($project->owner)
            ->patch($project->tasks->first()->path(), [
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
        $project = app(ProjectFactory::class)
            ->create();

        // You create the attributes with unvalidated description
        $attributes = Task::factory()->raw(['body' => '']);

        // Post request and assert errors due to unvalidated description
        $this->actingAs($project->owner)
            ->post($project->path() . '/tasks', $attributes)
            ->assertSessionHasErrors('body');
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
        $this->assertDatabaseMissing('tasks', ['body' => 'Test task']);    
    }

    public function test_only_owner_of_project_may_update_a_task()
    {
        // Sign in your user
        $this->signIn();

        // Create a project with 1 task
        $project = app(ProjectFactory::class)
            ->withTasks(1)
            ->create();

        // Attempt to update a new task and receive a 403
        $this->patch($project->tasks[0]->path(), ['body' => 'changed'])
            ->assertStatus(403);

        // Assert database doesn't contain the updated body
        $this->assertDatabaseMissing('tasks', ['body' => 'changed']);    
    }
}
