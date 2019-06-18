<?php

namespace App\Models;

class Course extends Model
{
    protected $table = "curso";

    protected $fillable = ['codigo', 'nombre', 'nombre_corto', 'id_programa', 'fecha', 'institucion_id'];

    public $timestamps = false;


    public function getFechaAttribute($value)
    {
        $date = new \DateTime($value);
        return $date->format('d-m-Y');
    }
    
    public function registers()
    {
        return $this->hasMany(Register::class, 'curso', 'codigo');
    }

    public function programs()
    {
        return $this->belongsTo(Program::class, 'id_programa', 'codigo');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institucion_id', 'codigo');
    }
    /*
    public function getIdProgramaAttribute($value)
    {
        $programa = Program::where('codigo', $value)->first();
        return $programa->nombre;
    }
*/

    public function scopePascual($query)
    {
        return $query->where('codigo','LIKE', "101%")
            ->orWhere('codigo','LIKE', "201%")
            ->orWhere('codigo','LIKE', "301%")
            ->orWhere('codigo','LIKE', "1001%");
    }

    public function scopeColegio($query)
    {
        return $query->where('codigo','LIKE', "102%")
            ->orWhere('codigo','LIKE', "202%")
            ->orWhere('codigo','LIKE', "302%")
            ->orWhere('codigo','LIKE', "802%");
    }

    public function scopeITM($query)
    {
        return $query->where('codigo','LIKE', "103%")
            ->orWhere('codigo','LIKE', "203%")
            ->orWhere('codigo','LIKE', "303%")
            ->orWhere('codigo','LIKE', "903%");

    }

    public function scopeRuta($query)
    {
        return $query->where('codigo','LIKE', "104%")
            ->orWhere('codigo','LIKE', "204%")
            ->orWhere('codigo','LIKE', "304%");
    }

    public function scopeMujeres($query)
    {
        return $query->where('codigo','LIKE', "107%");
    }

    static function checkCodigo($codigo)
    {
        $result = Course::where('codigo', '=', $codigo)->get();
        if ($result->count() < 1) {
            return true;
        }
        return false;
    }
}