<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ActivityReport extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'report_type',
        'title',
        'description',
        'report_date',
        'intern_id',
    ];

    /**
     * Get the intern who submitted this report.
     */
    public function intern()
    {
        return $this->belongsTo(Intern::class, 'intern_id', 'id');
    }
}
