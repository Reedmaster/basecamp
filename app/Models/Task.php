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

        // // When a task is created we generate activity
        // static::created(function ($task) {
        //     $task->project->recordActivity('created_task');
        // });

        // static::deleted(function ($task) {
        //     $task->project->recordActivity('deleted_task');
        // });
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

        // Record activity
        $this->recordActivity('completed_task');
    }

    public function incomplete()
    {
        // Update task completed field to false
        $this->update(['completed' => false]);

        // Record activity
        $this->recordActivity('uncompleted_task');
    }

    public function path()
    {
        return "/projects/{$this->project->id}/tasks/{$this->id}";
    }

    // Activity feed for the project
    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    // Record activity for a project
    public function recordActivity($description)
    {
        $this->activity()->create([
            'project_id' => $this->project_id,
            'description' => $description,
        ]);
    }
}
