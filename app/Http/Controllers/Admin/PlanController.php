<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Inertia\Inertia;
use Inertia\Response;

class PlanController extends Controller
{
    public function index(): Response
    {
        $planes = Plan::all(); // Obtén todos los planes desde la base de datos

        return Inertia::render('admin/Planes', [
            'planes' => $planes, // Pasa los planes a la vista
        ]);
    }
}
