<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ActivityReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_type',
        'title',
        'description',
        'report_date',
        'intern_id',
    ];

    // UUID casting
    protected $casts = [
        'id' => 'string',
        'report_date' => 'date',
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
     * Get the intern who submitted this report.
     */
    public function intern()
    {
        return $this->belongsTo(Intern::class, 'intern_id', 'id');
    }
}
