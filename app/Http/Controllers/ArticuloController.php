<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\ImgArticulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class ArticuloController extends Controller
{
    public function index()
    {
        $articulos = Articulo::with('imagenes')->get();
        return response()->json($articulos);
    }

    public function create()
    {
        return Inertia::render('Articulos/Create'); 
    }


public function store(Request $request)
{
    // Validación de datos
    $data = $request->validate([
        'nombre' => 'required|string',
        'descripcion' => 'required|string',
        'precio' => 'required|numeric',
        'estado' => 'required|in:disponible,alquilado',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $articulo = Articulo::create([
        'nombre' => $data['nombre'],
        'descripcion' => $data['descripcion'],
        'precio' => $data['precio'],
        'estado' => $data['estado'],
        'usuario_id' => auth()->id(),
    ]);

   
    if ($request->hasFile('imagen')) {
        $imagePath = $request->file('imagen')->store('articulos', 'public');
        ImgArticulo::create([
            'articulo_id' => $articulo->id,
            'link' => $imagePath,
        ]);
    }

    // Redirigir despues de crear el artículo
    return Inertia::render('Principal', [
        'message' => 'Artículo creado correctamente', 
    ]);
}


    public function show(Articulo $articulo)
    {
        return $articulo->load('categorias', 'usuario', 'imagenes');
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
        return response()->json([
            'message' => 'Artículo actualizado correctamente',
            'articulo' => $articulo,
        ]);
    }

    public function destroy(Articulo $articulo)
    {
       
        foreach ($articulo->imagenes as $imagen) {
            Storage::delete('public/' . $imagen->link);
            $imagen->delete();
        }

       
        $articulo->delete();
        
        return response()->json(['message' => 'Artículo eliminado correctamente']);
    }
}
