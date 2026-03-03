<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import type { Auth } from '@/types';

const { auth } = usePage<{ auth: Auth }>().props;
</script>

<template>
    <AppLayout>
        <Head title="Panel Propietario" />

        <div class="flex flex-col gap-6 p-6">
            <div>
                <h1 class="text-2xl font-bold">Mi Panel</h1>
                <p class="text-muted-foreground">
                    Bienvenido, {{ auth.user.first_name }} {{ auth.user.last_name }}
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border bg-card p-6 shadow-sm">
                    <p class="text-sm text-muted-foreground">Mis autorizaciones activas</p>
                    <p class="text-3xl font-bold">—</p>
                </div>
                <div class="rounded-xl border bg-card p-6 shadow-sm">
                    <p class="text-sm text-muted-foreground">Mi apartamento</p>
                    <p class="text-3xl font-bold">—</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
