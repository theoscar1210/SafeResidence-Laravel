<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ref, computed } from 'vue';

interface Authorization {
    id: number;
    full_name: string;
    cedula: string;
    type: string;
    end_date: string | null;
    owner: string;
    observations: string | null;
}

const props = defineProps<{ authorizations: Authorization[] }>();

const search = ref('');

const filtered = computed(() =>
    props.authorizations.filter(
        (a) =>
            a.full_name.toLowerCase().includes(search.value.toLowerCase()) ||
            a.cedula.includes(search.value),
    ),
);

const typeVariant: Record<string, 'default' | 'secondary'> = {
    visitante:  'secondary',
    autorizado: 'default',
};

const typeLabel: Record<string, string> = {
    visitante:  'Visitante',
    autorizado: 'Autorizado',
};
</script>

<template>
    <AppLayout>
        <Head title="Autorizaciones Activas" />

        <div class="flex flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Autorizaciones Activas</h1>
                    <p class="text-sm text-muted-foreground">
                        {{ authorizations.length }} autorización(es) vigente(s)
                    </p>
                </div>
            </div>

            <!-- Búsqueda -->
            <input
                v-model="search"
                type="text"
                placeholder="Buscar por nombre o cédula..."
                class="flex h-9 w-full max-w-sm rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm placeholder:text-muted-foreground"
            />

            <!-- Vacío -->
            <div
                v-if="filtered.length === 0"
                class="rounded-xl border bg-card p-12 text-center text-muted-foreground shadow-sm"
            >
                No hay autorizaciones activas.
            </div>

            <!-- Lista -->
            <div v-else class="grid gap-3">
                <div
                    v-for="auth in filtered"
                    :key="auth.id"
                    class="flex items-start justify-between rounded-xl border bg-card p-5 shadow-sm"
                >
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold">{{ auth.full_name }}</span>
                            <Badge :variant="typeVariant[auth.type]">
                                {{ typeLabel[auth.type] }}
                            </Badge>
                        </div>
                        <p class="text-sm text-muted-foreground">CC {{ auth.cedula }}</p>
                        <p class="text-sm text-muted-foreground">
                            Propietario: {{ auth.owner }}
                        </p>
                        <p v-if="auth.end_date" class="text-sm text-muted-foreground">
                            Válida hasta: {{ auth.end_date }}
                        </p>
                        <p v-if="auth.observations" class="text-sm italic text-muted-foreground">
                            "{{ auth.observations }}"
                        </p>
                    </div>

                    <!-- Botón rápido de registrar ingreso -->
                    <Link
                        :href="`/vigilante/entries/create?cedula=${auth.cedula}`"
                        class="ml-4 shrink-0"
                    >
                        <Button size="sm" variant="outline">Registrar ingreso</Button>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
