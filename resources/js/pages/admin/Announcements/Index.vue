<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Bell, Plus } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface Announcement {
    id: number;
    title: string;
    body: string;
    target: string;
    send_push: boolean;
    author: string;
    created_at: string;
    reads: number;
}

defineProps<{ announcements: Announcement[] }>();

const targetLabel: Record<string, string> = {
    all: 'Todos',
    propietario: 'Propietarios',
    residente: 'Residentes',
};
const targetClass: Record<string, string> = {
    all: 'bg-primary/10 text-primary',
    propietario: 'bg-blue-100 text-blue-700',
    residente: 'bg-violet-100 text-violet-700',
};
</script>

<template>
    <AppLayout>
        <Head title="Comunicados" />
        <div class="flex flex-col gap-5 p-4 sm:p-6">

            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="page-header">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">ACCESO · GUARD</p>
                    <h1 class="mt-1 text-2xl font-bold">Comunicados</h1>
                    <p class="mt-0.5 text-sm text-muted-foreground">Gestiona los avisos enviados a residentes y propietarios</p>
                </div>
                <Link href="/admin/announcements/create"
                    class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow transition hover:opacity-90">
                    <Plus class="h-4 w-4" />
                    Nuevo Comunicado
                </Link>
            </div>

            <div v-if="announcements.length === 0"
                class="flex flex-col items-center gap-3 rounded-xl border border-dashed border-muted-foreground/20 py-16">
                <Bell class="h-10 w-10 text-muted-foreground/30" />
                <p class="text-sm text-muted-foreground">No hay comunicados enviados aún.</p>
                <Link href="/admin/announcements/create" class="text-sm font-semibold text-primary underline">Crear el primero</Link>
            </div>

            <div v-else class="overflow-hidden rounded-xl border border-primary/10 bg-card shadow-sm">
                <table class="data-table w-full text-sm">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Dirigido a</th>
                            <th>Enviado por</th>
                            <th>Fecha</th>
                            <th class="text-center">Leídos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="a in announcements" :key="a.id">
                            <td class="px-5 py-3">
                                <p class="font-semibold">{{ a.title }}</p>
                                <p class="mt-0.5 text-xs text-muted-foreground line-clamp-1">{{ a.body }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <span :class="['rounded-full px-2.5 py-0.5 text-xs font-semibold', targetClass[a.target]]">
                                    {{ targetLabel[a.target] }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-muted-foreground">{{ a.author }}</td>
                            <td class="px-5 py-3 text-xs text-muted-foreground">{{ a.created_at }}</td>
                            <td class="px-5 py-3 text-center font-mono font-semibold">{{ a.reads }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </AppLayout>
</template>
