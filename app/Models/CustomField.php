<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    use HasFactory;

    protected $casts = [
        'list_name' => 'array'
    ];

    protected $fillable = [
        'job_id',
        'list_id',
        'position',
        'label_field_id',
        'field_type',
        'field_mask',
        'list_name',
        'label_field',
        'field_edit_lists',
        'field_required',
        'status',
    ];

    public function jobList()
    {
        return $this->hasOne(JobList::class, 'id', 'list_id');
    }
}
