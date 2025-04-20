<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categorias')->insert([
            ['nombre' => 'Herramientas'],
            ['nombre' => 'Electrodomésticos'],
            ['nombre' => 'Vehículos'],
            ['nombre' => 'Ropa y disfraces'],
            ['nombre' => 'Deportes'],
            ['nombre' => 'Tecnología'],
            ['nombre' => 'Muebles'],
            ['nombre' => 'Juguetes'],
            ['nombre' => 'Jardinería'],
            ['nombre' => 'Cámaras y fotografía'],
            ['nombre' => 'Audio y video'],
            ['nombre' => 'Camping'],
            ['nombre' => 'Fiestas y eventos'],
            ['nombre' => 'Musicales e instrumentos'],
            ['nombre' => 'Oficina'],
            ['nombre' => 'Bebés y niños'],
            ['nombre' => 'Libros y revistas'],
            ['nombre' => 'Gaming'],
            ['nombre' => 'Accesorios para autos'],
            ['nombre' => 'Maquinaria pesada'],
            ['nombre' => 'Artículos de cocina'],
            ['nombre' => 'Decoración'],
            ['nombre' => 'Fitness y ejercicio'],
            ['nombre' => 'Moda y accesorios'],
            ['nombre' => 'Fotocabinas'],
            ['nombre' => 'Espacios para reuniones'],
            ['nombre' => 'Drones'],
            ['nombre' => 'Patinetas y bicis'],
            ['nombre' => 'Artículos de limpieza'],
            ['nombre' => 'Carpas y toldos'],
        ]);
    }
}
