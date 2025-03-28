<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        return Categoria::with('articulos')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string',
        ]);

        return Categoria::create($data);
    }

    public function show(Categoria $categoria)
    {
        return $categoria->load('articulos');
    }

    public function update(Request $request, Categoria $categoria)
    {
        $data = $request->validate([
            'nombre' => 'string',
        ]);

        $categoria->update($data);
        return $categoria;
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        return response()->noContent();
    }
}
