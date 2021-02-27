<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Setup\ProjectFactory;
use Tests\TestCase;

class ActivityFeedTest extends TestCase
{
    use RefreshDatabase;

    // These use observers
    public function test_creating_project_records_activity()
    {
        // Generate a project
        $project = app(ProjectFactory::class)->create();

        // Assert that a project has generated 1 activity
        $this->assertCount(1, $project->activity);
        // Assert that the activity generated has 'created' in its description
        $this->assertEquals('created', $project->activity->first()->description) ;
    }

    public function test_updating_project_records_activity()
    {
        // Generate a project
        $project = app(ProjectFactory::class)->create();

        // Update the title of project to 'Changed'
        $project->update(['title' => 'Changed']);

        // Assert 2 activites were created, 1 for project creation and 1 for update
        $this->assertCount(2, $project->activity);
        // Assert that the most recent activity has a description of 'updated'
        $this->assertEquals('updated', $project->activity->last()->description);
    }

    public function test_creating_new_task_records_project_activity()
    {
        // Generate a project
        $project = app(ProjectFactory::class)->create();

        // Adds a new task to that project
        $project->addTask('New Task');

        // Assert 2 activites were created, 1 for project creation and 1 for new task
        $this->assertCount(2, $project->activity);
        // Assert that the most recent activity has a description of 'created_task'
        $this->assertEquals('created_task', $project->activity->last()->description);
    }

    public function test_completing_new_task_records_project_activity()
    {
        // Generate a project
        $project = app(ProjectFactory::class)->withTasks(1)->create();

        // Adds a new task to that project
        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'foobar',
                'completed' => true,
            ]);

        // Assert 3 activites were created, 1 for project creation and 1 for new task
        $this->assertCount(3, $project->activity);
        $this->assertEquals('completed_task', $project->activity->last()->description);
    }
}
