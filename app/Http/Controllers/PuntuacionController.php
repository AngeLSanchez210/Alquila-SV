<?php

namespace App\Http\Controllers;

use App\Models\Puntuacion;
use Illuminate\Http\Request;

class PuntuacionController extends Controller
{
    public function index()
    {
        return Puntuacion::with('usuario', 'articulo')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'articulo_id' => 'required|exists:articulos,id',
            'puntuacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string',
        ]);

        return Puntuacion::create($data);
    }

    public function destroy(Puntuacion $puntuacion)
    {
        $puntuacion->delete();
        return response()->noContent();
    }
}
