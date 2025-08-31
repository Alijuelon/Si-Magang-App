<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'supervisor_id',
    ];

    /**
     * Get the supervisor who created this task.
     */
    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'supervisor_id', 'id');
    }

    /**
     * Get all submissions for this task.
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class, 'task_id', 'id');
    }

    public function interns()
    {
        return $this->belongsToMany(Intern::class);
    }
}
