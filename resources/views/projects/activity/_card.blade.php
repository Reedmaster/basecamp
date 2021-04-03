<div class="card mt-3">
    <ul class="text-sm">
        @foreach ($project->activity as $activity)
            <li class="{{ $loop->last ? '' : 'mb-1' }}">
                @include("projects.activity.{$activity->description}")
                <span class="text-gray-400">{{ $activity->created_at->shortRelativeDiffForHumans() }}</span>
            </li>
        @endforeach
    </ul>
</div>