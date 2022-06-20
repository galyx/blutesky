<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobUser extends Model
{
    use HasFactory;

    //Niveis de acesso
    #acesso Geral - 0
    #Adiministrador - 1
    #Gerente - 2
    #Colaborador - 3
    #Observador - 4

    protected $fillable = [
        'job_id',
        'user_id',
        'access',
        'status',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function job()
    {
        return $this->hasOne(Job::class, 'id', 'job_id');
    }
}
