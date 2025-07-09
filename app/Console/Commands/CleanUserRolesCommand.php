<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Participacion;

class CleanUserRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:participaciones-duplicadas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia participaciones duplicadas en la tabla participacion';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Buscando participaciones duplicadas...');

        // Encontrar duplicados basados en curso_id y persona_id
        $duplicados = DB::table('participacion')
            ->select('curso_id', 'persona_id', DB::raw('COUNT(*) as count'))
            ->groupBy('curso_id', 'persona_id')
            ->having('count', '>', 1)
            ->get();

        if ($duplicados->isEmpty()) {
            $this->info('✅ No se encontraron participaciones duplicadas.');
        } else {
            $this->warn("⚠️  Se encontraron {$duplicados->count()} grupos de participaciones duplicadas.");

            $totalEliminadas = 0;

            foreach ($duplicados as $duplicado) {
                $this->info("Procesando: Curso ID {$duplicado->curso_id}, Persona ID {$duplicado->persona_id} ({$duplicado->count} registros)");

                // Obtener todas las participaciones para este curso y persona
                $participaciones = Participacion::where('curso_id', $duplicado->curso_id)
                    ->where('persona_id', $duplicado->persona_id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                // Mantener la primera (más antigua) y eliminar las demás
                $primera = $participaciones->first();
                $eliminar = $participaciones->skip(1);

                foreach ($eliminar as $participacion) {
                    $participacion->delete();
                    $totalEliminadas++;
                    $this->line("  - Eliminada participación ID: {$participacion->id}");
                }
            }

            $this->info("✅ Proceso completado. Se eliminaron {$totalEliminadas} participaciones duplicadas.");
        }

        // Sincronizar usuarios sin persona asociada
        $this->sincronizarUsuariosSinPersona();
    }

    /**
     * Sincroniza usuarios que no tienen persona asociada
     */
    private function sincronizarUsuariosSinPersona()
    {
        $this->info('🔍 Sincronizando usuarios sin persona asociada...');

        $usuariosSinPersona = \App\Models\User::whereDoesntHave('persona')->get();

        if ($usuariosSinPersona->isEmpty()) {
            $this->info('✅ Todos los usuarios ya tienen persona asociada.');
            return;
        }
        
        $this->warn("⚠️  Se encontraron {$usuariosSinPersona->count()} usuarios sin persona asociada.");

        $totalCreadas = 0;

        foreach ($usuariosSinPersona as $usuario) {
            try {
                $persona = \App\Models\Persona::create([
                    'nombre' => $usuario->name,
                    'apellido1' => '',
                    'apellido2' => '',
                    'dni' => 'user_' . $usuario->id, // Valor único temporal
                    'tfno' => '',
                    'direccion_id' => null,
                    'user_id' => $usuario->id,
                ]);

                $totalCreadas++;
                $this->line("  ✅ Creada persona para usuario: {$usuario->name} ({$usuario->email})");

            } catch (\Exception $e) {
                $this->error("  ❌ Error al crear persona para usuario {$usuario->name}: {$e->getMessage()}");
            }
        }

        $this->info("✅ Sincronización completada. Se crearon {$totalCreadas} personas.");
    }
} 