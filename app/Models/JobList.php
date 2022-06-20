<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobList extends Model
{
    use HasFactory;

    protected $casts = [
        'visibility' => 'array',
        'access' => 'array',
    ];

    protected $fillable = [
        'job_id',
        'list_name',
        'position',
        'visibility',
        'access',
    ];

    public function jobTasks()
    {
        return $this->hasMany(JobTask::class, 'list_id');
    }
}
