<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Auth } from '@/types';

const { auth } = usePage<{ auth: Auth }>().props;

interface Stats {
    users: number;
    properties: number;
    entries_today: number;
    inside: number;
    authorizations: number;
    by_type: { propietario: number; autorizado: number; visitante: number };
}
interface RecentEntry {
    id: number;
    full_name: string;
    apartment: string;
    type: string;
    entry_at: string;
    is_inside: boolean;
}
defineProps<{ stats: Stats; recent: RecentEntry[] }>();

const typeVariant: Record<string, 'default' | 'secondary' | 'outline'> = {
    propietario: 'default',
    autorizado: 'secondary',
    visitante: 'outline',
};
</script>

<template>
    <AppLayout>
        <Head title="Panel Administrador" />
        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <div>
                <h1 class="text-2xl font-bold">Panel de Administración</h1>
                <p class="text-muted-foreground">
                    Bienvenido, {{ auth.user.first_name }}
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="text-sm text-muted-foreground">Dentro ahora</p>
                    <p class="text-4xl font-bold text-primary">
                        {{ stats.inside }}
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ stats.entries_today }} ingresos hoy
                    </p>
                </div>
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="text-sm text-muted-foreground">Propietarios</p>
                    <p class="text-4xl font-bold">
                        {{ stats.by_type.propietario }}
                    </p>
                </div>
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="text-sm text-muted-foreground">Autorizados</p>
                    <p class="text-4xl font-bold">
                        {{ stats.by_type.autorizado }}
                    </p>
                </div>
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="text-sm text-muted-foreground">Visitantes</p>
                    <p class="text-4xl font-bold">
                        {{ stats.by_type.visitante }}
                    </p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="text-sm text-muted-foreground">
                        Usuarios registrados
                    </p>
                    <p class="text-3xl font-bold">{{ stats.users }}</p>
                </div>
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="text-sm text-muted-foreground">Inmuebles</p>
                    <p class="text-3xl font-bold">{{ stats.properties }}</p>
                </div>
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="text-sm text-muted-foreground">
                        Autorizaciones activas
                    </p>
                    <p class="text-3xl font-bold">{{ stats.authorizations }}</p>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border bg-card shadow-sm">
                <div class="border-b px-5 py-4">
                    <h2 class="font-semibold">Últimos ingresos del día</h2>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-muted/50">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium">
                                Nombre
                            </th>
                            <th class="px-5 py-3 text-left font-medium">
                                Apto
                            </th>
                            <th class="px-5 py-3 text-left font-medium">
                                Tipo
                            </th>
                            <th class="px-5 py-3 text-left font-medium">
                                Hora
                            </th>
                            <th class="px-5 py-3 text-left font-medium">
                                Estado
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="e in recent"
                            :key="e.id"
                            class="border-t hover:bg-muted/30"
                        >
                            <td class="px-5 py-3 font-medium">
                                {{ e.full_name }}
                            </td>
                            <td class="px-5 py-3">{{ e.apartment }}</td>
                            <td class="px-5 py-3">
                                <Badge :variant="typeVariant[e.type]">{{
                                    e.type
                                }}</Badge>
                            </td>
                            <td class="px-5 py-3">{{ e.entry_at }}</td>
                            <td class="px-5 py-3">
                                <span
                                    v-if="e.is_inside"
                                    class="font-medium text-green-600"
                                    >● Dentro</span
                                >
                                <span v-else class="text-muted-foreground"
                                    >Salió</span
                                >
                            </td>
                        </tr>
                        <tr v-if="recent.length === 0">
                            <td
                                colspan="5"
                                class="px-5 py-8 text-center text-muted-foreground"
                            >
                                No hay ingresos hoy.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
