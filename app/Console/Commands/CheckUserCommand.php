<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Facultativo;

class CheckUserCommand extends Command
{
    protected $signature = 'user:check {email}';
    protected $description = 'Verificar el estado de un usuario';

    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuario no encontrado: {$email}");
            return;
        }
        
        $this->info("Usuario encontrado:");
        $this->line("ID: {$user->id}");
        $this->line("Nombre: {$user->name}");
        $this->line("Email: {$user->email}");
        $this->line("Status: {$user->status}");
        $this->line("Email verificado: " . ($user->email_verified_at ? 'Sí' : 'No'));
        
        $this->info("\nRoles:");
        foreach ($user->roles as $role) {
            $this->line("- {$role->name}");
        }
        
        $this->info("\nPermisos:");
        foreach ($user->getAllPermissions() as $permission) {
            $this->line("- {$permission->name}");
        }
        
        // Verificar si es facultativo
        $facultativo = Facultativo::where('user_id', $user->id)->first();
        if ($facultativo) {
            $this->info("\nDatos de Facultativo:");
            $this->line("Número colegiado: {$facultativo->numero_colegiado}");
            $this->line("Especialidad ID: {$facultativo->especialidad_id}");
            $this->line("Activo: " . ($facultativo->activo ? 'Sí' : 'No'));
        } else {
            $this->warn("\nNo es facultativo");
        }
    }
} 