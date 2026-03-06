<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';

interface Entry {
    id: number;
    full_name: string;
    cedula: string;
    type: string;
    vehicle: string;
    plate: string | null;
    entry_at: string;
    exit_at: string | null;
    is_inside: boolean;
}

interface PaginatedEntries {
    data: Entry[];
    current_page: number;
    last_page: number;
    from: number;
    to: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

interface Filters {
    type?: string;
    date_from?: string;
    date_to?: string;
}

const props = defineProps<{
    entries: PaginatedEntries;
    filters: Filters;
    apartment: string | null;
}>();

const typeVariant: Record<string, 'default' | 'secondary' | 'outline'> = {
    propietario: 'default',
    autorizado: 'secondary',
    visitante: 'outline',
};

const typeLabel: Record<string, string> = {
    propietario: 'Propietario',
    autorizado: 'Autorizado',
    visitante: 'Visitante',
};

const vehicleLabel: Record<string, string> = {
    ninguno: '—',
    automovil: 'Auto',
    camioneta: 'Camioneta',
    moto: 'Moto',
    bicicleta: 'Bici',
};

const localFilters = ref<Filters>({ ...props.filters });

let filterTimeout: ReturnType<typeof setTimeout> | null = null;

watch(
    localFilters,
    () => {
        if (filterTimeout) clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            router.get('/propietario/history', localFilters.value, {
                preserveState: true,
                replace: true,
            });
        }, 400);
    },
    { deep: true },
);
</script>

<template>
    <AppLayout>
        <Head title="Historial de Visitas" />

        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold">Historial de Visitas</h1>
                <p class="text-sm text-muted-foreground">
                    Accesos registrados
                    <span v-if="apartment">
                        al Apartamento <strong>{{ apartment }}</strong></span
                    >
                    &mdash; {{ entries.total }} registro(s) en total
                </p>
            </div>

            <!-- Filtros -->
            <div class="flex flex-wrap items-end gap-3">
                <div class="grid w-40 gap-1">
                    <label class="text-xs font-medium text-muted-foreground"
                        >Tipo</label
                    >
                    <select
                        v-model="localFilters.type"
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                    >
                        <option value="">Todos</option>
                        <option value="propietario">Propietario</option>
                        <option value="autorizado">Autorizado</option>
                        <option value="visitante">Visitante</option>
                    </select>
                </div>
                <div class="grid gap-1">
                    <label class="text-xs font-medium text-muted-foreground"
                        >Desde</label
                    >
                    <Input
                        v-model="localFilters.date_from"
                        type="date"
                        class="w-40"
                    />
                </div>
                <div class="grid gap-1">
                    <label class="text-xs font-medium text-muted-foreground"
                        >Hasta</label
                    >
                    <Input
                        v-model="localFilters.date_to"
                        type="date"
                        class="w-40"
                    />
                </div>
            </div>

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
                                Tipo
                            </th>
                            <th class="px-4 py-3 text-left font-medium">
                                Vehículo
                            </th>
                            <th class="px-4 py-3 text-left font-medium">
                                Ingreso
                            </th>
                            <th class="px-4 py-3 text-left font-medium">
                                Salida
                            </th>
                            <th class="px-4 py-3 text-left font-medium">
                                Estado
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="entry in entries.data"
                            :key="entry.id"
                            class="border-b last:border-0 hover:bg-muted/30"
                        >
                            <td class="px-4 py-3 font-medium">
                                {{ entry.full_name }}
                            </td>
                            <td
                                class="px-4 py-3 font-mono text-xs text-muted-foreground"
                            >
                                {{ entry.cedula }}
                            </td>
                            <td class="px-4 py-3">
                                <Badge :variant="typeVariant[entry.type]">{{
                                    typeLabel[entry.type] ?? entry.type
                                }}</Badge>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span class="text-muted-foreground">{{
                                    vehicleLabel[entry.vehicle] ?? entry.vehicle
                                }}</span>
                                <span
                                    v-if="entry.plate"
                                    class="ml-1 font-mono font-semibold"
                                    >{{ entry.plate }}</span
                                >
                            </td>
                            <td class="px-4 py-3 font-mono text-xs">
                                {{ entry.entry_at }}
                            </td>
                            <td
                                class="px-4 py-3 font-mono text-xs text-muted-foreground"
                            >
                                {{ entry.exit_at ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    v-if="entry.is_inside"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-600"
                                >
                                    <span
                                        class="h-2 w-2 rounded-full bg-green-500"
                                    ></span>
                                    Dentro
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center gap-1.5 text-xs text-muted-foreground"
                                >
                                    <span
                                        class="h-2 w-2 rounded-full bg-slate-300"
                                    ></span>
                                    Salió
                                </span>
                            </td>
                        </tr>
                        <tr v-if="entries.data.length === 0">
                            <td
                                colspan="7"
                                class="px-4 py-12 text-center text-muted-foreground"
                            >
                                No hay visitas registradas con los filtros
                                actuales.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div
                v-if="entries.last_page > 1"
                class="flex items-center justify-between text-sm"
            >
                <p class="text-muted-foreground">
                    Mostrando {{ entries.from }}–{{ entries.to }} de
                    {{ entries.total }} registros
                </p>
                <div class="flex items-center gap-1">
                    <template v-for="link in entries.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-state
                            :class="[
                                'inline-flex h-8 min-w-8 items-center justify-center rounded-md px-2 text-sm transition-colors',
                                link.active
                                    ? 'bg-primary font-semibold text-primary-foreground'
                                    : 'hover:bg-muted',
                            ]"
                        >
                            <ChevronLeft
                                v-if="link.label === '&laquo; Previous'"
                                class="h-4 w-4"
                            />
                            <ChevronRight
                                v-else-if="link.label === 'Next &raquo;'"
                                class="h-4 w-4"
                            />
                            <span v-else>{{ link.label }}</span>
                        </Link>
                        <span
                            v-else
                            class="inline-flex h-8 min-w-8 items-center justify-center rounded-md px-2 text-sm text-muted-foreground opacity-50"
                        >
                            <ChevronLeft
                                v-if="link.label === '&laquo; Previous'"
                                class="h-4 w-4"
                            />
                            <ChevronRight
                                v-else-if="link.label === 'Next &raquo;'"
                                class="h-4 w-4"
                            />
                        </span>
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
