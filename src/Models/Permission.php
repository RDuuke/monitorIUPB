<?php

namespace App\Models;

class Permission extends Model
{
    protected $table = "permisos";

    protected $fillable = ['modulo_id', 'user_id', 'permiso'];

    public $timestamps = false;

    function module()
    {
        return $this->belongsTo(Module::class, 'modulo_id', 'id');
    }

    function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}