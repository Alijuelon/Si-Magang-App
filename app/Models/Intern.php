<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Intern extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'interns';

    protected $guarded = [];


    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'supervisor_id', 'id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'intern_id', 'id');
    }

     public function activityReports()
    {
        return $this->hasMany(ActivityReport::class, 'intern_id', 'id');
    }

    public function learningProgress()
    {
        return $this->hasMany(LearningProgress::class, 'intern_id', 'id');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'intern_task');
    }

    public function learningModules()
    {
        return $this->belongsToMany(LearningModule::class, 'intern_learning_module', 'intern_id', 'learning_module_id');
    }

}
