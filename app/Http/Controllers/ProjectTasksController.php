<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;

class ProjectTasksController extends Controller
{
    public function store(Project $project)
    {
        // If the auth user is not the owner of the project then abort
        if (auth()->user()->isNot($project->owner)) {
            abort(403);
        }

        request()->validate(['body' => 'required']);
        
        $project->addTask(request('body'));

        return redirect($project->path());
    }

    public function update(Project $project, Task $task)
    {
        // If the auth user is not the owner of the task then abort
        if (auth()->user()->isNot($task->project->owner)) {
            abort(403);
        }

        request()->validate(['body' => 'required']);

        $task->update(['body' => request('body')]);

        if (request()->has('completed')) {
            $task->complete();
        }

        return redirect($project->path());
    }
}
