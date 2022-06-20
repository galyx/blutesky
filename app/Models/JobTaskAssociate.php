<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTaskAssociate extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'total_time_task',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
