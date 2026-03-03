<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import type { Auth } from '@/types';

const { auth } = usePage<{ auth: Auth }>().props;

const form = useForm({
    first_name:   '',
    last_name:    '',
    cedula:       '',
    apartment:    '',
    type:         'visitante',
    vehicle:      'ninguno',
    observations: '',
});

const authorization = ref<{ type: string; end_date: string | null } | null>(null);
const lookupTimeout = ref<ReturnType<typeof setTimeout> | null>(null);
const searching = ref(false);

watch(() => form.cedula, (val) => {
    if (lookupTimeout.value) clearTimeout(lookupTimeout.value);
    authorization.value = null;

    if (val.length < 3) return;

    lookupTimeout.value = setTimeout(async () => {
        searching.value = true;
        try {
            const res = await fetch(`/vigilante/entries/lookup?cedula=${encodeURIComponent(val)}`);
            const data = await res.json();
            if (data) {
                form.first_name = data.first_name ?? form.first_name;
                form.last_name  = data.last_name  ?? form.last_name;
                form.apartment  = data.apartment  ?? form.apartment;
                form.type       = data.type       ?? form.type;
                authorization.value = data.authorization ?? null;
            }
        } finally {
            searching.value = false;
        }
    }, 500);
});

function submit() {
    form.post('/vigilante/entries');
}
</script>

<template>
    <AppLayout>
        <Head title="Registrar Ingreso" />

        <div class="mx-auto max-w-2xl p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Registrar Ingreso</h1>
                <p class="text-sm text-muted-foreground">
                    Vigilante: {{ auth.user.first_name }} {{ auth.user.last_name }}
                </p>
            </div>

            <!-- Alerta de autorización activa -->
            <div v-if="authorization" class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                <span class="font-semibold">Autorización activa</span> —
                Tipo: {{ authorization.type }}
                <span v-if="authorization.end_date"> · Válida hasta: {{ authorization.end_date }}</span>
            </div>

            <form @submit.prevent="submit" class="space-y-5 rounded-xl border bg-card p-6 shadow-sm">

                <!-- Cédula con autocomplete -->
                <div class="grid gap-1.5">
                    <Label for="cedula">Cédula *</Label>
                    <div class="relative">
                        <Input
                            id="cedula"
                            v-model="form.cedula"
                            placeholder="Número de cédula"
                            autocomplete="off"
                        />
                        <span v-if="searching" class="absolute right-3 top-2.5 text-xs text-muted-foreground">
                            buscando...
                        </span>
                    </div>
                    <InputError :message="form.errors.cedula" />
                </div>

                <!-- Nombres y apellidos -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-1.5">
                        <Label for="first_name">Nombres *</Label>
                        <Input id="first_name" v-model="form.first_name" placeholder="Nombres" />
                        <InputError :message="form.errors.first_name" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="last_name">Apellidos *</Label>
                        <Input id="last_name" v-model="form.last_name" placeholder="Apellidos" />
                        <InputError :message="form.errors.last_name" />
                    </div>
                </div>

                <!-- Apartamento y tipo -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-1.5">
                        <Label for="apartment">Apartamento *</Label>
                        <Input id="apartment" v-model="form.apartment" placeholder="Ej: 101" />
                        <InputError :message="form.errors.apartment" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="type">Tipo *</Label>
                        <select
                            id="type"
                            v-model="form.type"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                        >
                            <option value="propietario">Propietario</option>
                            <option value="autorizado">Autorizado</option>
                            <option value="visitante">Visitante</option>
                        </select>
                        <InputError :message="form.errors.type" />
                    </div>
                </div>

                <!-- Vehículo -->
                <div class="grid gap-1.5">
                    <Label for="vehicle">Vehículo</Label>
                    <select
                        id="vehicle"
                        v-model="form.vehicle"
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                    >
                        <option value="ninguno">Ninguno / Peatón</option>
                        <option value="automovil">Automóvil</option>
                        <option value="camioneta">Camioneta</option>
                        <option value="moto">Moto</option>
                        <option value="bicicleta">Bicicleta</option>
                    </select>
                    <InputError :message="form.errors.vehicle" />
                </div>

                <!-- Observaciones -->
                <div class="grid gap-1.5">
                    <Label for="observations">Observaciones</Label>
                    <textarea
                        id="observations"
                        v-model="form.observations"
                        rows="2"
                        placeholder="Observaciones opcionales..."
                        class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground"
                    />
                    <InputError :message="form.errors.observations" />
                </div>

                <Button type="submit" class="w-full" :disabled="form.processing">
                    Registrar Ingreso
                </Button>
            </form>
        </div>
    </AppLayout>
</template>
