<?php

namespace App\Models;

use Illuminate\Support\Arr;

trait RecordsActivity
{
    # oldAttributes are stored here before the project is updated
    public $oldAttributes = [];

    # Boot method called on trait needs to have syntax of trait name following
    # Static so method is accessible without instantiation
    public static function bootRecordsActivity()
    {
        # foreach recordable events as an event, when the model is created...
        foreach (self::recordableEvents() as $event) {
            static::$event(function ($model) use ($event) {

                # Record activity of the event type
                $model->recordActivity($model->activityDescription($event));
            });

            # if event is 'updated', which only occurs on Project model...
            if ($event === 'updated') {
                # store oldAttributes of a model
                static::updating(function ($model) {
                    $model->oldAttributes = $model->getOriginal();
                });
            }
        }
    }

    protected function activityDescription($description)
    {
        # Return a lowercase string of event description
        return "{$description}_" . strtolower(class_basename($this)); # e.g. updated_project
    }

    protected static function recordableEvents()
    {
        # If user has created a property on model override default $recordableEvents, else use defaults
        if (isset(static::$recordableEvents)) {
            return static::$recordableEvents;
        } else {
            return ['created', 'updated'];
        }
    }

    // Record activity for a project
    public function recordActivity($description)
    {
        # When activity is created, create a description and give the changes
        $this->activity()->create([
            'user_id' => $this->activityOwner()->id,
            'description' => $description,
            'changes' => $this->activityChanges(),
            # If the class basename is 'Project' then project_id is id, else project_id is project_id
            'project_id' => class_basename($this) === 'Project' ? $this->id : $this->project_id,
        ]);
    }

    protected function activityOwner()
    {
        if (auth()->check()) {
            return auth()->user();
        }

        # If a given class has a project relationship, use its owner, if not, then it is Project and use its owner
        return ($this->project ?? $this)->owner;
    }

    // Activity feed for the project
    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    protected function activityChanges()
    {
        # If a project was changed then return a before and after
        if ($this->wasChanged()) {
            return [
                # Create a before array with oldAttributes, except 'updated_at'
                'before' => Arr::except(array_diff($this->oldAttributes, $this->getAttributes()), 'updated_at'),
                # Create an after array with changed attributes, except 'updated_at'
                'after' => Arr::except($this->getChanges(), 'updated_at'),
            ];
        }
    }
}
