<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Auth } from '@/types';

const { auth } = usePage<{ auth: Auth }>().props;

interface Stats {
    authorizations_active: number;
    authorizations_total: number;
    inside_now: number;
}
interface Authorization {
    id: number;
    full_name: string;
    cedula: string;
    type: string;
    status: string;
    start_date: string;
    end_date: string;
}
defineProps<{ stats: Stats; authorizations: Authorization[] }>();

const statusClass: Record<string, string> = {
    activa: 'bg-green-100 text-green-800',
    usada: 'bg-blue-100 text-blue-800',
    expirada: 'bg-red-100 text-red-800',
};
const typeClass: Record<string, string> = {
    autorizado: 'bg-green-100 text-green-800',
    visitante: 'bg-amber-100 text-amber-800',
};
</script>

<template>
    <AppLayout>
        <Head title="Dashboard Propietario" />
        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <div>
                <h1 class="text-2xl font-bold">Mi Panel</h1>
                <p class="text-muted-foreground">
                    Bienvenido, {{ auth.user.first_name }}
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="text-sm text-muted-foreground">
                        Autorizaciones activas
                    </p>
                    <p class="text-4xl font-bold text-primary">
                        {{ stats.authorizations_active }}
                    </p>
                </div>
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="text-sm text-muted-foreground">
                        Total autorizaciones
                    </p>
                    <p class="text-4xl font-bold">
                        {{ stats.authorizations_total }}
                    </p>
                </div>
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="text-sm text-muted-foreground">
                        Personas en edificio
                    </p>
                    <p class="text-4xl font-bold">{{ stats.inside_now }}</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        relacionadas a tu apartamento
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border bg-card shadow-sm">
                <div
                    class="flex items-center justify-between border-b px-5 py-4"
                >
                    <h2 class="font-semibold">Mis autorizaciones</h2>
                    <Link
                        href="/propietario/authorizations/create"
                        class="rounded-md bg-primary px-3 py-1.5 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                    >
                        + Nueva
                    </Link>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-muted/50">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium">
                                Nombre
                            </th>
                            <th class="px-5 py-3 text-left font-medium">
                                Cédula
                            </th>
                            <th class="px-5 py-3 text-left font-medium">
                                Tipo
                            </th>
                            <th class="px-5 py-3 text-left font-medium">
                                Vigencia
                            </th>
                            <th class="px-5 py-3 text-left font-medium">
                                Estado
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="a in authorizations"
                            :key="a.id"
                            class="border-t hover:bg-muted/30"
                        >
                            <td class="px-5 py-3 font-medium">
                                {{ a.full_name }}
                            </td>
                            <td class="px-5 py-3">{{ a.cedula }}</td>
                            <td class="px-5 py-3">
                                <span
                                    :class="[
                                        'rounded-full px-2 py-0.5 text-xs font-medium',
                                        typeClass[a.type] ??
                                            'bg-muted text-muted-foreground',
                                    ]"
                                >
                                    {{ a.type }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-xs">
                                {{ a.start_date }} → {{ a.end_date }}
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    :class="[
                                        'rounded-full px-2 py-0.5 text-xs font-medium',
                                        statusClass[a.status] ??
                                            'bg-muted text-muted-foreground',
                                    ]"
                                >
                                    {{ a.status }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="authorizations.length === 0">
                            <td
                                colspan="5"
                                class="px-5 py-8 text-center text-muted-foreground"
                            >
                                No tienes autorizaciones.
                                <Link
                                    href="/propietario/authorizations/create"
                                    class="text-primary underline"
                                    >Crea una ahora</Link
                                >.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
