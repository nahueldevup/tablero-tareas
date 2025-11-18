<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'user', 'status'];

    // Estados posibles: por_hacer, en_proceso, hecho, aprobado
}
