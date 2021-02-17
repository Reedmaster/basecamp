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