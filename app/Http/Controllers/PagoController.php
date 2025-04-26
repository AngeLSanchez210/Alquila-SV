<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Plan;
use App\Models\Suscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        try {
            $data = $request->validate([
                'metodo' => 'required|in:tarjeta,paypal',
                'plan_id' => 'required|exists:planes_alquila,id',
                'transaccion_id' => 'nullable|string',
            ]);

            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado.'], 401);
            }

            $plan = Plan::findOrFail($data['plan_id']);
            if (!$plan->duracion) {
                return response()->json(['error' => 'El plan no tiene duración definida.'], 400);
            }

            // Comenzar transacción de base de datos
            DB::beginTransaction();

            // Crear el pago primero
            $pago = Pago::create([
                'metodo' => $data['metodo'],
                'monto' => $plan->precio,
                'estado' => 'completado',
                'fecha_pago' => now(),
                'transaccion_id' => $data['transaccion_id'],
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ]);

            // Revisar si el usuario ya tiene una suscripción activa
            $suscripcion = Suscripcion::where('usuario_id', $user->id)
                ->where('estado', 'activa')
                ->where('fecha_fin', '>', now())
                ->first();

            if ($suscripcion) {
                if ($suscripcion->plan_id == 1) {
                    // Cancelar la suscripción actual
                    $suscripcion->update([
                        'estado' => 'cancelada',
                        'fecha_fin' => now(),
                    ]);

                    // Crear nueva suscripción
                    Suscripcion::create([
                        'usuario_id' => $user->id,
                        'plan_id' => $plan->id,
                        'pago_id' => $pago->id,
                        'fecha_inicio' => now(),
                        'fecha_fin' => now()->addDays($plan->duracion),
                        'estado' => 'activa',
                    ]);
                } else {
                    // Revertir pago y cancelar operación
                    DB::rollBack();
                    return response()->json([
                        'error' => 'Ya tienes una suscripción activa. No puedes adquirir un nuevo plan hasta que termine.'
                    ], 400);
                }
            } else {
                // Revisar si no se creó una suscripción recientemente (doble pago protección)
                $suscripcionExistente = Suscripcion::where('usuario_id', $user->id)
                    ->where('estado', 'activa')
                    ->where('created_at', '>=', now()->subMinutes(5))
                    ->first();

                if (!$suscripcionExistente) {
                    Suscripcion::create([
                        'usuario_id' => $user->id,
                        'plan_id' => $plan->id,
                        'pago_id' => $pago->id,
                        'fecha_inicio' => now(),
                        'fecha_fin' => now()->addDays($plan->duracion),
                        'estado' => 'activa',
                    ]);
                }
            }

            // Confirmar transacción
            DB::commit();

            return response()->json($pago, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
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
