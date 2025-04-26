<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Plan;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    /**
     * Muestra una lista de pagos.
     */
    public function index()
    {
        return Pago::all();
    }

    /**
     * Almacena un nuevo pago.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'metodo' => 'required|in:tarjeta,paypal',
            'plan_id' => 'required|exists:planes_alquila,id', // Validar que el plan exista en la tabla correcta
            'transaccion_id' => 'nullable|string',
        ]);

        $plan = Plan::findOrFail($data['plan_id']); // Obtener el plan seleccionado

        $pago = Pago::create([
            'metodo' => $data['metodo'],
            'monto' => $plan->precio, // Usar el precio del plan
            'estado' => 'completado', // Estado por defecto
            'fecha_pago' => now(), // Fecha actual
            'transaccion_id' => $data['transaccion_id'],
            'user_id' => $request->user()->id, // Asociar el pago al usuario autenticado
            'plan_id' => $plan->id, // Asociar el pago al plan
        ]);

        return response()->json($pago, 201);
    }

    /**
     * Muestra un pago específico.
     */
    public function show(Pago $pago)
    {
        return $pago;
    }

    /**
     * Actualiza un pago existente.
     */
    public function update(Request $request, Pago $pago)
    {
        $data = $request->validate([
            'metodo' => 'sometimes|in:tarjeta,paypal',
            'monto' => 'sometimes|numeric|min:0',
            'estado' => 'sometimes|in:pendiente,completado,fallido',
            'transaccion_id' => 'sometimes|string',
            'user_id' => 'sometimes|exists:users,id', // Validar que el usuario exista
            'plan_id' => 'sometimes|exists:planes_alquila,id', // Validar que el plan exista
            'fecha_pago' => 'sometimes|date',
        ]);

        $pago->update($data);

        return $pago;
    }

    /**
     * Elimina un pago.
     */
    public function destroy(Pago $pago)
    {
        $pago->delete();

        return response()->noContent();
    }
}
