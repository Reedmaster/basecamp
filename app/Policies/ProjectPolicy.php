<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Project $project)
    {
        # Allow auth if user is project owner OR user in project members
        return $user->is($project->owner) || $project->members->contains($user);
    }
}
