<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
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
    propietario: 'bg-blue-100 text-blue-800',
    autorizado:  'bg-green-100 text-green-800',
    visitante:   'bg-amber-100 text-amber-800',
};
</script>

<template>
    <AppLayout>
        <Head title="Dashboard Vigilante" />
        <div class="flex flex-col gap-6 p-6">
            <div>
                <h1 class="text-2xl font-bold">Panel de Vigilancia</h1>
                <p class="text-muted-foreground">Bienvenido, {{ auth.user.first_name }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="text-sm text-muted-foreground">Dentro ahora</p>
                    <p class="text-4xl font-bold text-primary">{{ stats.inside }}</p>
                </div>
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="text-sm text-muted-foreground">Ingresos hoy</p>
                    <p class="text-4xl font-bold">{{ stats.entries_today }}</p>
                </div>
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="text-sm text-muted-foreground">Salidas hoy</p>
                    <p class="text-4xl font-bold">{{ stats.exits_today }}</p>
                </div>
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="text-sm text-muted-foreground">Autorizaciones activas</p>
                    <p class="text-4xl font-bold">{{ stats.authorizations }}</p>
                </div>
            </div>

            <div class="rounded-xl border bg-card shadow-sm">
                <div class="border-b px-5 py-4">
                    <h2 class="font-semibold">Personas dentro del edificio</h2>
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
                                <span :class="['rounded-full px-2 py-0.5 text-xs font-medium', typeBadge[e.type] ?? 'bg-muted text-muted-foreground']">
                                    {{ e.type }}
                                </span>
                            </td>
                            <td class="px-5 py-3">{{ e.entry_at }}</td>
                        </tr>
                        <tr v-if="inside.length === 0">
                            <td colspan="4" class="px-5 py-8 text-center text-muted-foreground">
                                No hay personas dentro del edificio en este momento.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
