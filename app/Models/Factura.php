<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $fillable = [
        'user_id',
        'pago_id',
        'producto',
        'importe',
        'fecha',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id', 'id_pago');
    }
}
