<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;

class ArticuloController extends Controller
{
    public function index()
    {
        return Articulo::with('categorias', 'usuario')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric',
            'estado' => 'required|in:disponible,alquilado',
            'usuario_id' => 'required|exists:users,id',
        ]);

        return Articulo::create($data);
    }

    public function show(Articulo $articulo)
    {
        return $articulo->load('categorias', 'usuario');
    }

    public function update(Request $request, Articulo $articulo)
    {
        $data = $request->validate([
            'nombre' => 'string',
            'descripcion' => 'string',
            'precio' => 'numeric',
            'estado' => 'in:disponible,alquilado',
        ]);

        $articulo->update($data);
        return $articulo;
    }

    public function destroy(Articulo $articulo)
    {
        $articulo->delete();
        return response()->noContent();
    }
}
