<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        return Plan::select('id', 'nombre', 'descripcion', 'duracion', 'max_publicaciones', 'destacar', 'precio')->get();
    }

    public function show(Plan $plan)
    {
        return response()->json($plan);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|unique:planes_alquila,nombre',
            'descripcion' => 'nullable|string',
            'duracion' => 'required|integer|min:0', // Asegúrate de que la duración sea un número positivo
            'max_publicaciones' => 'required|integer|min:1', // Asegúrate de que sea un número positivo
            'destacar' => 'required|boolean',
            'precio' => 'required|numeric|min:0', // Asegúrate de que el precio sea un número positivo
        ]);

        $plan = Plan::create($data);

        return response()->json($plan, 201); // Devuelve el plan recién creado
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'nombre' => 'sometimes|string|unique:planes_alquila,nombre,' . $plan->id,
            'descripcion' => 'sometimes|nullable|string', // Agrega la descripción
            'duracion' => 'sometimes|integer|min:0', // Agrega la duración
            'max_publicaciones' => 'sometimes|integer|min:1', // Agrega el máximo de publicaciones
            'destacar' => 'sometimes|boolean',
            'precio' => 'sometimes|numeric|min:0',
        ]);

        $plan->update($data);

        return $plan;
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return response()->noContent();
    }
}
