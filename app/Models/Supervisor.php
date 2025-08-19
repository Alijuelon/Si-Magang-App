<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Supervisor extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'supervisors';

    protected $primaryKey = 'id';


    protected $guarded = [];

    /**
     * Get the user that owns the supervisor.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Get the interns for the supervisor.
     */
    public function interns()
    {
        return $this->hasMany(Intern::class, 'supervisor_id', 'id');
    }


    public function tasks()
    {
        return $this->hasMany(Task::class, 'supervisor_id', 'id');
    }


    public function learningModules()
    {
        return $this->hasMany(LearningModule::class, 'supervisor_id', 'id');
    }


}
