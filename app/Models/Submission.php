<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Submission extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'status',
        'submission_date',
        'task_id',
        'intern_id',
    ];

    /**
     * Get the task associated with this submission.
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }

    /**
     * Get the intern who made this submission.
     */
    public function intern()
    {
        return $this->belongsTo(Intern::class, 'intern_id', 'id');
    }

    /**
     * Get all submission attempts for this submission.
     */
    public function attempts()
    {
        return $this->hasMany(SubmissionAttempt::class, 'submission_id', 'id');
    }
}
