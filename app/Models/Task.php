<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'supervisor_id',
        'intern_id',
    ];

    // UUID casting
    protected $casts = [
        'id' => 'string',
        'due_date' => 'date',
    ];

    // Non-incrementing primary key
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

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
}
