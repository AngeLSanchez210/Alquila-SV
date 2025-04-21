<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\ImgArticulo;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ArticuloController extends Controller
{
    public function index(Request $request)
    {
        // Iniciar la consulta base
        $query = Articulo::with('imagenes', 'usuario', 'categoria');
        
        // Aplicar filtro de precio si está presente
        if ($request->has('precio_max') && is_numeric($request->precio_max)) {
            $query->where('precio', '<=', $request->precio_max);
        }
        
        // Aplicar filtro de categorías si está presente
        if ($request->has('categorias') && is_array($request->categorias) && count($request->categorias) > 0) {
            $query->whereHas('categoria', function($q) use ($request) {
                $q->whereIn('nombre', $request->categorias);
            });
        }
        
        $articulos = $query->get();
        return response()->json($articulos);
    }

    public function vista()
    {
        $articulos = Articulo::with('imagenes', 'usuario', 'categoria')->get();
        $categorias = Categoria::all()->pluck('nombre');
        
        return Inertia::render('Articulos/Index', [
            'articulos' => $articulos,
            'categorias' => $categorias
        ]);
    }

    public function create()
    {
        $categorias = Categoria::all();
    
        return Inertia::render('addArticles', [
            'categorias' => $categorias,
        ]);
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric',
            'estado' => 'required|in:disponible,alquilado',
            'idcategoria' => 'nullable|exists:categorias,id', // Validación para idcategoria
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        $articulo = Articulo::create([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'precio' => $data['precio'],
            'estado' => $data['estado'],
            'idcategoria' => $request->idcategoria, // Guardar el idcategoria
            'usuario_id' => auth()->id(), 
        ]);

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $imagePath = $imagen->store('articulos', 'public');
                ImgArticulo::create([
                    'articulo_id' => $articulo->id,
                    'link' => $imagePath,
                ]);
            }
        }

        return redirect()->route('articulos')->with('success', 'Artículo creado con éxito.');
    }

    public function show(Articulo $articulo)
    {
        $articulo->load('categoria', 'usuario', 'imagenes');
        
        $nombreUsuario = $articulo->usuario->name; 
        
        return response()->json([
            'articulo' => $articulo,
            'nombre_usuario' => $nombreUsuario, 
        ]);
    }

    public function update(Request $request, Articulo $articulo)
    {
        $data = $request->validate([
            'nombre' => 'string',
            'descripcion' => 'string',
            'precio' => 'numeric',
            'estado' => 'in:disponible,alquilado',
            'idcategoria' => 'nullable|exists:categorias,id', // Agregar validación para idcategoria
        ]);

        $articulo->update($data);
        
        return response()->json([
            'message' => 'Artículo actualizado correctamente',
            'articulo' => $articulo,
        ]);
    }

    public function destroy(Articulo $articulo)
{
    try {
        // Verificar que el usuario actual es el propietario del artículo
        if ($articulo->usuario_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        
        // Eliminar imágenes asociadas
        foreach ($articulo->imagenes as $imagen) {
            if (Storage::exists('public/' . $imagen->link)) {
                Storage::delete('public/' . $imagen->link);
            }
            $imagen->delete();
        }

        // Eliminar artículo
        $articulo->delete();
        
        return response()->json(['message' => 'Artículo eliminado correctamente']);
    } catch (\Exception $e) {
        // Registrar el error
        \Log::error('Error al eliminar artículo: ' . $e->getMessage());
        return response()->json(['error' => 'Error al eliminar el artículo'], 500);
    }
}
    public function getUserArticulos()
    {
        $articulos = Articulo::where('usuario_id', auth()->id())->with('imagenes', 'categoria')->get();
        return response()->json($articulos);
    }

    public function eliminarImagen(ImgArticulo $imagen)
    {
        if ($imagen->articulo->imagenes()->count() <= 1) {
            return response()->json(['error' => 'No puedes eliminar todas las imágenes.'], 400);
        }

        Storage::delete('public/' . $imagen->link);
        $imagen->delete();

        return response()->json(['message' => 'Imagen eliminada correctamente.']);
    }

    public function subirImagenes(Request $request, Articulo $articulo)
    {
        $request->validate([
            'imagenes.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagenes = [];
        foreach ($request->file('imagenes') as $imagen) {
            $path = $imagen->store('articulos', 'public');
            $imagenes[] = $articulo->imagenes()->create(['link' => $path]);
        }

        return response()->json($imagenes);
    }
    
    // Nuevo método para obtener todas las categorías
    public function getCategorias()
    {
        $categorias = Categoria::all()->pluck('nombre');
        return response()->json($categorias);
    }
}