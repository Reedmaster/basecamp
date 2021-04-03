<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    # Old values are stored here before the project is updated
    public $old = [];

    public function path()
    {
        return "/projects/{$this->id}";
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function addTask($body)
    {
        return $this->tasks()->create(['body' => $body]);
    }

    // Activity feed for the project
    public function activity()
    {
        return $this->hasMany(Activity::class)->latest();
    }

    // Record activity for a project
    public function recordActivity($description)
    {
        # When activity is created, create a description and give the changes
        $this->activity()->create([
            'description' => $description,
            'changes' => $this->activityChanges($description)
        ]);
    }

    protected function activityChanges()
    {
        # If a project was changed then return a before and after
        if ($this->wasChanged()) {
            return [
                # Create a before array with old attributes, except 'updated_at'
                'before' => Arr::except(array_diff($this->old, $this->getAttributes()), 'updated_at'),
                # Create an after array with changed attributes, except 'updated_at'
                'after' => Arr::except($this->getChanges(), 'updated_at'),
            ];
        }
    }
}
