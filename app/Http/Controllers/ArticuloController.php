<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\ImgArticulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function uploadImages(Request $request, Articulo $articulo)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $uploadedImages = [];
        foreach ($request->file('images') as $image) {
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/articulos', $filename);

            $imgArticulo = ImgArticulo::create([
                'articulo_id' => $articulo->id,
                'link' => Storage::url($path),
            ]);

            $uploadedImages[] = $imgArticulo;
        }

        return response()->json($uploadedImages, 201);
    }
}
