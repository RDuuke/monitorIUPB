<?php

namespace App\Models;

class Rol extends Model
{
    protected $table = "rol";

    protected $fillable = ['codigo', 'nombre'];

    public $timestamps = false;

}