<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuracion;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Seeder: ConfiguracionInicialSeeder
 * 
 * Crea la configuración inicial del consultorio,
 * los roles del sistema y el usuario administrador.
 * 
 * Ejecutar con: php artisan db:seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesYPermisosSeeder::class,
            ConfiguracionSeeder::class,
            UsuarioAdminSeeder::class,
            PlantillasConsentimientoSeeder::class,
            InventarioSeeder::class,
            LaboratoriosSeeder::class,
            ProveedoresSeeder::class,
        ]);
    }
}


// ─────────────────────────────────────────────────────────────────
// Seeder de roles y permisos (Spatie Permission)
// ─────────────────────────────────────────────────────────────────
class RolesYPermisosSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permisos del sistema ───────────────────────────────

        $permisos = [
            // Pacientes
            'ver pacientes', 'crear pacientes', 'editar pacientes', 'eliminar pacientes',
            // Citas
            'ver citas', 'crear citas', 'editar citas', 'cancelar citas',
            // Historia clínica
            'ver historias', 'crear historias', 'editar historias',
            // Evoluciones
            'ver evoluciones', 'crear evoluciones', 'editar evoluciones',
            // Pagos
            'ver pagos', 'crear pagos', 'editar pagos',
            // Presupuestos
            'ver presupuestos', 'crear presupuestos', 'aprobar presupuestos',
            // Inventario
            'ver inventario', 'editar inventario',
            // Reportes
            'ver reportes',
            // Usuarios
            'ver usuarios', 'crear usuarios', 'editar usuarios', 'eliminar usuarios',
            // Configuración
            'ver configuracion', 'editar configuracion',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        // ── Rol: Doctora (acceso total) ────────────────────────
        $rolDoctora = Role::firstOrCreate(['name' => 'doctora', 'guard_name' => 'web']);
        $rolDoctora->givePermissionTo(Permission::all());

        // ── Rol: Asistente ─────────────────────────────────────
        $rolAsistente = Role::firstOrCreate(['name' => 'asistente', 'guard_name' => 'web']);
        $rolAsistente->givePermissionTo([
            'ver pacientes', 'crear pacientes', 'editar pacientes',
            'ver citas', 'crear citas', 'editar citas', 'cancelar citas',
            'ver historias', 'crear historias',
            'ver evoluciones', 'crear evoluciones',
            'ver pagos', 'crear pagos',
            'ver presupuestos',
        ]);

        // ── Rol: Administrador ─────────────────────────────────
        $rolAdmin = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        $rolAdmin->givePermissionTo([
            'ver pacientes', 'ver citas', 'ver historias',
            'ver pagos', 'ver reportes',
            'ver inventario', 'editar inventario',
            'ver usuarios', 'crear usuarios', 'editar usuarios', 'eliminar usuarios',
            'ver configuracion', 'editar configuracion',
        ]);

        $this->command->info('✓ Roles y permisos creados correctamente.');
    }
}


// ─────────────────────────────────────────────────────────────────
// Seeder de configuración del consultorio
// ─────────────────────────────────────────────────────────────────
class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        Configuracion::firstOrCreate(
            ['activo' => true],
            [
                'nombre_consultorio'               => 'Tatiana Velandia Odontología',
                'slogan'                           => 'Cuidando tu sonrisa con pasión',
                'nit'                              => '',
                'registro_medico'                  => '',
                'telefono'                         => '',
                'telefono_whatsapp'                => '',
                'email'                            => '',
                'direccion'                        => '',
                'ciudad'                           => 'Colombia',
                'pais'                             => 'Colombia',
                'duracion_cita_minutos'            => 30,
                'hora_apertura'                    => '08:00:00',
                'hora_cierre'                      => '18:00:00',
                'dias_laborales'                   => [1, 2, 3, 4, 5], // Lunes a Viernes
                'moneda'                           => 'COP',
                'simbolo_moneda'                   => '$',
                'recordatorios_activos'            => false,
                'horas_anticipacion_recordatorio'  => 24,
            ]
        );

        $this->command->info('✓ Configuración inicial del consultorio creada.');
    }
}


// ─────────────────────────────────────────────────────────────────
// Seeder del usuario administrador inicial
// ─────────────────────────────────────────────────────────────────
class UsuarioAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario: Doctora Tatiana
        $doctora = User::firstOrCreate(
            ['email' => 'tatiana@consultorio.com'],
            [
                'name'              => 'Tatiana Velandia',
                'password'          => Hash::make('Tatiana2024!'),
                'email_verified_at' => now(),
            ]
        );
        $doctora->assignRole('doctora');

        // Usuario: Administrador del sistema
        $admin = User::firstOrCreate(
            ['email' => 'admin@consultorio.com'],
            [
                'name'              => 'Administrador Sistema',
                'password'          => Hash::make('Admin2024!'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('administrador');

        $this->command->info('✓ Usuarios iniciales creados:');
        $this->command->info('  Doctora  → tatiana@consultorio.com  / Tatiana2024!');
        $this->command->info('  Admin    → admin@consultorio.com     / Admin2024!');
        $this->command->warn('  ⚠ Cambia las contraseñas en producción.');
    }
}