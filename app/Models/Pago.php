<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Pago extends Model
{
    use HasFactory;

    public function curso()
    {
        return $this->belongsTo(\App\Models\Curso::class, 'curso_id');
    }

    public function persona()
    {
        return $this->belongsTo(\App\Models\Persona::class, 'persona_id');
    }

    /**
     * Relación: un pago puede tener una factura asociada
     */
    public function factura()
    {
        return $this->hasOne(\App\Models\Factura::class, 'pago_id', 'id_pago');
    }

    /**
     * Relación: un pago tiene un método de pago
     */
    public function paymentMethod()
    {
        return $this->belongsTo(\App\Models\PaymentMethod::class, 'payment_method_id');
    }

    protected $table = 'pagos';  // nombre de la tabla

    protected $primaryKey = 'id_pago';

    protected $fillable = [
        'id_gasto',
        'importe',
        'concepto',
        'fecha',
        'pendiente',
        'tipo_pago', // unico o mensual
        'meses',     // número de meses si es mensual
        'nombre',
        'email',
        'curso',
        'payment_method_id',
    ];

    /**
     * Accesor para mostrar el nombre del método de pago
     */
    public function getMetodoPagoAttribute()
    {
        return $this->paymentMethod ? $this->paymentMethod->name : null;
    }
}
