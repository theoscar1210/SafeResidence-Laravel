<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRightLeft, ClipboardList, LogIn, LogOut, Shield, Users } from 'lucide-vue-next';
import type { Auth } from '@/types';

const { auth } = usePage<{ auth: Auth }>().props;

interface Stats {
    entries_today: number;
    inside: number;
    exits_today: number;
    authorizations: number;
}
interface InsideEntry {
    id: number;
    full_name: string;
    apartment: string;
    type: string;
    entry_at: string;
}
defineProps<{ stats: Stats; inside: InsideEntry[] }>();

const typeBadge: Record<string, string> = {
    propietario: 'bg-blue-100 text-blue-700',
    autorizado:  'bg-green-100 text-green-700',
    visitante:   'bg-amber-100 text-amber-700',
    contratista: 'bg-orange-100 text-orange-700',
};

const typeLabel: Record<string, string> = {
    propietario: 'Propietario',
    autorizado:  'Autorizado',
    visitante:   'Visitante',
    contratista: 'Contratista',
};
</script>

<template>
    <AppLayout>
        <Head title="Dashboard Vigilante" />
        <div class="flex flex-col gap-6 p-6">

            <!-- Header -->
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Panel de Vigilancia</h1>
                    <p class="text-sm text-muted-foreground">Bienvenido, {{ auth.user.first_name }} — {{ new Date().toLocaleDateString('es-CO', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
                </div>
            </div>

            <!-- Stat cards con iconos -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Dentro ahora — destacado -->
                <div class="rounded-xl border-2 border-primary/20 bg-primary/5 p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Dentro ahora</p>
                            <p class="mt-1 text-4xl font-bold text-primary">{{ stats.inside }}</p>
                        </div>
                        <div class="rounded-lg bg-primary/10 p-2">
                            <Users class="h-5 w-5 text-primary" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Ingresos hoy</p>
                            <p class="mt-1 text-4xl font-bold text-green-600">{{ stats.entries_today }}</p>
                        </div>
                        <div class="rounded-lg bg-green-50 p-2">
                            <LogIn class="h-5 w-5 text-green-600" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Salidas hoy</p>
                            <p class="mt-1 text-4xl font-bold text-slate-600">{{ stats.exits_today }}</p>
                        </div>
                        <div class="rounded-lg bg-slate-100 p-2">
                            <LogOut class="h-5 w-5 text-slate-600" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Autorizaciones</p>
                            <p class="mt-1 text-4xl font-bold text-blue-600">{{ stats.authorizations }}</p>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-2">
                            <Shield class="h-5 w-5 text-blue-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas (máx. 1 clic para llegar) -->
            <div>
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Acciones rápidas</h2>
                <div class="grid gap-3 sm:grid-cols-3">
                    <Link
                        href="/vigilante/entries/create"
                        class="group flex items-center gap-4 rounded-xl border-2 border-green-200 bg-green-50 p-5 transition-colors hover:border-green-400 hover:bg-green-100"
                    >
                        <div class="rounded-lg bg-green-500 p-3">
                            <LogIn class="h-6 w-6 text-white" />
                        </div>
                        <div>
                            <p class="font-semibold text-green-900">Registrar Ingreso</p>
                            <p class="text-xs text-green-700">Visitante, propietario o contratista</p>
                        </div>
                    </Link>

                    <Link
                        href="/vigilante/exits"
                        class="group flex items-center gap-4 rounded-xl border-2 border-slate-200 bg-slate-50 p-5 transition-colors hover:border-slate-400 hover:bg-slate-100"
                    >
                        <div class="rounded-lg bg-slate-600 p-3">
                            <LogOut class="h-6 w-6 text-white" />
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Registrar Salida</p>
                            <p class="text-xs text-slate-600">{{ stats.inside }} persona(s) dentro</p>
                        </div>
                    </Link>

                    <Link
                        href="/vigilante/authorizations"
                        class="group flex items-center gap-4 rounded-xl border-2 border-blue-200 bg-blue-50 p-5 transition-colors hover:border-blue-400 hover:bg-blue-100"
                    >
                        <div class="rounded-lg bg-blue-600 p-3">
                            <Shield class="h-6 w-6 text-white" />
                        </div>
                        <div>
                            <p class="font-semibold text-blue-900">Autorizaciones</p>
                            <p class="text-xs text-blue-700">{{ stats.authorizations }} activa(s)</p>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Tabla: personas actualmente dentro -->
            <div class="rounded-xl border bg-card shadow-sm">
                <div class="flex items-center justify-between border-b px-5 py-4">
                    <div class="flex items-center gap-2">
                        <ArrowRightLeft class="h-4 w-4 text-muted-foreground" />
                        <h2 class="font-semibold">Personas dentro ahora</h2>
                        <span class="rounded-full bg-primary/10 px-2 py-0.5 text-xs font-semibold text-primary">
                            {{ inside.length }}
                        </span>
                    </div>
                    <Link href="/vigilante/entries" class="text-sm text-primary hover:underline">
                        <span class="flex items-center gap-1">
                            <ClipboardList class="h-3.5 w-3.5" />
                            Ver historial
                        </span>
                    </Link>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-muted/50">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium">Nombre</th>
                            <th class="px-5 py-3 text-left font-medium">Apto</th>
                            <th class="px-5 py-3 text-left font-medium">Tipo</th>
                            <th class="px-5 py-3 text-left font-medium">Ingresó</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="e in inside" :key="e.id" class="border-t hover:bg-muted/30">
                            <td class="px-5 py-3 font-medium">{{ e.full_name }}</td>
                            <td class="px-5 py-3">{{ e.apartment }}</td>
                            <td class="px-5 py-3">
                                <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold', typeBadge[e.type] ?? 'bg-muted text-muted-foreground']">
                                    {{ typeLabel[e.type] ?? e.type }}
                                </span>
                            </td>
                            <td class="px-5 py-3 font-mono text-xs">{{ e.entry_at }}</td>
                        </tr>
                        <tr v-if="inside.length === 0">
                            <td colspan="4" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-muted-foreground">
                                    <Users class="h-8 w-8 opacity-30" />
                                    <span>No hay personas dentro del edificio en este momento.</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </AppLayout>
</template>
