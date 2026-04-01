<?php

namespace Database\Seeders;

use App\Models\CategoriaEgreso;
use Illuminate\Database\Seeder;

class CategoriasEgresoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Arriendo',              'color' => '#DC3545', 'icono' => 'bi-building',           'es_fijo' => true,  'descripcion' => 'Arriendo del local o consultorio'],
            ['nombre' => 'Servicios Públicos',    'color' => '#FD7E14', 'icono' => 'bi-lightning-charge',   'es_fijo' => true,  'descripcion' => 'Agua, luz, gas, aseo'],
            ['nombre' => 'Internet y Teléfono',   'color' => '#0D6EFD', 'icono' => 'bi-wifi',               'es_fijo' => true,  'descripcion' => 'Internet, telefonía fija y móvil'],
            ['nombre' => 'Salarios y Honorarios', 'color' => '#6B21A8', 'icono' => 'bi-people',             'es_fijo' => true,  'descripcion' => 'Salarios empleados y honorarios profesionales'],
            ['nombre' => 'Mantenimiento',         'color' => '#6C757D', 'icono' => 'bi-tools',              'es_fijo' => false, 'descripcion' => 'Mantenimiento de equipos e instalaciones'],
            ['nombre' => 'Publicidad',            'color' => '#E83E8C', 'icono' => 'bi-megaphone',          'es_fijo' => false, 'descripcion' => 'Publicidad, redes sociales, marketing'],
            ['nombre' => 'Equipos y Tecnología',  'color' => '#17A2B8', 'icono' => 'bi-laptop',             'es_fijo' => false, 'descripcion' => 'Compra de equipos, computadores, software'],
            ['nombre' => 'Impuestos',             'color' => '#856404', 'icono' => 'bi-receipt',            'es_fijo' => true,  'descripcion' => 'ICA, predial, renta y otras obligaciones'],
            ['nombre' => 'Gastos Bancarios',      'color' => '#495057', 'icono' => 'bi-bank',               'es_fijo' => true,  'descripcion' => 'Cuotas de manejo, comisiones bancarias'],
            ['nombre' => 'Capacitaciones',        'color' => '#28A745', 'icono' => 'bi-book',               'es_fijo' => false, 'descripcion' => 'Cursos, congresos, capacitaciones'],
            ['nombre' => 'Transporte',            'color' => '#20C997', 'icono' => 'bi-car-front',          'es_fijo' => false, 'descripcion' => 'Gasolina, parqueadero, transporte'],
            ['nombre' => 'Otros Gastos',          'color' => '#ADB5BD', 'icono' => 'bi-three-dots',         'es_fijo' => false, 'descripcion' => 'Gastos varios no categorizados'],
        ];

        foreach ($categorias as $cat) {
            CategoriaEgreso::firstOrCreate(['nombre' => $cat['nombre']], $cat);
        }

        $this->command->info('✓ Categorías de egresos creadas correctamente.');
    }
}
