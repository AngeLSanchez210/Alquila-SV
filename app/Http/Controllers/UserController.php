<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller {
    public function index()
    {
        return response()->json(User::all());
    }
    
    public function edit(User $user) {
        return Inertia::render('Users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'role' => 'required|string|max:50',
        ]);
        
        // Manejar la contraseña si se proporciona
        if ($request->has('password') && !empty($request->password)) {
            $validated['password'] = bcrypt($request->password);
        }
            
        $user->update($validated);
        
        return response()->json(['message' => 'Usuario actualizado correctamente']);
    }
 
    public function destroy(User $user) {
        $user->delete();
        
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}