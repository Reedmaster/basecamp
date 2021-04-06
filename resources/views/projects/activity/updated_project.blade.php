@if (count($activity->changes['after']) == 1)
    Project {{  key($activity->changes['after']) }} 
    <div class="inline-block text-gray-400">
        updated by {{ $activity->user->name }}
    </div>
@else
    Project
    <div class="inline-block text-gray-400">
        updated by {{ $activity->user->name }}
    </div>
@endif