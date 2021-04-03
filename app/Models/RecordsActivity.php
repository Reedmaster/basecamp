<?php

namespace App\Models;

use Illuminate\Support\Arr;

trait RecordsActivity
{
    # Old values are stored here before the project is updated
    public $old = [];
    
    // Record activity for a project
    public function recordActivity($description)
    {
        # When activity is created, create a description and give the changes
        $this->activity()->create([
            'description' => $description,
            'changes' => $this->activityChanges(),
            # If the class basename is 'Project' then project_id is id, else project_id is project_id
            'project_id' => class_basename($this) === 'Project' ? $this->id : $this->project_id,
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
