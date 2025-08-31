<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LearningProgress extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'description',
        'progress_status',
        'module_id',
        'intern_id',
    ];


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
        return $this->belongsTo(LearningModule::class, 'module_id', 'id');
    }
}
