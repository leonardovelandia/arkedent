<?php

return [

    /*
    |─────────────────────────────────────────────────────
    | PLAN ACTIVO
    | Cambia este valor para activar un plan completo
    | Opciones: 'basico', 'estandar', 'premium', 'especializado', 'personalizado'
    | O configura en .env: PLAN_ACTIVO=premium
    |─────────────────────────────────────────────────────
    */
    'plan_activo' => env('PLAN_ACTIVO', 'premium'),

    /*
    |─────────────────────────────────────────────────────
    | DEFINICIÓN DE PLANES
    | Precios en COP/mes — Mercado colombiano 2026
    | Referencia competencia:
    |   Nimbo-X:    $150.000 - $400.000/mes
    |   Dentsys:    $120.000 - $350.000/mes
    |   OdontoSoft: $80.000  - $250.000/mes
    |─────────────────────────────────────────────────────
    */
    'planes' => [

        // ─────────────────────────────────────────
        // PLAN BÁSICO — Consultorio unipersonal
        // 1 doctor + 1 asistente
        // Precio sugerido: $89.900/mes
        // ─────────────────────────────────────────
        'basico' => [
            'nombre' => 'Plan Básico',
            'descripcion' => 'Ideal para odontólogos independientes. Ge revisstión esencial del consultorio.',
            'precio' => 89900,
            'usuarios_max' => 2,
            'modulos' => [
                'pacientes',
                'historia_clinica',
                'citas',
                'evoluciones',
                'pagos',
                'consentimientos',
                'autorizacion_datos',
                'configuracion',
                'usuarios',
            ],
        ],

        // ─────────────────────────────────────────
        // PLAN ESTÁNDAR — Consultorio establecido
        // Hasta 3 usuarios
        // Precio sugerido: $159.900/mes
        // ─────────────────────────────────────────
        'estandar' => [
            'nombre' => 'Plan Estándar',
            'descripcion' => 'Para consultorios establecidos. Incluye gestión financiera y clínica completa.',
            'precio' => 159900,
            'usuarios_max' => 3,
            'modulos' => [
                // Core
                'pacientes',
                'historia_clinica',
                'citas',
                'evoluciones',
                'pagos',
                'consentimientos',
                'autorizacion_datos',
                'configuracion',
                'usuarios',
                // Gestión
                'presupuestos',
                'valoraciones',
                'imagenes',
                'reportes',
                'inventario',
                'laboratorio',
                'recordatorios',
            ],
        ],

        // ─────────────────────────────────────────
        // PLAN PREMIUM — Clínica completa
        // Usuarios ilimitados
        // Precio sugerido: $259.900/mes
        // ─────────────────────────────────────────
        'premium' => [
            'nombre' => 'Plan Premium',
            'descripcion' => 'Sistema completo para clínicas. Incluye contabilidad, proveedores y recordatorios automáticos.',
            'precio' => 259900,
            'usuarios_max' => 0, // 0 = ilimitado
            'modulos' => [
                // Core
                'pacientes',
                'historia_clinica',
                'citas',
                'evoluciones',
                'pagos',
                'consentimientos',
                'autorizacion_datos',
                'configuracion',
                'usuarios',
                // Gestión
                'presupuestos',
                'valoraciones',
                'imagenes',
                'reportes',
                'inventario',
                'laboratorio',
                'recordatorios',
                // Avanzado
                'proveedores',
                'egresos',
                'libro_contable',
                // Recetas
                'recetas',
            ],
        ],

        // ─────────────────────────────────────────
        // PLAN ESPECIALIZADO — Clínica especializada
        // Usuarios ilimitados + módulos de especialidades
        // Precio sugerido: $359.900/mes
        // Ideal para: ortodoncistas, periodoncistas,
        //             clínicas multiespecialidad
        // ─────────────────────────────────────────
        'especializado' => [
            'nombre' => 'Plan Especializado',
            'descripcion' => 'Para clínicas especializadas. Incluye módulos de ortodoncia, periodoncia, odontopediatría y endodoncia.',
            'precio' => 359900,
            'usuarios_max' => 0,
            'modulos' => [
                // Core
                'pacientes',
                'historia_clinica',
                'citas',
                'evoluciones',
                'pagos',
                'consentimientos',
                'autorizacion_datos',
                'configuracion',
                'usuarios',
                // Gestión
                'presupuestos',
                'valoraciones',
                'imagenes',
                'reportes',
                'inventario',
                'laboratorio',
                'recordatorios',
                // Avanzado
                'proveedores',
                'egresos',
                'libro_contable',
                'recetas',
                // Especialidades
                'ortodoncia',
                'periodoncia',
                'odontopediatria',
                'endodoncia',
                // Futuro
                'portal_paciente',
                'encuestas',
            ],
        ],

        // ─────────────────────────────────────────
        // PLAN EPS/IPS — Para instituciones de salud
        // Requiere habilitación REPS
        // Precio sugerido: desde $500.000/mes
        // ─────────────────────────────────────────
        'eps_ips' => [
            'nombre' => 'Plan EPS/IPS',
            'descripcion' => 'Para IPS habilitadas y consultorios con convenios EPS. Incluye RIPS, logs de auditoría y cumplimiento normativo completo.',
            'precio' => 500000,
            'usuarios_max' => 0,
            'modulos' => [
                // Core
                'pacientes',
                'historia_clinica',
                'citas',
                'evoluciones',
                'pagos',
                'consentimientos',
                'autorizacion_datos',
                'configuracion',
                'usuarios',
                // Gestión
                'presupuestos',
                'valoraciones',
                'imagenes',
                'reportes',
                'inventario',
                'laboratorio',
                'recordatorios',
                // Avanzado
                'proveedores',
                'egresos',
                'libro_contable',
                'recetas',
                // Especialidades
                'ortodoncia',
                'periodoncia',
                'odontopediatria',
                'endodoncia',
                // Portal y comunicación
                'portal_paciente',
                'encuestas',
                // Normativo EPS/IPS
                'logs_auditoria',
                'rips',
                'facturacion_electronica',
                'habilitacion_prestador',
            ],
        ],

        // ─────────────────────────────────────────
        // PLAN PERSONALIZADO
        // El desarrollador activa módulo por módulo
        // Precio: según módulos activos
        // ─────────────────────────────────────────
        'personalizado' => [
            'nombre' => 'Plan Personalizado',
            'descripcion' => 'Módulos seleccionados manualmente por el desarrollador.',
            'precio' => 0,
            'usuarios_max' => 0,
            'modulos' => [], // Se llena desde modulos_activos abajo
        ],

    ],

    /*
    |─────────────────────────────────────────────────────
    | MÓDULOS ACTIVOS MANUALMENTE
    | Solo aplica cuando plan_activo = 'personalizado'
    | Cambia true/false para activar o desactivar
    |─────────────────────────────────────────────────────
    */
    'modulos_activos' => [

        // ── CORE — Siempre recomendados ──────────
        'pacientes' => true,
        'historia_clinica' => true,
        'citas' => true,
        'evoluciones' => true,
        'pagos' => true,
        'consentimientos' => true,
        'autorizacion_datos' => true,  // Ley 1581/2012 Habeas Data
        'configuracion' => true,
        'usuarios' => true,

        // ── GESTIÓN ──────────────────────────────
        'presupuestos' => true,
        'valoraciones' => true,
        'imagenes' => true,
        'reportes' => true,
        'inventario' => true,
        'laboratorio' => true,
        'recordatorios' => true,

        // ── AVANZADO ─────────────────────────────
        'proveedores' => true,
        'egresos' => true,
        'libro_contable' => true,
        'recetas' => true,

        // ── ESPECIALIDADES ───────────────────────
        'ortodoncia' => false,
        'periodoncia' => false,
        'odontopediatria' => false,
        'endodoncia' => false,

        // ── PORTAL Y COMUNICACIÓN ────────────────
        'portal_paciente' => false,
        'encuestas' => false,

        // ── NORMATIVO EPS/IPS (futuro) ───────────
        'logs_auditoria' => false,  // Tarea A-04
        'rips' => false,  // Tarea EPS-01
        'facturacion_electronica' => false,  // Tarea EPS-01
        'habilitacion_prestador' => false,  // Tarea EPS-03
        'api_fhir' => false,  // Tarea I-01
        'rnds' => false,  // Tarea I-02
    ],

    /*
    |─────────────────────────────────────────────────────
    | CATÁLOGO COMPLETO DE MÓDULOS
    | nombre, descripcion, icono, plan_minimo, core
    |─────────────────────────────────────────────────────
    */
    'catalogo' => [

        // ── CORE ─────────────────────────────────
        'pacientes' => [
            'nombre' => 'Pacientes',
            'descripcion' => 'Ficha completa, foto, datos personales y de contacto',
            'icono' => 'bi-people',
            'plan_minimo' => 'basico',
            'core' => true,
        ],
        'historia_clinica' => [
            'nombre' => 'Historia Clínica',
            'descripcion' => 'Odontograma digital, antecedentes, alergias, firma digital',
            'icono' => 'bi-journal-medical',
            'plan_minimo' => 'basico',
            'core' => true,
        ],
        'citas' => [
            'nombre' => 'Citas y Agenda',
            'descripcion' => 'Calendario visual, confirmaciones, control de inasistencias',
            'icono' => 'bi-calendar3',
            'plan_minimo' => 'basico',
            'core' => true,
        ],
        'evoluciones' => [
            'nombre' => 'Evoluciones',
            'descripcion' => 'Nota clínica por sesión, materiales usados, firma digital',
            'icono' => 'bi-clipboard2-pulse',
            'plan_minimo' => 'basico',
            'core' => true,
        ],
        'pagos' => [
            'nombre' => 'Abonos y Pagos',
            'descripcion' => 'Pagos parciales, saldo pendiente, recibos en PDF',
            'icono' => 'bi-cash-coin',
            'plan_minimo' => 'basico',
            'core' => true,
        ],
        'consentimientos' => [
            'nombre' => 'Consentimientos',
            'descripcion' => 'Plantillas digitales, firma desde tablet o PC',
            'icono' => 'bi-pen',
            'plan_minimo' => 'basico',
            'core' => true,
        ],
        'autorizacion_datos' => [
            'nombre' => 'Autorización de Datos',
            'descripcion' => 'Cumplimiento Ley 1581/2012 Habeas Data Colombia',
            'icono' => 'bi-shield-check',
            'plan_minimo' => 'basico',
            'core' => true,
        ],
        'configuracion' => [
            'nombre' => 'Configuración',
            'descripcion' => 'Nombre, logo, temas, datos del consultorio, parámetros',
            'icono' => 'bi-gear',
            'plan_minimo' => 'basico',
            'core' => true,
        ],
        'usuarios' => [
            'nombre' => 'Usuarios y Roles',
            'descripcion' => 'Perfiles para doctora, asistente y administrador',
            'icono' => 'bi-person-gear',
            'plan_minimo' => 'basico',
            'core' => true,
        ],

        // ── GESTIÓN ──────────────────────────────
        'presupuestos' => [
            'nombre' => 'Presupuestos',
            'descripcion' => 'Cotizaciones por tratamiento, aprobación con firma digital',
            'icono' => 'bi-file-earmark-text',
            'plan_minimo' => 'estandar',
            'core' => false,
        ],
        'valoraciones' => [
            'nombre' => 'Valoraciones',
            'descripcion' => 'Diagnóstico inicial, hallazgos clínicos, plan de tratamiento',
            'icono' => 'bi-search-heart',
            'plan_minimo' => 'estandar',
            'core' => false,
        ],
        'imagenes' => [
            'nombre' => 'Imágenes Clínicas',
            'descripcion' => 'Radiografías, fotos intraorales, comparativos antes/después',
            'icono' => 'bi-images',
            'plan_minimo' => 'estandar',
            'core' => false,
        ],
        'reportes' => [
            'nombre' => 'Reportes y Estadísticas',
            'descripcion' => 'Ingresos, pacientes atendidos, productividad, gráficas',
            'icono' => 'bi-bar-chart-line',
            'plan_minimo' => 'estandar',
            'core' => false,
        ],
        'inventario' => [
            'nombre' => 'Inventario',
            'descripcion' => 'Materiales e insumos, alertas de stock, consumo por procedimiento',
            'icono' => 'bi-box-seam',
            'plan_minimo' => 'estandar',
            'core' => false,
        ],
        'laboratorio' => [
            'nombre' => 'Laboratorio',
            'descripcion' => 'Órdenes a laboratorio externo, seguimiento de entrega',
            'icono' => 'bi-eyedropper',
            'plan_minimo' => 'estandar',
            'core' => false,
        ],
        'recordatorios' => [
            'nombre' => 'Recordatorios',
            'descripcion' => 'Envío automático por WhatsApp y Email 24h antes de la cita',
            'icono' => 'bi-bell',
            'plan_minimo' => 'estandar',
            'core' => false,
        ],

        // ── AVANZADO ─────────────────────────────
        'proveedores' => [
            'nombre' => 'Proveedores',
            'descripcion' => 'Registro, historial de compras, comparación de precios',
            'icono' => 'bi-truck',
            'plan_minimo' => 'premium',
            'core' => false,
        ],
        'egresos' => [
            'nombre' => 'Egresos',
            'descripcion' => 'Gastos del consultorio, egresos recurrentes, comprobantes',
            'icono' => 'bi-cash-stack',
            'plan_minimo' => 'premium',
            'core' => false,
        ],
        'libro_contable' => [
            'nombre' => 'Libro Contable',
            'descripcion' => 'Libro de caja diario, estado de resultados, comparativo 12 meses',
            'icono' => 'bi-journal-bookmark',
            'plan_minimo' => 'premium',
            'core' => false,
        ],
        'recetas' => [
            'nombre' => 'Recetas Médicas',
            'descripcion' => 'Generador digital con firma, plantillas rápidas, PDF profesional',
            'icono' => 'bi-file-medical',
            'plan_minimo' => 'premium',
            'core' => false,
        ],

        // ── ESPECIALIDADES ───────────────────────
        'ortodoncia' => [
            'nombre' => 'Ortodoncia',
            'descripcion' => 'Ficha ortodóntica, control de arcos, seguimiento fotográfico, retención',
            'icono' => 'bi-braces',
            'plan_minimo' => 'especializado',
            'core' => false,
        ],
        'periodoncia' => [
            'nombre' => 'Periodoncia',
            'descripcion' => 'Periodontograma, sondaje, índice de placa, control de bolsas',
            'icono' => 'bi-heart-pulse',
            'plan_minimo' => 'especializado',
            'core' => false,
        ],
        'odontopediatria' => [
            'nombre' => 'Odontopediatría',
            'descripcion' => 'Odontograma infantil, índice CEO, ficha para niños',
            'icono' => 'bi-emoji-smile',
            'plan_minimo' => 'especializado',
            'core' => false,
        ],
        'endodoncia' => [
            'nombre' => 'Endodoncia',
            'descripcion' => 'Conductometría, odontometría, limas, pruebas de vitalidad',
            'icono' => 'bi-activity',
            'plan_minimo' => 'especializado',
            'core' => false,
        ],

        // ── PORTAL Y COMUNICACIÓN ────────────────
        'portal_paciente' => [
            'nombre' => 'Portal del Paciente',
            'descripcion' => 'El paciente puede ver citas, pagos y descargar documentos',
            'icono' => 'bi-person-circle',
            'plan_minimo' => 'especializado',
            'core' => false,
        ],
        'encuestas' => [
            'nombre' => 'Encuestas de Satisfacción',
            'descripcion' => 'Envío automático post-cita, análisis de resultados',
            'icono' => 'bi-star',
            'plan_minimo' => 'especializado',
            'core' => false,
        ],

        // ── NORMATIVO EPS/IPS (futuro) ───────────
        'logs_auditoria' => [
            'nombre' => 'Logs de Auditoría',
            'descripcion' => 'Registro de accesos y acciones — Ley 2015/2020',
            'icono' => 'bi-shield-lock',
            'plan_minimo' => 'eps_ips',
            'core' => false,
        ],
        'rips' => [
            'nombre' => 'RIPS',
            'descripcion' => 'Registro Individual de Prestación de Servicios — Res. 3374/2000',
            'icono' => 'bi-file-earmark-medical',
            'plan_minimo' => 'eps_ips',
            'core' => false,
        ],
        'facturacion_electronica' => [
            'nombre' => 'Facturación Electrónica',
            'descripcion' => 'Facturación DIAN para servicios de salud — Obligatorio EPS',
            'icono' => 'bi-receipt',
            'plan_minimo' => 'eps_ips',
            'core' => false,
        ],
        'habilitacion_prestador' => [
            'nombre' => 'Habilitación Prestador',
            'descripcion' => 'Gestión del código REPS, indicadores de calidad',
            'icono' => 'bi-building-check',
            'plan_minimo' => 'eps_ips',
            'core' => false,
        ],
        'api_fhir' => [
            'nombre' => 'API FHIR R4',
            'descripcion' => 'Interoperabilidad HL7 FHIR — Res. 866/2021',
            'icono' => 'bi-diagram-3',
            'plan_minimo' => 'eps_ips',
            'core' => false,
        ],
        'rnds' => [
            'nombre' => 'RNDS',
            'descripcion' => 'Red Nacional de Datos en Salud — Ministerio de Salud',
            'icono' => 'bi-cloud-arrow-up',
            'plan_minimo' => 'eps_ips',
            'core' => false,
        ],
    ],

    /*
    |─────────────────────────────────────────────────────
    | TABLA DE PRECIOS SUGERIDOS DE VENTA
    | Solo referencia — no afecta el sistema
    |─────────────────────────────────────────────────────
    */
    'precios_venta' => [
        'basico' => [
            'instalado' => ['min' => 1500000, 'max' => 2500000],
            'suscripcion_mes' => ['min' => 89900, 'max' => 119900],
            'soporte_mes' => ['min' => 60000, 'max' => 80000],
        ],
        'estandar' => [
            'instalado' => ['min' => 2500000, 'max' => 3500000],
            'suscripcion_mes' => ['min' => 159900, 'max' => 199900],
            'soporte_mes' => ['min' => 80000, 'max' => 100000],
        ],
        'premium' => [
            'instalado' => ['min' => 3500000, 'max' => 5000000],
            'suscripcion_mes' => ['min' => 259900, 'max' => 299900],
            'soporte_mes' => ['min' => 100000, 'max' => 150000],
        ],
        'especializado' => [
            'instalado' => ['min' => 5000000, 'max' => 8000000],
            'suscripcion_mes' => ['min' => 359900, 'max' => 429900],
            'soporte_mes' => ['min' => 120000, 'max' => 180000],
        ],
        'eps_ips' => [
            'instalado' => ['min' => 10000000, 'max' => 30000000],
            'suscripcion_mes' => ['min' => 500000, 'max' => 1500000],
            'soporte_mes' => ['min' => 200000, 'max' => 500000],
        ],
    ],

];