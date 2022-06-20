<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTaskField extends Model
{
    use HasFactory;

    protected $casts = [
        'field_array' => 'array'
    ];

    protected $fillable = [
        'job_id',
        'list_id',
        'task_id',
        'field_label_id',
        'field_value',
        'field_array',
    ];
}
