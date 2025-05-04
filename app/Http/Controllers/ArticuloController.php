<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\ImgArticulo;
use App\Models\Categoria;
use App\Models\Suscripcion;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ArticuloController extends Controller
{
    public function index(Request $request)
{
    $query = Articulo::with('imagenes', 'usuario', 'categoria')
        ->with(['usuario.suscripciones.plan']) // precargar para evitar N+1

        ->withCount(['usuario as es_destacado' => function ($q) {
            $q->whereHas('suscripciones', function ($s) {
                $s->where('estado', 'activa')
                  ->whereHas('plan', function ($p) {
                      $p->where('destacar', true);
                  });
            });
        }]);


    if ($request->has('precio_max') && is_numeric($request->precio_max)) {
        $query->where('precio', '<=', $request->precio_max);
    }


    if ($request->has('categorias') && is_array($request->categorias) && count($request->categorias) > 0) {
        $query->whereHas('categoria', function($q) use ($request) {
            $q->whereIn('nombre', $request->categorias);
        });
    }

   
    $query->orderByDesc('es_destacado');

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
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado.'], 401);
        }

        // Verificar la cantidad de artículos publicados
        $articulosPublicados = Articulo::where('usuario_id', $user->id)->count();
        $suscripcion = $user->suscripciones()->where('estado', 'activa')->first();

        if ($suscripcion && $suscripcion->plan) {
            $maxPublicaciones = $suscripcion->plan->max_publicaciones;
        } else {
            $planGratis = Plan::find(1); // Plan con ID 1
            if (!$planGratis) {
                return response()->json([
                    'error' => 'Plan gratuito no encontrado.',
                    'message' => 'No se puede verificar el límite de publicaciones.',
                ], 500);
            }
            $maxPublicaciones = $planGratis->max_publicaciones;
        }

        if ($articulosPublicados >= $maxPublicaciones) {
            return response()->json([
                'error' => 'Límite alcanzado',
                'message' => "No puedes publicar más de $maxPublicaciones artículos con tu plan actual."
            ], 403);
        }

        // Validar y guardar artículo
        $data = $request->validate([
            'nombre' => 'required|string',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric',
            'estado' => 'required|in:disponible,alquilado',
            'idcategoria' => 'nullable|exists:categorias,id',
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $articulo = Articulo::create([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'precio' => $data['precio'],
            'estado' => $data['estado'],
            'idcategoria' => $request->idcategoria,
            'usuario_id' => $user->id,
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

        return response()->json([
            'message' => 'Artículo creado con éxito.',
            'articulo' => $articulo,
        ]);
    }

    public function show(Articulo $articulo)
    {
        $articulo->load('categoria', 'usuario', 'imagenes', 'puntuaciones.usuario');
    
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
        // // Verificar que el usuario actual es el propietario del artículo
        // if ($articulo->usuario_id !== auth()->id()) {
        //     return response()->json(['error' => 'No autorizado'], 403);
        // }
        
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

    public function apiSearch(Request $request)
    {
        $query = $request->input('q');
    
        $articulos = Articulo::with('imagenes')
            ->withCount(['usuario as es_destacado' => function ($q) {
                $q->whereHas('suscripciones', function ($s) {
                    $s->where('estado', 'activa')
                      ->whereHas('plan', function ($p) {
                          $p->where('destacar', true);
                      });
                });
            }])
            ->where(function ($q) use ($query) {
                $q->where('nombre', 'like', "%$query%")
                  ->orWhere('descripcion', 'like', "%$query%")
                  ->orWhereHas('categoria', function ($cat) use ($query) {
                      $cat->where('nombre', 'like', "%$query%");
                  });
            })
            ->orderByDesc('es_destacado')
            ->limit(10)
            ->get();
    
        return response()->json($articulos);
    }
    
    
        
public function ver(Articulo $articulo)
{
    $articulo->load('categoria', 'usuario', 'imagenes');

    return Inertia::render('ArticuloDetalle', [
        'articulo' => $articulo,
        'nombre_usuario' => $articulo->usuario->name,
        'id_usuario' => $articulo->usuario->id, // Agregar el ID del usuario
    ]);
}




}
