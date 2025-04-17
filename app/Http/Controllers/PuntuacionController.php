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
       
        $request->validate([
            'articulo_id' => 'required|exists:articulos,id',  
            'puntuacion' => 'required|integer|min:1|max:5',   
            'comentario' => 'nullable|string',  
        ]);

        
        $user = $request->user();

        
        $existe = Puntuacion::where('usuario_id', $user->id)
                            ->where('articulo_id', $request->articulo_id)
                            ->first();

       
        if ($existe) {
            return response()->json(['message' => 'Ya has puntuado este artículo.'], 409);  
        }

       
        $puntuacion = new Puntuacion();
        $puntuacion->usuario_id = $user->id;  
        $puntuacion->articulo_id = $request->articulo_id;  
        $puntuacion->puntuacion = $request->puntuacion;  
        $puntuacion->comentario = $request->comentario;  
        $puntuacion->save();  

       
        return response()->json($puntuacion, 201);  
    }

    public function verificarPuntuacion($articuloId, $userId)
{
    $puntuacion = Puntuacion::where('articulo_id', $articuloId)
                            ->where('user_id', $userId)
                            ->first();

    return response()->json($puntuacion);
}

}
