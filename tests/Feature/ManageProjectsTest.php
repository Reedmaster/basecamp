<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
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
            'description' => $this->faker->paragraph,
        ];
        
        $this->post('/projects', $attributes)->assertRedirect('/projects');

        $this->assertDatabaseHas('projects', $attributes);

        $this->get('/projects')->assertSee($attributes['title']);
    }

    public function test_a_user_can_view_their_project()
    {
        $this->signIn();
        
        $project = Project::factory()->create(['owner_id' => auth()->id()]);

        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee(Str::limit($project->description));
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
}
