<?php

namespace App\Models;


use Illuminate\Database\Capsule\Manager;
use Illuminate\Support\Carbon;

class Monitor extends Model
{
    protected $table = "monitores";

    protected $fillable = [
        "id", "name", "url", "interval", "type", "status"
    ];
    public $appends = [
        "intervale", "countup", "count24hour", "count7days", "count30days", "average"
    ];

    public $timestamps = false;

    function registers()
    {
        return $this->hasMany(MonitorRegistro::class, "monitor_id", "id");
    }

    function getIntervaleAttribute()
    {
        return $this->interval ." ".$this->type;
    }

    function getCountUpAttribute()
    {
        $total = $this->registers()->count();
        if ($total != 0) {
            $total_200 = $this->registers()->where("response", "=", 200)->count();
            $valor = ($total_200/$total)*100;
            return round($valor, 1);
        }
        return "Sin registro";
    }

    function getCount24HourAttribute()
    {
        $total = $this->registers()->whereDay("created_at", "=", date("d"))->count();

        if ($total != 0) {

            $total_200 = $this->registers()->where("response", "=", 200)
                ->whereDay("created_at", "=", date("d"))->count();
            $valor = ($total_200/$total)*100;
            return round($valor, 1);
        }

        return "Sin registro";

    }

    function getCount7DaysAttribute()
    {
        $total = $this->registers()->whereBetween("created_at", [Carbon::today()->subDay(7), Carbon::today()->addDay()])->count();
        if ($total != 0) {
            $total_200 = $this->registers()->where("response", "=", 200)
                ->whereBetween("created_at", [Carbon::today()->subDay(7), Carbon::today()->addDay()])->count();
            $valor = ($total_200/$total)*100;
            return round($valor, 1);
        }

        return "Sin registro";

    }

    function getCount30DaysAttribute()
    {
        $total = $this->registers()->whereMonth("created_at", "=", date("m"))->count();
        if ($total != 0) {
            $total_200 = $this->registers()->where("response", "=", 200)
                ->whereMonth("created_at", "=", date("m"))->count();
            $valor = ($total_200/$total)*100;
            return round($valor, 1);
        }
        return "Sin registro";
    }

    function getAverageAttribute()
    {
        $total = $this->registers()->count();
        if ($total != 0) {
            $time = $this->registers()->sum("time");
            return round($time/$total, 4)*1000;
        }

        return "Sin registro";

    }
}