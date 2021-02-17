@extends('layouts.app')

@section('content')
    <header class="flex items-center mb-3 py-4">
        <div class="flex justify-between items-end w-full">        
            <p class="text-gray-500 text-lg">
                My Projects
            </p>

            <a href="/projects/create" class="btn-red">
                New Project
            </a>
        </div>
    </header>

    <div class="lg:flex lg:flex-wrap -mx-3">
        @forelse ($projects as $project)
            <div class="lg:w-1/3 px-3 pb-6">
                @include ('projects._card')
            </div>
        @empty
            <div>No projects yet.</div>
        @endforelse
    </div> 
@endsection