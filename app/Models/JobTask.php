<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'list_id',
        'position',
        'task_title',
        'delivery_date_task',
        'total_time_task',
        'status',
    ];

    public function jobTaskFields()
    {
        return $this->hasMany(JobTaskField::class, 'task_id');
    }

    public function jobTaskAssociate()
    {
        return $this->hasMany(JobTaskAssociate::class, 'task_id');
    }

    public function jobTaskTag()
    {
        return $this->hasMany(JobTaskTag::class, 'task_id');
    }
}
