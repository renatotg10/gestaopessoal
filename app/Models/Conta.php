<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    protected $fillable = ['nome', 'tipo', 'saldo_inicial'];
}
