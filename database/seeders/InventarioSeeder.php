<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaInventario;
use App\Models\Material;

class InventarioSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Anestesia',                    'color' => '#DC3545', 'descripcion' => 'Anestesicos locales y vasoconstrictores'],
            ['nombre' => 'Instrumental',                  'color' => '#6C757D', 'descripcion' => 'Limas, fresas, instrumentos de corte'],
            ['nombre' => 'Materiales de Obturación',      'color' => '#0D6EFD', 'descripcion' => 'Resinas, cementos, ionómeros'],
            ['nombre' => 'Materiales de Impresión',       'color' => '#FD7E14', 'descripcion' => 'Alginatos, siliconas, yesos'],
            ['nombre' => 'Higiene y Profilaxis',          'color' => '#28A745', 'descripcion' => 'Pastas, cepillos, sellantes'],
            ['nombre' => 'Desinfección y Esterilización', 'color' => '#6B21A8', 'descripcion' => 'Hipoclorito, alcohol, glutaraldehido'],
            ['nombre' => 'Radiología',                    'color' => '#17A2B8', 'descripcion' => 'Películas, revelador, fijador'],
            ['nombre' => 'Consumibles',                   'color' => '#856404', 'descripcion' => 'Guantes, tapabocas, baberos, vasos'],
            ['nombre' => 'Medicamentos',                  'color' => '#E83E8C', 'descripcion' => 'Antibióticos, antiinflamatorios, analgésicos'],
        ];

        $categoriasCreadas = [];
        foreach ($categorias as $cat) {
            $c = CategoriaInventario::firstOrCreate(['nombre' => $cat['nombre']], $cat);
            $categoriasCreadas[$cat['nombre']] = $c->id;
        }

        $materiales = [
            // Anestesia
            ['nombre' => 'Anestesia Lidocaína 2% con epinefrina', 'unidad_medida' => 'carpules',  'stock_actual' => 50,  'stock_minimo' => 10,  'stock_maximo' => 100,  'precio_unitario' => 2500,  'categoria' => 'Anestesia'],
            ['nombre' => 'Anestesia Mepivacaína 3%',              'unidad_medida' => 'carpules',  'stock_actual' => 20,  'stock_minimo' => 5,   'stock_maximo' => 50,   'precio_unitario' => 3000,  'categoria' => 'Anestesia'],
            ['nombre' => 'Agujas dentales cortas',                 'unidad_medida' => 'unidades', 'stock_actual' => 100, 'stock_minimo' => 20,  'stock_maximo' => 200,  'precio_unitario' => 800,   'categoria' => 'Anestesia'],
            // Instrumental
            ['nombre' => 'Limas K #15 al #40',                    'unidad_medida' => 'juegos',   'stock_actual' => 10,  'stock_minimo' => 3,   'stock_maximo' => 20,   'precio_unitario' => 45000, 'categoria' => 'Instrumental'],
            ['nombre' => 'Limas Protaper Universal',               'unidad_medida' => 'juegos',   'stock_actual' => 5,   'stock_minimo' => 2,   'stock_maximo' => 10,   'precio_unitario' => 85000, 'categoria' => 'Instrumental'],
            ['nombre' => 'Fresas de diamante redondas',            'unidad_medida' => 'unidades', 'stock_actual' => 20,  'stock_minimo' => 5,   'stock_maximo' => 40,   'precio_unitario' => 12000, 'categoria' => 'Instrumental'],
            // Materiales de Obturación
            ['nombre' => 'Resina compuesta A2',                    'unidad_medida' => 'gramos',   'stock_actual' => 100, 'stock_minimo' => 20,  'stock_maximo' => 200,  'precio_unitario' => 1500,  'categoria' => 'Materiales de Obturación'],
            ['nombre' => 'Resina compuesta A3',                    'unidad_medida' => 'gramos',   'stock_actual' => 80,  'stock_minimo' => 20,  'stock_maximo' => 200,  'precio_unitario' => 1500,  'categoria' => 'Materiales de Obturación'],
            ['nombre' => 'Cemento de ionómero de vidrio',          'unidad_medida' => 'gramos',   'stock_actual' => 50,  'stock_minimo' => 10,  'stock_maximo' => 100,  'precio_unitario' => 2000,  'categoria' => 'Materiales de Obturación'],
            ['nombre' => 'Gutapercha calibre 25',                  'unidad_medida' => 'cajas',    'stock_actual' => 8,   'stock_minimo' => 2,   'stock_maximo' => 15,   'precio_unitario' => 25000, 'categoria' => 'Materiales de Obturación'],
            ['nombre' => 'Hidróxido de calcio',                    'unidad_medida' => 'gramos',   'stock_actual' => 100, 'stock_minimo' => 20,  'stock_maximo' => 200,  'precio_unitario' => 500,   'categoria' => 'Materiales de Obturación'],
            // Desinfección
            ['nombre' => 'Hipoclorito de sodio 5.25%',             'unidad_medida' => 'litros',   'stock_actual' => 5,   'stock_minimo' => 1,   'stock_maximo' => 10,   'precio_unitario' => 8000,  'categoria' => 'Desinfección y Esterilización'],
            ['nombre' => 'Alcohol antiséptico 70%',                'unidad_medida' => 'litros',   'stock_actual' => 3,   'stock_minimo' => 1,   'stock_maximo' => 8,    'precio_unitario' => 6000,  'categoria' => 'Desinfección y Esterilización'],
            // Consumibles
            ['nombre' => 'Guantes de látex talla M',               'unidad_medida' => 'cajas',    'stock_actual' => 5,   'stock_minimo' => 2,   'stock_maximo' => 15,   'precio_unitario' => 35000, 'categoria' => 'Consumibles'],
            ['nombre' => 'Tapabocas quirúrgico',                   'unidad_medida' => 'cajas',    'stock_actual' => 4,   'stock_minimo' => 1,   'stock_maximo' => 10,   'precio_unitario' => 18000, 'categoria' => 'Consumibles'],
            ['nombre' => 'Baberos desechables',                    'unidad_medida' => 'unidades', 'stock_actual' => 200, 'stock_minimo' => 50,  'stock_maximo' => 400,  'precio_unitario' => 300,   'categoria' => 'Consumibles'],
            ['nombre' => 'Vasos plásticos desechables',            'unidad_medida' => 'unidades', 'stock_actual' => 500, 'stock_minimo' => 100, 'stock_maximo' => 1000, 'precio_unitario' => 80,    'categoria' => 'Consumibles'],
            // Higiene
            ['nombre' => 'Pasta profiláctica',                     'unidad_medida' => 'gramos',   'stock_actual' => 500, 'stock_minimo' => 100, 'stock_maximo' => 1000, 'precio_unitario' => 200,   'categoria' => 'Higiene y Profilaxis'],
            ['nombre' => 'Sellante de fosas y fisuras',            'unidad_medida' => 'ml',       'stock_actual' => 30,  'stock_minimo' => 5,   'stock_maximo' => 60,   'precio_unitario' => 3000,  'categoria' => 'Higiene y Profilaxis'],
        ];

        foreach ($materiales as $mat) {
            $catNombre = $mat['categoria'];
            unset($mat['categoria']);
            $mat['categoria_id'] = $categoriasCreadas[$catNombre] ?? null;
            Material::firstOrCreate(['nombre' => $mat['nombre']], $mat);
        }

        $this->command->info('✓ Inventario inicial creado: ' . count($categorias) . ' categorías y ' . count($materiales) . ' materiales.');
    }
}
