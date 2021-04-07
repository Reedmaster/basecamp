<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_a_path()
    {
        $project = Project::factory()->create();

        $this->assertEquals('/projects/' . $project->id, $project->path());
    }

    public function test_it_belongs_to_an_owner()
    {
        $project = Project::factory()->create();

        $this->assertInstanceOf('App\Models\User', $project->owner);
    }

    public function test_it_can_add_a_task()
    {
        $project = Project::factory()->create();

        $task = $project->addTask('Test task');

        $this->assertCount(1, $project->tasks);
        // Assert true that the tasks in project contains a task that was just created
        $this->assertTrue($project->tasks->contains($task));
    }

    function test_it_can_invite_user()
    {
        # We have a project
        $project = Project::factory()->create();

        # The project invites a user
        $project->invite($user = User::factory()->create());

        # Assert true that the projects members contains that user
        $this->assertTrue($project->members->contains($user));
    }
}
