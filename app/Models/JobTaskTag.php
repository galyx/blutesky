<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTaskTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'tag_id',
    ];

    public function tag()
    {
        return $this->hasOne(JobTag::class, 'id', 'tag_id');
    }
}
