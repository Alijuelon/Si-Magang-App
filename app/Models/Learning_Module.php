<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Learning_Module extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'description',
        'supervisor_id',
        'intern_id',
    ];

    protected $table = 'learning_modules';
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
     * Get the supervisor who created this learning module.
     */
    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'supervisor_id', 'id');
    }
}
