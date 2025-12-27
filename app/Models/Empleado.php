<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleados';
    
    protected $fillable = [
        'nombre',
        'email',
        'sexo',
        'area_id',
        'boletin',
        'descripcion'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
}
