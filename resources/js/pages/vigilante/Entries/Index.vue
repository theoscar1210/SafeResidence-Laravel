<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';

interface Entry {
    id: number;
    full_name: string;
    cedula: string;
    apartment: string;
    type: string;
    vehicle: string;
    entry_at: string;
    is_inside: boolean;
    registered_by: string | null;
}

interface Stats {
    total: number;
    inside: number;
    propietario: number;
    autorizado: number;
    visitante: number;
}

const props = defineProps<{ entries: Entry[]; stats: Stats }>();

const search = ref('');

const filtered = computed(() =>
    props.entries.filter(
        (e) =>
            e.full_name.toLowerCase().includes(search.value.toLowerCase()) ||
            e.cedula.includes(search.value) ||
            e.apartment.includes(search.value),
    ),
);

const typeLabel: Record<string, string> = {
    propietario: 'Propietario',
    autorizado: 'Autorizado',
    visitante: 'Visitante',
};

const typeVariant: Record<string, 'default' | 'secondary' | 'outline'> = {
    propietario: 'default',
    autorizado: 'secondary',
    visitante: 'outline',
};
</script>

<template>
    <AppLayout>
        <Head title="Monitor de Ingresos" />

        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-bold">Monitor de Ingresos</h1>
                    <p class="text-sm text-muted-foreground">
                        Ingresos del día de hoy
                    </p>
                </div>
                <Link href="/vigilante/entries/create">
                    <Button>+ Registrar Ingreso</Button>
                </Link>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                <div
                    class="rounded-xl border bg-card p-4 text-center shadow-sm"
                >
                    <p class="text-xs text-muted-foreground">Dentro ahora</p>
                    <p class="text-3xl font-bold text-primary">
                        {{ stats.inside }}
                    </p>
                </div>
                <div
                    class="rounded-xl border bg-card p-4 text-center shadow-sm"
                >
                    <p class="text-xs text-muted-foreground">Propietarios</p>
                    <p class="text-3xl font-bold">{{ stats.propietario }}</p>
                </div>
                <div
                    class="rounded-xl border bg-card p-4 text-center shadow-sm"
                >
                    <p class="text-xs text-muted-foreground">Autorizados</p>
                    <p class="text-3xl font-bold">{{ stats.autorizado }}</p>
                </div>
                <div
                    class="rounded-xl border bg-card p-4 text-center shadow-sm"
                >
                    <p class="text-xs text-muted-foreground">Visitantes</p>
                    <p class="text-3xl font-bold">{{ stats.visitante }}</p>
                </div>
            </div>

            <!-- Búsqueda -->
            <input
                v-model="search"
                type="text"
                placeholder="Buscar por nombre, cédula o apartamento..."
                class="flex h-9 w-full max-w-sm rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm placeholder:text-muted-foreground"
            />

            <!-- Tabla -->
            <div class="overflow-x-auto rounded-xl border bg-card shadow-sm">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">
                                Nombre
                            </th>
                            <th class="px-4 py-3 text-left font-medium">
                                Cédula
                            </th>
                            <th class="px-4 py-3 text-left font-medium">
                                Apto
                            </th>
                            <th class="px-4 py-3 text-left font-medium">
                                Tipo
                            </th>
                            <th class="px-4 py-3 text-left font-medium">
                                Hora
                            </th>
                            <th class="px-4 py-3 text-left font-medium">
                                Estado
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="entry in filtered"
                            :key="entry.id"
                            class="border-b last:border-0 hover:bg-muted/30"
                        >
                            <td class="px-4 py-3 font-medium">
                                {{ entry.full_name }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ entry.cedula }}
                            </td>
                            <td class="px-4 py-3">{{ entry.apartment }}</td>
                            <td class="px-4 py-3">
                                <Badge :variant="typeVariant[entry.type]">
                                    {{ typeLabel[entry.type] }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3">{{ entry.entry_at }}</td>
                            <td class="px-4 py-3">
                                <span
                                    v-if="entry.is_inside"
                                    class="inline-flex items-center gap-1 font-medium text-green-600"
                                >
                                    <span
                                        class="inline-block h-2 w-2 rounded-full bg-green-500"
                                    ></span>
                                    Dentro
                                </span>
                                <span v-else class="text-muted-foreground"
                                    >Salió</span
                                >
                            </td>
                        </tr>
                        <tr v-if="filtered.length === 0">
                            <td
                                colspan="6"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                No hay ingresos registrados hoy.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
