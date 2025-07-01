namespace App\Services;

use App\Models\Evento;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EventoService
{
    public function validarDatos($request)
    {
        // Lógica de validación común
    }
    
    public function actualizarEvento(Evento $evento, $datos)
    {
        // Lógica común para actualizar eventos
    }
    
    public function eliminarEvento(Evento $evento)
    {
        // Lógica común para eliminar eventos
    }
    
    public function clearEventosCache($userId = null)
    {
        if ($userId) {
            Cache::forget('eventos.user.' . $userId);
            Cache::forget('eventos.api.user.' . $userId);
        } else {
            // Limpiar cache para todos los usuarios
            $userIds = DB::table('users')->pluck('id');
            foreach ($userIds as $userId) {
                Cache::forget('eventos.user.' . $userId);
                Cache::forget('eventos.api.user.' . $userId);
            }
        }
    }
}