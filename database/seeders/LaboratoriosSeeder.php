<?php

namespace Database\Seeders;

use App\Models\Laboratorio;
use Illuminate\Database\Seeder;

class LaboratoriosSeeder extends Seeder
{
    public function run(): void
    {
        $laboratorios = [
            [
                'nombre'              => 'Laboratorio Dental Sonrisas',
                'contacto'            => 'Carlos Mendoza',
                'telefono'            => '3001234567',
                'whatsapp'            => '3001234567',
                'email'               => 'info@sonrisaslab.com',
                'ciudad'              => 'Medellín',
                'especialidades'      => ['coronas_puentes', 'protesis_removible', 'estetica'],
                'tiempo_entrega_dias' => 7,
                'notas'               => 'Laboratorio de confianza, buena calidad en zirconia',
                'activo'              => true,
            ],
            [
                'nombre'              => 'ProDental Lab',
                'contacto'            => 'Ana García',
                'telefono'            => '3109876543',
                'whatsapp'            => '3109876543',
                'email'               => 'pedidos@prodental.com',
                'ciudad'              => 'Medellín',
                'especialidades'      => ['coronas_puentes', 'implantologia', 'ortodoncia'],
                'tiempo_entrega_dias' => 10,
                'notas'               => 'Especialistas en implantología y ortodoncia',
                'activo'              => true,
            ],
        ];

        foreach ($laboratorios as $data) {
            Laboratorio::firstOrCreate(
                ['nombre' => $data['nombre']],
                $data
            );
        }
    }
}
