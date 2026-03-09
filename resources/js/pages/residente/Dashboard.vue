<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { Activity, Home } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Auth } from '@/types';

const { auth } = usePage<{ auth: Auth }>().props;
</script>

<template>
    <AppLayout>
        <Head title="Panel Residente" />
        <div class="flex flex-col gap-5 p-4 sm:p-6">

            <!-- Header futurista -->
            <div class="page-header">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">ACCESO · GUARD</p>
                <h1 class="mt-1 text-2xl font-bold">Mi Panel</h1>
                <p class="mt-0.5 text-sm text-muted-foreground">
                    Bienvenido, <span class="font-semibold text-foreground">{{ auth.user.first_name }} {{ auth.user.last_name }}</span>
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="stat-card">
                    <div class="absolute right-4 top-4 rounded-xl bg-primary/10 p-2">
                        <Activity class="h-4 w-4 text-primary" />
                    </div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-muted-foreground">Últimos ingresos</p>
                    <p class="mt-1.5 text-3xl font-bold tracking-tight text-muted-foreground">—</p>
                    <p class="mt-1 text-xs text-muted-foreground">Sin registros recientes</p>
                </div>
                <div class="stat-card">
                    <div class="absolute right-4 top-4 rounded-xl bg-teal-500/10 p-2">
                        <Home class="h-4 w-4 text-teal-500" />
                    </div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-muted-foreground">Estado actual</p>
                    <p class="mt-1.5 text-2xl font-bold tracking-tight text-teal-600">Fuera</p>
                    <p class="mt-1 text-xs text-muted-foreground">No registrado en el edificio</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
