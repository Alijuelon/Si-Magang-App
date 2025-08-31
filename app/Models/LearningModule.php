<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LearningModule extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'description',
        'supervisor_id',
    ];

    protected $table = 'learning_modules';

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'supervisor_id', 'id');
    }


    public function interns()
    {
        return $this->belongsToMany(Intern::class, 'intern_learning_module', 'learning_module_id', 'intern_id');
    }

}
