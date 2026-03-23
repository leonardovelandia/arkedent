<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProveedoresSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            [
                'nombre'              => 'Dental Import Colombia',
                'nit'                 => '900123456-1',
                'contacto'            => 'Juan Pérez',
                'telefono'            => '3001234567',
                'whatsapp'            => '3001234567',
                'email'               => 'ventas@dentalimport.com',
                'ciudad'              => 'Medellín',
                'categorias'          => ['anestesia', 'instrumental', 'materiales_obturacion'],
                'tiempo_entrega_dias' => 2,
                'condiciones_pago'    => 'Contado o 30 días',
                'calificacion'        => 4.5,
                'notas'               => 'Proveedor principal de materiales de endodoncia',
                'activo'              => true,
            ],
            [
                'nombre'              => 'Distribuidora Odontológica del Valle',
                'nit'                 => '800987654-2',
                'contacto'            => 'María López',
                'telefono'            => '3209876543',
                'whatsapp'            => '3209876543',
                'email'               => 'pedidos@odontovalle.com',
                'ciudad'              => 'Medellín',
                'categorias'          => ['consumibles', 'higiene', 'desinfeccion'],
                'tiempo_entrega_dias' => 1,
                'condiciones_pago'    => 'Contado',
                'calificacion'        => 4.0,
                'notas'               => 'Buenos precios en consumibles y desinfectantes',
                'activo'              => true,
            ],
        ];

        foreach ($proveedores as $data) {
            Proveedor::firstOrCreate(
                ['nombre' => $data['nombre']],
                $data
            );
        }

        $this->command->info('✓ Proveedores iniciales creados.');
    }
}
