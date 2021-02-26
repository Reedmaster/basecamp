@extends('layouts.app')

@section('content')
    <form method="POST" 
        action="{{ $project->path() }}" 
        class="lg:w-1/2 lg:mx-auto bg-white p-6 md:py-12 md:px-16 rounded shadow"
    >
        
        @csrf
        @method('PATCH')

        <h1 class="text-2xl font-normal mb-10 text-center">
            Edit a project
        </h1>

        <div class="field mb-6">
            <label class="label text-sm mb-2 block" for="title">Title</label>

            <div class="control">
                <input type="text" 
                    class="input bg-transparent border border-gray-500 rounded p-2 text-xs w-full" 
                    name="title" 
                    placeholder="Title"
                    value="{{ $project->title }}">
            </div>
        </div>

        <div class="field">
            <label class="label" for="description">Description</label>

            <div class="control">
                <textarea name="description" 
                    id="" 
                    class="textarea bg-transparent border border-gray-500 rounded p-2 text-xs w-full"
                >{{ $project->description }}</textarea>
            </div>
        </div>

        <div class="field">
            <div class="control">
                <button type="submit" class="btn-red">Update Project</button>
                <a href="{{ $project->path() }}">Cancel</a>
            </div>
        </div>
    </form>
@endsection