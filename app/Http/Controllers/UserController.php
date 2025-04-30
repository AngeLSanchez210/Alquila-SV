<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'role' => 'required|string|max:50',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return response()->json(['message' => 'Usuario creado correctamente'], 201);
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => $user,
        ]);
    }

    public function show(Request $request, User $user)
    {
        // Verifica si hay un usuario autenticado
        if (!$request->user() || $request->user()->id !== $user->id) {
            return redirect()->route('home');
        }

        return Inertia::render('User/infoUser', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'role' => 'required|string|max:50',
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']); // No actualizar si está vacía
        }

        $user->update($validated);

        return response()->json(['message' => 'Usuario actualizado correctamente']);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    public function getImage(User $user)
    {
        $image = $user->image()->first();
        return response()->json(['image_url' => $image ? $image->image_url : null]);
    }

    public function uploadImage(Request $request, User $user)
    {
        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048']);

        // Eliminar la imagen anterior si existe
        if ($user->image) {
            Storage::disk('public')->delete($user->image->image_url);
            $user->image()->delete();
        }

        // Subir la nueva imagen
        $path = $request->file('image')->store('user_images', 'public');

        // Guardar la nueva imagen en la base de datos
        $user->image()->create(['image_url' => $path]);

        return response()->json(['image_url' => $path]);
    }
}
