<?php

namespace App\Models;

class Student extends Model
{
    protected $table = "usuario";

    protected $fillable = ['usuario', 'nombres', 'clave', 'correo', 'apellidos', 'documento', 'institucion', 'genero', 'ciudad', 'departamento', 'pais', 'telefono', 'celular', 'direccion', 'fecha', 'institucion_id'];

    protected $hidden = ['created_at', 'updated_at'];

    public $timestamps = false;

    static function getForInstitution($where)
    {

        return Student::where('institucion', 'Like', $where)->get()->toArray();
    }

    public function getGeneroAttribute($value)
    {
        if (!empty($value)) {
            if ($value == '1' || $value == '2') {
                return $value == '1' ? 'M' : 'F';
            }
        }
        return strtoupper($value);
    }

    public function getFechaAttribute($value)
    {
        $date = new \DateTime($value);
        return $date->format('d-m-Y');
    }
    public function registers()
    {
        return $this->hasMany(Register::class, 'usuario', 'usuario');
    }

    public function registershistoricos()
    {
        return $this->hasMany(RegisterArchive::class, 'usuario', 'usuario');
    }

    public function scopeRutaN($query)
    {
        return $query->where('institucion', 'RutaN')->get()->toArray();
    }

    public function scopePascualBravo($query)
    {
        return $query->where('institucion', 'Institución Universitaria Pascual Bravo')->get()->toArray();
    }

    public function scopeColegioMayor($query)
    {
        return $query->where('institucion', 'Institución Universitaria Colegio Mayor de Antioquia')->get()->toArray();
    }

    public function scopeMujeres($query)
    {
        return $query->where('institucion', 'Secretaría de las mujeres')->get()->toArray();
    }


}