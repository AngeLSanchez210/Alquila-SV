<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seguidor;
use App\Models\User;


class SeguidorController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'seguidor_id' => 'required|integer|exists:users,id',
            'seguido_id' => 'required|integer|exists:users,id',
        ]);

        if ($request->seguidor_id === $request->seguido_id) {
            return response()->json(['message' => 'No puedes seguirte a ti mismo.'], 400);
        }

        // Verificar si ya existe el seguidor
        $exists = Seguidor::where('seguidor_id', $request->seguidor_id)
            ->where('seguido_id', $request->seguido_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'El usuario ya sigue a este perfil'], 409);
        }

        Seguidor::create([
            'seguidor_id' => $request->seguidor_id,
            'seguido_id' => $request->seguido_id,
        ]);

        return response()->json(['message' => 'Usuario seguido con éxito'], 201);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'seguidor_id' => 'required|integer|exists:users,id',
            'seguido_id' => 'required|integer|exists:users,id',
        ]);

        $deleted = Seguidor::where('seguidor_id', $request->seguidor_id)
            ->where('seguido_id', $request->seguido_id)
            ->delete();

        if ($deleted) {
            return response()->json(['message' => 'Usuario dejado de seguir con éxito'], 200);
        }

        return response()->json(['message' => 'No se encontró la relación de seguimiento'], 404);
    }

    public function checkFollowing($seguidor_id, $seguido_id)
    {
        $isFollowing = Seguidor::where('seguidor_id', $seguidor_id)
            ->where('seguido_id', $seguido_id)
            ->exists();

        return response()->json(['isFollowing' => $isFollowing], 200);
    }

    public function listarSeguidores($userId)
    {
        // Validar si el usuario existe
        if (!User::where('id', $userId)->exists()) {
            return response()->json(['message' => 'El usuario no existe.'], 404);
        }

        // Obtener los IDs de los seguidores
        $seguidorIds = Seguidor::where('seguido_id', $userId)->pluck('seguidor_id');

        // Obtener los datos de los seguidores
        $seguidores = User::whereIn('id', $seguidorIds)
            ->select('id', 'name')
            ->get();

        // Verificar si hay seguidores
        if ($seguidores->isEmpty()) {
            return response()->json(['message' => 'El usuario no tiene seguidores.'], 200);
        }

        return response()->json($seguidores, 200);
    }

    public function listarSeguidos($userId)
    {
        // Validar si el usuario existe
        if (!User::where('id', $userId)->exists()) {
            return response()->json(['message' => 'El usuario no existe.'], 404);
        }

        // Obtener los IDs de los usuarios seguidos
        $seguidoIds = Seguidor::where('seguidor_id', $userId)->pluck('seguido_id');

        // Obtener los datos de los usuarios seguidos
        $seguidos = User::whereIn('id', $seguidoIds)
            ->select('id', 'name')
            ->get();

        // Verificar si hay usuarios seguidos
        if ($seguidos->isEmpty()) {
            return response()->json(['message' => 'El usuario no sigue a nadie.'], 200);
        }

        return response()->json($seguidos, 200);
    }
}
