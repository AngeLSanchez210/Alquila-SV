<?php

namespace App\Http\Controllers;

use App\Models\Favorito;
use Illuminate\Http\Request;


class FavoritoController extends Controller
{

 
    public function index()
    {
        return Favorito::with('articulo.imagenes')->get(); // Incluye las imágenes del artículo
    }

    public function store(Request $request)
    {
        $request->validate([
            'articulo_id' => 'required|exists:articulos,id',
        ]);

        $user = $request->user();

        // Verifica si ya está en favoritos
        $existe = Favorito::where('usuario_id', $user->id)
                          ->where('articulo_id', $request->articulo_id)
                          ->first();

        if ($existe) {
            return response()->json(['message' => 'Ya está en favoritos'], 200);
        }

        $favorito = new Favorito();
        $favorito->usuario_id = $user->id;
        $favorito->articulo_id = $request->articulo_id;
        $favorito->save();

        return response()->json(['message' => 'Artículo añadido a favoritos'], 201);
    }


    public function destroy(Favorito $favorito)
    {
        $favorito->delete();
        return response()->noContent();
    }
}
