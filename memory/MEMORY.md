# SafeResidence-Laravel — Memoria del Proyecto

## Stack
- Laravel 11 + Inertia.js v2 + Vue 3 + Tailwind CSS v4
- shadcn-like UI: reka-ui + componentes en `resources/js/components/ui/`
- Lucide Vue Next para íconos
- Spatie Laravel Permission para roles

## Roles
Administrador | Vigilante | Propietario | Residente

## Rutas por rol
- Admin: /admin/dashboard, /admin/users (resource), /admin/entries
- Vigilante: /vigilante/dashboard, entries (index/create/store/lookup), exits, authorizations, reports
- Propietario: /propietario/dashboard, authorizations (index/create/store/destroy), /propietario/history
- Residente: /residente/dashboard

## Modelos clave
- Entry: first_name, last_name, cedula, apartment, type, vehicle, plate (nullable), registered_by, observations, entry_at
- ExitRecord (tabla: exits): entry_id, exited_at, exited_by, observations
- Authorization: user_id, first_name, last_name, cedula, type, status, start_date, end_date, observations
- Apartment: number, user_id
- User: first_name, last_name, cedula, phone, username, email → roles via Spatie

## Convenciones de vistas
- Páginas en: resources/js/pages/{rol}/{Modulo}/Index.vue | Create.vue | Edit.vue
- Layout: AppLayout → AppSidebarLayout (sidebar + ToastContainer)
- Flash messages: session('success'|'error'|'warning'|'info') → aparecen como toasts automáticamente
- El middleware HandleInertiaRequests comparte: auth, flash, sidebarOpen

## Componentes creados
- ToastContainer.vue: sistema de notificaciones global (bottom-right, animado)
- useToast.ts: composable reactivo compartido

## Últimos cambios (2026-03-06)
- Implementado plan UX/UI completo (7 fases)
- Migración: campo plate nullable en entries
- Dashboard Vigilante rediseñado (stat cards con iconos + acciones rápidas)
- Formulario ingreso: tipo como botones visuales, placa condicional, alertas auth
- Admin/Entries/Index: historial global con filtros + paginación
- Propietario/History/Index: historial de visitas del apartamento
- Sidebar: History añadido a Propietario
