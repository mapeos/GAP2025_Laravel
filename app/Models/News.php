<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory, SoftDeletes;

    //Definir que campos pueden ser asignados masivamnete
    protected $fillable = [
        'titulo',
        'contenido',
        'autor',
        'fecha_publicacion',
        'imagen',
    ];

    protected $casts = [
        'fecha_publicacion' => 'datetime',
    ];

    //Definir a ralacion muchos a muchos con Categorias
    public function categorias()
    {
        return $this->belongsToMany(Categorias::class, 'news_has_categorias', 'news_id', 'categorias_id');
    }

    /**
     * Relación inversa con el modelo User (autor de la noticia).
     * Asume que el campo 'autor' en esta tabla es la clave foránea que referencia a la tabla users.
     */
    // public function autor()
    // {
    //     return $this->belongsTo(User::class, 'autor');
    // }


    // Si usas soft deletes, gestiona el campo 'deleted_at' automáticamente
    protected $dates = ['deleted_at']; // opcional
}
