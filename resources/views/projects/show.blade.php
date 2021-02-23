@extends('layouts.app')

@section('content')
    <header class="flex items-center mb-3 py-4">
        <div class="flex justify-between items-end w-full">        
            <p class="text-gray-500 text-lg">
                <a href="/projects" class="text-gray-500 text-lg">My Projects</a> / {{ $project->title }}
            </p>

            <a href="/projects/create" class="btn-red">
                New Project
            </a>
        </div>
    </header>

    <main>
        <div class="lg:flex -mx-3">
            <div class="lg:w-3/4 px-3 mb-6">
                <div class="mb-6">
                    <h2 class="text-gray-500 text-lg mb-3">Tasks</h2>

                    @foreach ($project->tasks as $task)
                        <div class="card mb-3">
                            <form method="POST" action="{{  $project->path() . '/tasks/' . $task->id }}">
                                @method('PATCH')
                                @csrf

                                <div class="flex">
                                    <input name="body" value="{{ $task->body }}" class="w-full {{ $task->completed ? 'text-gray-500' : '' }}">
                                    <input name="completed" type="checkbox" onChange="this.form.submit()" {{ $task->completed ? 'checked' : '' }}>                                    
                                </div>

                            </form>
                        </div>
                    @endforeach
                        <div class="card mb-3">
                            <form action="{{ $project->path() . '/tasks' }}"
                                method="POST"
                            >
                                @csrf
                                <input placeholder="Add a task..." class="w-full" name="body">
                            </form>
                        </div>
                </div>

                <div>
                    <h2 class="text-gray-500 text-lg mb-3">General Notes</h2>

                    <textarea class="card w-full min-h-200px">Lorem ipsum.</textarea>
                </div>
            </div>

            <div class="lg:w-1/4 px-3"> 
                @include ('projects._card')
            </div>
        </div>
    </main>


@endsection