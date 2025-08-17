<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LearningProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'progress_status',
        'module_id',
        'intern_id',
    ];

    // UUID casting
    protected $casts = [
        'id' => 'string',
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
     * Get the intern who owns this progress.
     */
    public function intern()
    {
        return $this->belongsTo(Intern::class, 'intern_id', 'id');
    }

    /**
     * Get the learning module for this progress.
     */
    public function module()
    {
        return $this->belongsTo(Learning_Module::class, 'module_id', 'id');
    }
}
