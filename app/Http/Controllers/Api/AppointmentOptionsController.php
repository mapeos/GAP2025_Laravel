<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
// Medical models removed - not supported by current database schema
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * API Controller for appointment options (professors, specialties, treatments)
 */
class AppointmentOptionsController extends Controller
{
    /**
     * Helper method to check if user has a specific role
     * 
     * @param User|null $user
     * @param string $role
     * @return bool
     */
    private function userHasRole($user, string $role): bool
    {
        if (!$user) {
            return false;
        }
        
        // Check if user has the hasRole method (from Spatie Laravel Permission)
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }
        
        return false;
    }

    /**
     * 👨‍🏫 Get available professors for academic appointments
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function professors()
    {
        try {
            $profesores = User::whereHas('roles', function ($query) {
                $query->where('name', 'profesor');
            })
            ->where('status', 'activo')
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Profesores obtenidos correctamente',
                'data' => $profesores
            ]);
            
        } catch (\Exception $e) {
            Log::error('[API][APPOINTMENT_OPTIONS] Error al obtener profesores', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la lista de profesores'
            ], 500);
        }
    }

    /**
     * 🏥 Get available medical specialties
     * Note: Medical appointments not supported by current database schema
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function specialties()
    {
        return response()->json([
            'success' => false,
            'message' => 'Las especialidades médicas no están disponibles en la versión actual',
            'data' => []
        ], 501); // Not Implemented
    }

    /**
     * 💊 Get available treatments for a medical specialty
     * Note: Medical appointments not supported by current database schema
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function treatments(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Los tratamientos médicos no están disponibles en la versión actual',
            'data' => []
        ], 501); // Not Implemented
    }

    /**
     * 👨‍⚕️ Get available doctors for a medical specialty
     * Note: Medical appointments not supported by current database schema
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doctors(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Los doctores médicos no están disponibles en la versión actual',
            'data' => []
        ], 501); // Not Implemented
    }

    /**
     * 📋 Get all appointment options in one request
     * Note: Only academic appointments supported by current database schema
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        try {
            // Get professors (only academic appointments supported)
            $profesores = User::whereHas('roles', function ($query) {
                $query->where('name', 'profesor');
            })
            ->where('status', 'activo')
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Opciones de citas obtenidas correctamente (solo académicas)',
                'data' => [
                    'profesores' => $profesores,
                    'especialidades' => [], // Not supported
                    'tratamientos_por_especialidad' => [], // Not supported
                    'doctores_por_especialidad' => [] // Not supported
                ],
                'note' => 'Solo las citas académicas están disponibles en la versión actual'
            ]);
            
        } catch (\Exception $e) {
            Log::error('[API][APPOINTMENT_OPTIONS] Error al obtener todas las opciones', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las opciones de citas'
            ], 500);
        }
    }

    /**
     * 🕐 Get available time slots for a specific date and professor
     * Note: Only academic appointments supported by current database schema
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function availableSlots(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Los horarios disponibles no están implementados en la versión actual',
            'data' => []
        ], 501); // Not Implemented
    }
}
