<?php

namespace App\Http\Controllers;

use App\Models\Seguidor;
use Illuminate\Http\Request;

class SeguidorController extends Controller
{
    public function index()
    {
        return Seguidor::with('seguidor', 'seguido')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'seguidor_id' => 'required|exists:users,id',
            'seguido_id' => 'required|exists:users,id',
        ]);

        return Seguidor::create($data);
    }

    public function destroy(Seguidor $seguidor)
    {
        $seguidor->delete();
        return response()->noContent();
    }
}
