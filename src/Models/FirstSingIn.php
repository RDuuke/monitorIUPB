<?php

namespace App\Models;


class FirstSingIn extends Model
{
    protected $table = "ingreso_plataforma";

    protected $fillable = ["usuario", "singin"];

    protected $primaryKey = "usuario";

}