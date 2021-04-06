<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\Setup\ProjectFactory;
use Tests\TestCase;

class ManageProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_a_user_can_create_a_project()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $this->get('/projects/create')->assertStatus(200);

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->sentence,
            'notes' => 'Project notes here.',
        ];

        // Project with attributes is posted to index and saved as response
        $response = $this->post('/projects', $attributes);

        $project = Project::where($attributes)->first();

        // Access Project where theattributes and pull first one which is the id then assert redirect to project path
        $response->assertRedirect($project->path());

        $this->assertDatabaseHas('projects', $attributes);

        $this->get($project->path())
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);
    }

    function test_unauthorised_cannot_delete_project()
    {
        # Create a project belonging to auth user
        $project = app(ProjectFactory::class)->create();

        # As a guest delete the project and redirect to /login
        $this->delete($project->path())
            ->assertRedirect('/login');

        $this->signIn();

        $this->delete($project->path())
            ->assertStatus(403);
    }

    function test_user_can_delete_project()
    {
        $this->withoutExceptionHandling();

        # Create a project belonging to auth user
        $project = app(ProjectFactory::class)->create();

        # Acting as project owner, delete the project and redirect to /project
        $this->actingAs($project->owner)
            ->delete($project->path())
            ->assertRedirect('/projects');

        # Assert that the projects id is missing in the database
        $this->assertDatabaseMissing('projects', $project->only('id'));
    }

    public function test_user_can_update_project()
    {
        $this->signIn();

        $this->withoutExceptionHandling();

        // Create a project belonging to auth user
        $project = app(ProjectFactory::class)->create();

        // Update the notes to new notes
        $this->actingAs($project->owner)
            ->patch($project->path(), [
                'title' => 'Changed',
                'description' => 'Changed',
                'notes' => 'Changed',
            ])
            ->assertRedirect($project->path());

        // If make get request to project path edit, then assert okay
        $this->get($project->path() . '/edit')->assertOk();

        // Assert that database has the new notes
        $this->assertDatabaseHas('projects', ['notes' => 'Changed']);
    }

    public function test_user_can_update_a_projects_notes()
    {
        // Create a project belonging to auth user
        $project = app(ProjectFactory::class)->create();

        // Update the notes to new notes
        $this->actingAs($project->owner)
            ->patch($project->path(), [
                'notes' => 'Changed',
            ])
            ->assertRedirect($project->path());

        // Assert that database has the new notes
        $this->assertDatabaseHas('projects', ['notes' => 'Changed']);
    }

    public function test_a_user_can_view_their_project()
    {
        $this->signIn();

        $project = Project::factory()->create(['owner_id' => auth()->id()]);

        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee(Str::limit($project->description, 100));
    }

    public function test_a_project_requires_a_title()
    {
        // Sign in your user
        $this->signIn();

        // You create the attributes of a project with unvalidated title
        $attributes = Project::factory()->raw(['title' => '']);

        // Post request and assert errors due to unvalidated title in project
        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
    }

    public function test_a_project_requires_a_description()
    {
        // Sign in your user
        $this->signIn();

        // You create the attributes of a project with unvalidated description
        $attributes = Project::factory()->raw(['description' => '']);

        // Post request and assert errors due to unvalidated description in project
        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
    }

    public function test_guest_cannot_manage_projects()
    {
        $project = Project::factory()->create();

        $this->get('/projects')->assertRedirect('login');
        $this->get('/projects/create')->assertRedirect('login');
        $this->get('/projects/edit')->assertRedirect('login');
        $this->get($project->path())->assertRedirect('login');
        $this->post('/projects', $project->toArray())->assertRedirect('login');
    }

    public function test_authenticated_user_cannot_view_the_projects_of_others()
    {
        // Sign in your user
        $this->signIn();

        $project = Project::factory()->create();

        $this->get($project->path())->assertStatus(403);
    }

    public function test_authenticated_user_cannot_update_the_projects_of_others()
    {
        // Sign in your user
        $this->signIn();

        $project = Project::factory()->create();

        $this->patch($project->path(), [])->assertStatus(403);
    }
}
