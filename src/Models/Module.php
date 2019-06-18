<?php

namespace App\Models;

use Illuminate\Database\Capsule;
class Module extends Model
{
    protected $table = "modulos";

    protected $fillable = ['nombre'];

    public $timestamps = false;

    public function scopePublic()
    {
        return Capsule\Manager::table('modulos')
            ->whereNotIn('id', [3, 4])
            ->get();
    }
}