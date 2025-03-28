<?php

namespace App\Http\Controllers;

use App\Models\Suscripcion;
use Illuminate\Http\Request;

class SuscripcionController extends Controller
{
    public function index()
    {
        return Suscripcion::with('usuario', 'articulo')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'articulo_id' => 'required|exists:articulos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date',
            'estado' => 'required|in:activa,cancelada',
            'plan' => 'required|in:gratis,basico,premium',
        ]);

        return Suscripcion::create($data);
    }

    public function destroy(Suscripcion $suscripcion)
    {
        $suscripcion->delete();
        return response()->noContent();
    }
}
