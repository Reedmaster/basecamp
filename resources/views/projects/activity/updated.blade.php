@if (count($activity->changes['after']) == 1)
    Updated the {{  key($activity->changes['after']) }} of the project
@else
    Updated the project
@endif