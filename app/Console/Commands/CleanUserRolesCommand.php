<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CleanUserRolesCommand extends Command
{
    protected $signature = 'user:clean-roles {email}';
    protected $description = 'Limpiar roles duplicados de un usuario';

    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuario no encontrado: {$email}");
            return;
        }
        
        $this->info("Usuario: {$user->name} ({$user->email})");
        $this->info("Roles actuales:");
        foreach ($user->roles as $role) {
            $this->line("- {$role->name}");
        }
        
        // Remover rol estudiante si existe
        if ($user->hasRole('estudiante')) {
            $user->removeRole('estudiante');
            $this->info("Rol 'estudiante' removido");
        }
        
        $this->info("Roles después de limpieza:");
        foreach ($user->fresh()->roles as $role) {
            $this->line("- {$role->name}");
        }
    }
} 