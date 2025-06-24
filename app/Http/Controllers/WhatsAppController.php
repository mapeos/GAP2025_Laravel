<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsAppService;

class WhatsAppController extends Controller
{
    public function showForm()
    {
        return view('admin.whatsapp');
    }

    public function send(Request $request, WhatsAppService $whatsappService)
    {
        // No es necesario validar porque los datos están hardcodeados en el servicio
        $result = $whatsappService->sendMessage(null, null);
        return back()->with('status', 'Respuesta: ' . json_encode($result));
    }
}
