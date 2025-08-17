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

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'interns';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the user that owns the intern.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Get the supervisor that owns the intern.
     */
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
    }   /**
     * Get all tasks assigned to this intern.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'intern_id', 'id');
    }

    /**
     * Get all learning modules assigned to this intern.
     */
    public function learningModules(): HasMany
    {
        return $this->hasMany(Learning_Module::class, 'intern_id', 'id');
    }


}
