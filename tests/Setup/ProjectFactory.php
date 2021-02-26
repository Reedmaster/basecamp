<?php

namespace Tests\Setup;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class ProjectFactory
{
    protected $tasksCount = 0;
    protected $user;

    // Receives a number to $count and changes the $tasksCount to that number
    public function withTasks($count)
    {
        $this->tasksCount = $count;

        return $this;
    }

    public function ownedBy($user)
    {
        // This user is this user
        $this->user = $user;

        return $this;
    }

    public function create()
    {
        // Creates a project with owner id belonging to created user id
        $project = Project::factory()->create([
            // Use existing user, otherwise, create one
            'owner_id' => $this->user ?? User::factory()
        ]);

        // Creates amount of tasks according to tasksCount and matching the project_id to project id
        Task::factory($this->tasksCount)->create([
            'project_id' => $project->id
        ]);

        return $project;
    }
}

app(ProjectFactory::class)->create();