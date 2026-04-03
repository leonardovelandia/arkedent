# Arkedent Dental ERP

Sistema ERP odontológico completo desarrollado en Laravel para la gestión integral de consultorios dentales.

## Módulos del sistema

### Clínico
- Gestión de pacientes con galería de imágenes
- Historia clínica con firma digital
- Evoluciones y correcciones
- Valoraciones y consultas
- Imágenes clínicas
- Recetas médicas
- Ortodoncia (fichas, controles, retenciones)
- Periodoncia (fichas y seguimiento)

### Administrativo
- Dashboard con métricas en tiempo real
- Agenda y citas
- Usuarios y roles (Administrador, Doctor, Asistente)
- Consentimientos informados con plantillas configurables
- Firmas digitales y verificación de documentos
- Auditoría y trazabilidad de acciones

### Financiero
- Presupuestos y abonos
- Pagos y recibos
- Gastos y libro contable
- Control de ingresos y cuentas por cobrar

### Laboratorio
- Gestión de órdenes de laboratorio
- Alertas de laboratorio vencido

### Inventario y Compras
- Inventario por categorías y movimientos de stock
- Proveedores y compras
- Comparación de cotizaciones

### Reportes y Sistema
- Reportes de ingresos, pacientes y citas
- Configuración del sistema (temas, notificaciones)
- Integración con WhatsApp vía Twilio
- Backup automatizado y exportación de datos
- Importación de datos (Excel)
- Panel de desarrollo protegido por contraseña

## Tecnologías

**Backend:**
- Laravel 12 · PHP 8.2+
- Spatie Permission (RBAC)
- Laravel Breeze (autenticación)
- DomPDF (generación de PDFs)
- PHPOffice/PhpSpreadsheet (exportación Excel)
- Bacon QR Code (códigos QR)
- Spatie Laravel Backup

**Frontend:**
- Tailwind CSS 3.1
- Alpine.js 3.4
- Vite
- Axios

**Base de datos:** MySQL

## Seguridad

- Autenticación con Laravel Breeze
- Control de acceso por roles con Spatie Permission
- Firmas digitales y tokens de verificación de documentos
- Auditoría con registro de IP, dispositivo y timestamps
- Variables de entorno protegidas (.env)

## Estado del proyecto

En desarrollo activo (~85% completado)  
Listo para escalar a SaaS multi-tenant

## Autor

Leonardo Velandia

---

Proyecto desarrollado como base para un sistema SaaS odontológico.
