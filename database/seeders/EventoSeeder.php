<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Evento;
use App\Models\TipoEvento;
use App\Models\User;
use Carbon\Carbon;

class EventoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener tipos de evento
        $tipoClase = TipoEvento::where('nombre', 'Clase')->first();
        $tipoReunion = TipoEvento::where('nombre', 'Reunión')->first();
        $tipoEntrega = TipoEvento::where('nombre', 'Entrega')->first();
        
        // Obtener un usuario administrador
        $admin = User::role('administrador')->first();
        
        if (!$admin) {
            $admin = User::first();
        }
        
        // Crear eventos de prueba
        $eventos = [
            [
                'titulo' => 'Clase de Programación Web',
                'descripcion' => 'Introducción a Laravel y desarrollo web moderno',
                'fecha_inicio' => Carbon::now()->addDays(2)->setTime(10, 0),
                'fecha_fin' => Carbon::now()->addDays(2)->setTime(12, 0),
                'tipo_evento_id' => $tipoClase->id,
                'creado_por' => $admin->id,
                'ubicacion' => 'Aula 101',
                'status' => true,
            ],
            [
                'titulo' => 'Reunión de Profesores',
                'descripcion' => 'Planificación del próximo trimestre',
                'fecha_inicio' => Carbon::now()->addDays(5)->setTime(15, 0),
                'fecha_fin' => Carbon::now()->addDays(5)->setTime(17, 0),
                'tipo_evento_id' => $tipoReunion->id,
                'creado_por' => $admin->id,
                'ubicacion' => 'Sala de reuniones',
                'status' => true,
            ],
            [
                'titulo' => 'Entrega de Proyecto Final',
                'descripcion' => 'Fecha límite para entregar el proyecto de Laravel',
                'fecha_inicio' => Carbon::now()->addDays(7)->setTime(23, 59),
                'fecha_fin' => Carbon::now()->addDays(7)->setTime(23, 59),
                'tipo_evento_id' => $tipoEntrega->id,
                'creado_por' => $admin->id,
                'ubicacion' => 'Plataforma online',
                'status' => true,
            ],
            [
                'titulo' => 'Clase de Base de Datos',
                'descripcion' => 'Optimización de consultas SQL y relaciones',
                'fecha_inicio' => Carbon::now()->addDays(1)->setTime(14, 0),
                'fecha_fin' => Carbon::now()->addDays(1)->setTime(16, 0),
                'tipo_evento_id' => $tipoClase->id,
                'creado_por' => $admin->id,
                'ubicacion' => 'Aula 102',
                'status' => true,
            ],
            [
                'titulo' => 'Tutoría Individual',
                'descripcion' => 'Sesión de consulta sobre el proyecto',
                'fecha_inicio' => Carbon::now()->addDays(3)->setTime(9, 0),
                'fecha_fin' => Carbon::now()->addDays(3)->setTime(10, 0),
                'tipo_evento_id' => $tipoReunion->id,
                'creado_por' => $admin->id,
                'ubicacion' => 'Oficina del profesor',
                'status' => true,
            ],
        ];
        
        foreach ($eventos as $evento) {
            Evento::create($evento);
        }
    }
} 