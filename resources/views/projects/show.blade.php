@extends('layouts.app')

@section('content')
    <header class="flex items-center mb-3 py-4">
        <div class="flex justify-between items-end w-full">        
            <p class="text-gray-500 text-lg">
                <a href="/projects" class="text-gray-500 text-lg">My Projects</a> / {{ $project->title }}
            </p>

            <a href="{{ $project->path() . '/edit' }}" class="btn-red">
                Edit Project
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
                            <form method="POST" action="{{ $task->path() }}">
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

                    <form method="POST" action="{{ $project->path() }}">
                        @csrf
                        @method('PATCH')

                        <textarea 
                            name="notes"
                            class="card w-full mb-4 min-h-200px" 
                            placeholder="Notes here..."
                        >{{ $project->notes }}</textarea>

                        <button type="submit" class="btn-red">Save</button>
                    </form>

                    {{-- Shows errors in list if any are requested by the form --}}
                    @if ($errors->any())
                        <div class="field mt-6">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm text-red-500">{{ $error }}</li>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="lg:w-1/4 px-3"> 
                @include ('projects._card')
            </div>
        </div>
    </main>


@endsection