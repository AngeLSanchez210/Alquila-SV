<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;


class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'nombre' => 'gratis',
            'max_publicaciones' => 4,
            'destacar' => false,
            'precio' => 0.00,
        ]);
    
        Plan::create([
            'nombre' => 'basico',
            'max_publicaciones' => 10,
            'destacar' => true,
            'precio' => 9.99, // Precio mensual
        ]);
    
        Plan::create([
            'nombre' => 'premium',
            'max_publicaciones' => 999,
            'destacar' => true,
            'precio' => 19.99, // Precio mensual
        ]);
    }
}
