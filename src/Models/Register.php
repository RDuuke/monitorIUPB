<?php

namespace App\Models;

use App\Tools\Tools;

class Register extends Model
{
    protected $table = "matricula";

    protected $fillable = ['curso', 'instancia', 'usuario', 'rol', 'fecha', 'institucion_id'];

    public $timestamps = false;

    public function getFechaAttribute($value)
    {
        $date = new \DateTime($value);
        return $date->format('d-m-Y');
    }

    function student()
    {
        return $this->belongsTo(Student::class, 'usuario', 'usuario');
    }

    function instancia()
    {
        return $this->belongsTo(Instance::class, 'instancia', 'codigo');
    }

    function course()
    {
        return $this->belongsTo(Course::class, 'curso', 'codigo');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institucion_id', 'codigo');
    }
    function scopePublic($query)
    {
        return $query->whereIn('instancia', [1, 2, 3]);
    }

    function getInstanciaAttribute($value)
    {
        //return $value;
        return Tools::getInstanceForCodigo((int) $value) ?? '';
    }
    public function scopeRuta($query)
    {
        return $query->where('instancia', 4);
    }
    public function scopePascual($query)
    {
        return $query->where('curso','LIKE', "101%")
            ->orWhere('curso','LIKE', "201%")
            ->orWhere('curso','LIKE', "6%")
            ->orWhere('curso','LIKE', "301%");
    }

    public function scopeColegio($query)
    {
        return $query->where('curso','LIKE', "102%")
            ->orWhere('curso','LIKE', "202%")
            ->orWhere('curso','LIKE', "8%")
            ->orWhere('curso','LIKE', "302%");
    }

    public function scopeITM($query)
    {
        return $query->where('curso','LIKE', "103%")
            ->orWhere('curso','LIKE', "203%")
            ->orWhere('curso','LIKE', "9%")
            ->orWhere('curso','LIKE', "303%");
    }

    public function scopeRutaN($query)
    {
        return $query->where('curso','LIKE', "104%")
            ->orWhere('curso','LIKE', "204%")
            ->orWhere('curso','LIKE', "4%")
            ->orWhere('curso','LIKE', "304%");
    }

}