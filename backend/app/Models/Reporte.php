<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $table = 'reportes';

    protected $fillable = [
        'fecha_generacion', 'tipo', 'datos', 'admin_id',
    ];

    protected $casts = [
        'fecha_generacion' => 'datetime',
        'datos' => 'array',
    ];

    public function administrador()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public $timestamps = false;
}

