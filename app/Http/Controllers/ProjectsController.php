<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectsController extends Controller
{
    public function index()
    {
        // Gets projects by auth user
        $projects = auth()->user()->projects;

        return view('projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        if (auth()->user()->isNot($project->owner)) {
            abort(403);
        }

        return view('projects.show', compact('project'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store()
    {
        $attributes = $this->validateRequest();

        $project = auth()->user()->projects()->create($attributes);

        return redirect($project->path());
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Project $project)
    {
        $this->authorize('update', $project);

        // Updates attributes
        $project->update($this->validateRequest());

        return redirect($project->path());
    }

    # Delete the project
    public function destroy(Project $project)
    {
        // If auth user is not owner of project, then abort
        $this->authorize('update', $project);

        $project->delete();

        return redirect('/projects');
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required',
            'description' => 'sometimes|required|max:100',
            'notes' => 'nullable',
        ]);
    }
}
