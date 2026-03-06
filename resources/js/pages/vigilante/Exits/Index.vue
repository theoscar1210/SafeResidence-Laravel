<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';

interface Entry {
    id: number;
    full_name: string;
    cedula: string;
    apartment: string;
    type: string;
    vehicle: string;
    entry_at: string;
}

const props = defineProps<{ inside: Entry[] }>();

const { props: pageProps } = usePage<{ flash?: { success?: string } }>();

const selected = ref<number[]>([]);

const form = useForm({
    entry_ids: [] as number[],
    observations: '',
});

const search = ref('');

const filtered = computed(() =>
    props.inside.filter(
        (e) =>
            e.full_name.toLowerCase().includes(search.value.toLowerCase()) ||
            e.cedula.includes(search.value) ||
            e.apartment.includes(search.value),
    ),
);

function toggle(id: number) {
    const idx = selected.value.indexOf(id);
    if (idx === -1) selected.value.push(id);
    else selected.value.splice(idx, 1);
}

function toggleAll() {
    if (selected.value.length === filtered.value.length) {
        selected.value = [];
    } else {
        selected.value = filtered.value.map((e) => e.id);
    }
}

function submit() {
    form.entry_ids = selected.value;
    form.post('/vigilante/exits', {
        onSuccess: () => {
            selected.value = [];
        },
    });
}

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
</script>

<template>
    <AppLayout>
        <Head title="Registrar Salidas" />

        <div class="flex flex-col gap-6 p-6">

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Registrar Salidas</h1>
                    <p class="text-sm text-muted-foreground">
                        {{ inside.length }} persona(s) dentro del edificio
                    </p>
                </div>
                <Button
                    @click="submit"
                    :disabled="selected.length === 0 || form.processing"
                    class="min-w-36"
                >
                    Registrar salida
                    <span v-if="selected.length > 0" class="ml-1">({{ selected.length }})</span>
                </Button>
            </div>

            <!-- Flash success -->
            <div
                v-if="pageProps.flash?.success"
                class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800"
            >
                {{ pageProps.flash.success }}
            </div>

            <!-- Sin personas dentro -->
            <div
                v-if="inside.length === 0"
                class="rounded-xl border bg-card p-12 text-center text-muted-foreground shadow-sm"
            >
                No hay personas dentro del edificio en este momento.
            </div>

            <template v-else>
                <!-- Búsqueda y seleccionar todos -->
                <div class="flex items-center gap-3">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Buscar por nombre, cédula o apartamento..."
                        class="flex h-9 w-full max-w-sm rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm placeholder:text-muted-foreground"
                    />
                    <button
                        @click="toggleAll"
                        class="text-sm text-primary underline-offset-4 hover:underline"
                    >
                        {{ selected.length === filtered.length ? 'Deseleccionar todos' : 'Seleccionar todos' }}
                    </button>
                </div>

                <!-- Lista de personas dentro -->
                <div class="grid gap-2">
                    <div
                        v-for="entry in filtered"
                        :key="entry.id"
                        @click="toggle(entry.id)"
                        :class="[
                            'flex cursor-pointer items-center gap-4 rounded-xl border bg-card p-4 shadow-sm transition-colors',
                            selected.includes(entry.id)
                                ? 'border-primary bg-primary/5'
                                : 'hover:bg-muted/30',
                        ]"
                    >
                        <!-- Checkbox -->
                        <div
                            :class="[
                                'flex h-5 w-5 shrink-0 items-center justify-center rounded border-2 transition-colors',
                                selected.includes(entry.id)
                                    ? 'border-primary bg-primary text-primary-foreground'
                                    : 'border-muted-foreground',
                            ]"
                        >
                            <svg v-if="selected.includes(entry.id)" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>

                        <!-- Info -->
                        <div class="flex flex-1 items-center justify-between">
                            <div>
                                <p class="font-semibold">{{ entry.full_name }}</p>
                                <p class="text-sm text-muted-foreground">
                                    CC {{ entry.cedula }} · Apto {{ entry.apartment }}
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <Badge :variant="typeVariant[entry.type]">
                                    {{ typeLabel[entry.type] }}
                                </Badge>
                                <span class="text-xs text-muted-foreground">
                                    Ingresó {{ entry.entry_at }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observaciones (opcional) -->
                <div v-if="selected.length > 0" class="grid gap-1.5 max-w-lg">
                    <label class="text-sm font-medium">Observaciones (opcional)</label>
                    <textarea
                        v-model="form.observations"
                        rows="2"
                        placeholder="Observaciones sobre la salida..."
                        class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground"
                    />
                </div>
            </template>
        </div>
    </AppLayout>
</template>
