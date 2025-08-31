<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionAttempt extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'file_path',
        'submission_id',
    ];

    protected $table = 'submission__attempts';

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class, 'submission_id', 'id');
    }
}
