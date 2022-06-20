<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_name',
        'position',
        'status',
    ];

    public function jobUsers()
    {
        return $this->hasMany(JobUser::class);
    }

    public function jobLists()
    {
        return $this->hasMany(JobList::class);
    }
}
