<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Property that references any relationships and updates when current instance is updated, Task updates -> Project update
    protected $touches = ['project'];

    // Ensure that even if completed field on MySQL is stored as 0 or 1, we cast it into boolean
    protected $casts = [
        'completed' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        // When a task is created we generate activity
        static::created(function ($task) {
            $task->project->recordActivity('created_task');
        });

        // Task is updated, if it is completed then changed description to 'completed_task'
        static::updated(function ($task) {
            // If not completed then return
            if (! $task->completed) return;

            $task->project->recordActivity('completed_task');
        });
    }

    public function project()
    {
        // A task belongs to a project
        return $this->belongsTo(Project::class);
    }

    public function complete()
    {
        // Update task completed field to true
        $this->update(['completed' => true]);
    }

    public function path()
    {
        return "/projects/{$this->project->id}/tasks/{$this->id}";
    }
}
