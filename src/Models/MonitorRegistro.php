<?php

namespace App\Models;


class MonitorRegistro extends Model
{
    protected $table = "registro_monitores";

    protected $fillable = [
        "monitor_id", "response", "time"
    ];

    protected $appends = [
        "statusresponse"
    ];

    public $timestamps = false;


    public function getStatusResponseAttribute()
    {
        return $this->response == 200 ? "Activo" : "Inactivo";
    }

}