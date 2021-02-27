<?php

namespace Tests\Feature;

use Database\Factories\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityFeedTest extends TestCase
{
    use RefreshDatabase;

    // These use observers
    public function test_creating_project_generates_activity()
    {
        // Generate a project
        $project = app(ProjectFactory::class)->create();

        // Assert that a project has generated 1 activity
        $this->assertCount(1, $project->activity);
        // Assert that the activity generated has 'created' in its description
        $this->assertEquals('created', $project->activity->first()->description) ;
    }

    public function test_updating_project_generates_activity()
    {
        // Generate a project
        $project = app(ProjectFactory::class)->create();

        // Update the title of project to 'Changed'
        $project->update(['title' => 'Changed']);

        // Assert 2 activites were created, 1 for project creation and 1 for update
        $this->assertCount(2, $project->activity);
        // Assert that the most recent activity has a description of updated
        $this->assertEquals('updated', $project->activity->last()->description) ;
    }
}
