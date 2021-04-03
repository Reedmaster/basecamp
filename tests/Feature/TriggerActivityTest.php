<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Setup\ProjectFactory;
use Tests\TestCase;

class TriggerActivityTest extends TestCase
{
    use RefreshDatabase;

    // These use observers
    public function test_creating_project()
    {
        // Generate a project
        $project = app(ProjectFactory::class)->create();

        // Assert that a project has generated 1 activity
        $this->assertCount(1, $project->activity);

        // Get the most recent activity
        tap($project->activity->last(), function($activity) {
            // Assert that the activity generated has 'created' in its description
            $this->assertEquals('created_project', $activity->description) ;
            # Assert that when a project is created activity changes returns as null
            $this->assertNull($activity->changes);
        });
    }

    public function test_updating_project()
    {
        // Generate a project
        $project = app(ProjectFactory::class)->create();
        $originalTitle = $project->title;

        // Update the title of project to 'Changed'
        $project->update(['title' => 'Changed']);

        // Assert 2 activites were created, 1 for project creation and 1 for update
        $this->assertCount(2, $project->activity);

        // Get the most recent activity
        tap($project->activity->last(), function($activity) use ($originalTitle) {
            // Assert that the most recent activity has a description of 'updated'
            $this->assertEquals('updated_project', $activity->description);

            #
            $expected = [
                'before' => ['title' => $originalTitle],
                'after' => ['title' => 'Changed'],
            ];

            # Assert what we expected is equal to activity changes
            $this->assertEquals($expected, $activity->changes);
        });
    }

    public function test_creating_new_task()
    {
        // Generate a project
        $project = app(ProjectFactory::class)->create();

        // Adds a new task to that project
        $project->addTask('New Task');

        // Assert 2 activites were created, 1 for project creation and 1 for new task
        $this->assertCount(2, $project->activity);

        // Get the most recent activity
        tap($project->activity->last(), function ($activity) {
            // Assert that activity description is equal to 'created_task'
            $this->assertEquals('created_task', $activity->description);
            // Assert instance of Task when given subject of activity
            $this->assertInstanceOf(Task::class, $activity->subject);
            // Assert that activity body is equal to 'New Task'
            $this->assertEquals('New Task', $activity->subject->body);
        });
    }

    public function test_completing_task()
    {
        $this->withoutExceptionHandling();
        // Generate a project
        $project = app(ProjectFactory::class)->withTasks(1)->create();

        // Adds a new task to that project
        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'test body',
                'completed' => true,
            ]);

        // Assert 3 activites were created, 1 for project creation, 1 for new task, 1 for completion, fresh copy from databse for changes to have gone through
        $this->assertCount(3, $project->activity);

        // Get the most recent activity
        tap($project->activity->last(), function ($activity) {
            // Assert that activity description is equal to 'completed_task', which is the last field in the table
            $this->assertEquals('completed_task', $activity->description);
            // Assert instance of Task when given subject of activity
            $this->assertInstanceOf(Task::class, $activity->subject);
        });
    }

    public function test_uncompleting_task()
    {
        // Generate a project
        $project = app(ProjectFactory::class)->withTasks(1)->create();

        // Acting as the project owner, adds a new task to that project, sets completion to true
        $this->actingAs($project->owner)
            ->patch($project->tasks[0]->path(), [
                'body' => 'test body',
                'completed' => true,
            ]);

        // Assert 3 activites were created, 1 for project creation, 1 for new task, 1 for completion
        $this->assertCount(3, $project->activity);

        // Adds a new task to that project, sets completion to false
        $this->patch($project->tasks[0]->path(), [
                'body' => 'test body',
                'completed' => false,
            ]);

        // Refresh project in databse for changes to have gone through
        $project->refresh();

        // Assert 4 activites were created, 1 project creation, 1 new task, 1 completion, 1 incompletion
        $this->assertCount(4, $project->activity);
        // Assert that activity description is equal to 'uncompleted_task', which is the last field in the table
        $this->assertEquals('uncompleted_task', $project->activity->last()->description);
    }

    function test_deleting_task()
    {
        // Generate a project
        $project = app(ProjectFactory::class)->withTasks(1)->create();

        // Fetch and delete the first task
        $project->tasks->first()->delete();

        // Assert 3 activites were created, 1 project creation, 1 new task, 1 task deletion
        $this->assertCount(3, $project->activity);
    }
}
