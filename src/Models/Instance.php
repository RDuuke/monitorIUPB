<?php

namespace App\Models;


class Instance extends Model
{
    protected $table = "instancia";

    protected $fillable = ['codigo', 'nombre', 'bd'];

    public $timestamps = false;

    public function registers()
    {
        return $this->hasMany(Register::class, 'instancia');
    }

    static function getLastCodigo()
    {
        $all = Instance::all();
        $last = $all->last();
        return $last->codigo;
    }

    static function checkCodigo($codigo)
    {
        $result = Instance::where('codigo', '=', $codigo)->get();
        if ($result->count() < 1) {
            return true;
        }
        return false;
    }
}