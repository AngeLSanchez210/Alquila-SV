<?php

namespace App\Http\Controllers;

use App\Models\Seguidor;
use Illuminate\Http\Request;

class SeguidorController extends Controller
{
    // Obtener todos los seguidores con relaciones
    public function index()
    {
        return Seguidor::with('seguidor', 'seguido')->get();
    }

    // Crear un nuevo seguidor
    public function store(Request $request)
    {
        $data = $request->validate([
            'seguidor_id' => 'required|exists:users,id', // ID del usuario que sigue
            'seguido_id' => 'required|exists:users,id', // ID del usuario seguido
        ]);

        // Verificar si ya existe la relación
        $existe = Seguidor::where('seguidor_id', $data['seguidor_id'])
                          ->where('seguido_id', $data['seguido_id'])
                          ->exists();

        if ($existe) {
            return response()->json(['message' => 'Ya sigues a este usuario'], 200);
        }

        // Crear la relación
        $seguidor = Seguidor::create($data);

        return response()->json($seguidor, 201);
    }

    // Eliminar un seguidor
    public function destroy(Seguidor $seguidor)
    {
        $seguidor->delete();
        return response()->noContent();
    }
}
