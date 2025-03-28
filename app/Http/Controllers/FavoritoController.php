<?php

namespace App\Http\Controllers;

use App\Models\Favorito;
use Illuminate\Http\Request;

class FavoritoController extends Controller
{
    public function index()
    {
        return Favorito::with('usuario', 'articulo')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'articulo_id' => 'required|exists:articulos,id',
        ]);

        return Favorito::create($data);
    }

    public function destroy(Favorito $favorito)
    {
        $favorito->delete();
        return response()->noContent();
    }
}
