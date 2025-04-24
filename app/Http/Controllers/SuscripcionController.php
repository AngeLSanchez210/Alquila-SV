<?php

namespace App\Http\Controllers;

use App\Models\Suscripcion;
use App\Models\Plan;
use Illuminate\Http\Request;

class SuscripcionController extends Controller
{
    public function index()
    {
        return Suscripcion::with('usuario', 'articulo', 'plan')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'fecha_inicio' => 'required|date',
            'estado' => 'required|in:activa,cancelada',
            'plan_id' => 'required|exists:planes_alquila,id',
        ]);

        $plan = Plan::findOrFail($data['plan_id']);
        $data['fecha_fin'] = now()->addDays($plan->duracion);

        return Suscripcion::create($data);
    }

    public function destroy(Suscripcion $suscripcion)
    {
        $suscripcion->delete();
        return response()->noContent();
    }
}
