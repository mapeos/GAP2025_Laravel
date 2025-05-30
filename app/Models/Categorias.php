<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categorias extends Model
{
    use HasFactory, SoftDeletes;

    // Definir qué campos pueden ser asignados masivamente
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    // Definir la relación muchos a muchos con News
    public function news()
    {
        return $this->belongsToMany(News::class, 'news_has_categorias', 'categorias_id', 'news_id');
    }

    // Si usas soft deletes, gestiona el campo 'deleted_at' automáticamente
    protected $dates = ['deleted_at'];

    // Si 'create_at' y 'update_at' son fechas, puedes agregarlas al cast
    protected $casts = [
        'create_at' => 'datetime',
        'update_at' => 'datetime',
    ];
}
