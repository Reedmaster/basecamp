<?php

namespace Tests\Feature;

use App\Http\Controllers\ProjectTasksController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Setup\ProjectFactory;
use Tests\TestCase;

class InvitationsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    function test_a_project_can_invite_a_user()
    {
        # Given you have a project
        $project = app(ProjectFactory::class)->create();

        # The owner of the project invites another user
        $project->invite($newUser = User::factory()->create());

        # User will have permission to add tasks
        $this->signIn($newUser);
        $this->post(action([ProjectTasksController::class, 'store'], $project), $task = ['body' => 'Test task']);

        $this->assertDatabaseHas('tasks', $task);
    }
}
