<?php

namespace App\Models;

class Institution extends Model
{
    protected $table = "institucion";

    protected $fillable = ['codigo', 'nombre', 'img'];

    public $timestamps = false;

    public function programs()
    {
        return $this->hasMany(Program::class, "codigo_institucion", "codigo");
    }

    public function registers()
    {
        return $this->hasMany(Register::class, "institucion_id", "codigo");
    }

    static function checkCodigo($codigo)
    {
        $result = Institution::where('codigo', '=', $codigo)->get();
        if ($result->count() < 1) {
            return true;
        }
        return false;
    }

    static function checkinstance($codigo)
    {
        $result = Institution::where('codigo', '=', "0".$codigo)->get();
        if ($result->count() < 1) {
            return true;
        }
        return false;
    }

    static function getNameInstitutionForCodigo($codigo)
    {
        return Institution::where("codigo", $codigo)->first()->nombre;
    }

    public function getFechaAttribute($value)
    {
        $date = new \DateTime($value);
        return $date->format('d-m-Y');
    }
}