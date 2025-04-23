<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        return Plan::all();
    }

    public function show(Plan $plan)
    {
        return $plan;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|unique:planes_alquila,nombre',
            'max_publicaciones' => 'required|integer',
            'destacar' => 'required|boolean',
            'precio' => 'required|numeric',
        ]);

        return Plan::create($data);
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'nombre' => 'sometimes|string|unique:planes_alquila,nombre,' . $plan->id,
            'max_publicaciones' => 'sometimes|integer',
            'destacar' => 'sometimes|boolean',
            'precio' => 'sometimes|numeric',
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
