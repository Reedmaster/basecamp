@extends('layouts.app')

@section('content')
    <div class="lg:w-1/2 lg:mx-auto bg-white p-6 md:py-12 md:px-16 rounded shadow">
        <h1 class="text-2xl font-normal mb-10 text-center">
            Create a project
        </h1>

        <form method="POST" 
            action="/projects" 
        >
            {{-- This passes in a new instance of project, so no information is need to pass in like edit.blade.php --}}
            @include('projects._form',[
                'project' => new App\Models\Project,
                'buttonText' => 'Create Project',
            ])
        </form>
    </div>
@endsection