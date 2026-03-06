<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { AlertCircle, CheckCircle2, Loader2 } from 'lucide-vue-next';
import { onMounted, ref, watch, computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Auth } from '@/types';

const { auth } = usePage<{ auth: Auth }>().props;

const form = useForm({
    first_name: '',
    last_name: '',
    cedula: '',
    apartment: '',
    type: 'visitante',
    vehicle: 'ninguno',
    plate: '',
    observations: '',
});

const authorization = ref<{ type: string; end_date: string | null } | null>(
    null,
);
const noAuthorization = ref(false);
const lookupTimeout = ref<ReturnType<typeof setTimeout> | null>(null);
const searching = ref(false);
const lookupDone = ref(false);

async function lookup(cedula: string) {
    if (cedula.length < 3) {
        lookupDone.value = false;
        noAuthorization.value = false;
        authorization.value = null;
        return;
    }
    searching.value = true;
    try {
        const res = await fetch(
            `/vigilante/entries/lookup?cedula=${encodeURIComponent(cedula)}`,
        );
        const data = await res.json();
        if (data) {
            form.first_name = data.first_name ?? form.first_name;
            form.last_name = data.last_name ?? form.last_name;
            form.apartment = data.apartment ?? form.apartment;
            form.type = data.type ?? form.type;
            authorization.value = data.authorization ?? null;
            noAuthorization.value = !data.authorization;
        } else {
            authorization.value = null;
            noAuthorization.value = false;
        }
        lookupDone.value = true;
    } finally {
        searching.value = false;
    }
}

watch(
    () => form.cedula,
    (val) => {
        if (lookupTimeout.value) clearTimeout(lookupTimeout.value);
        authorization.value = null;
        noAuthorization.value = false;
        lookupDone.value = false;
        lookupTimeout.value = setTimeout(() => lookup(val), 500);
    },
);

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const cedula = params.get('cedula');
    if (cedula) {
        form.cedula = cedula;
        lookup(cedula);
    }
});

function submit() {
    form.post('/vigilante/entries');
}

const typeOptions = [
    { value: 'visitante', label: 'Visitante', color: 'amber' },
    { value: 'autorizado', label: 'Autorizado', color: 'green' },
    { value: 'propietario', label: 'Propietario', color: 'blue' },
];

const typeButtonClass = (value: string, color: string) => {
    const active = form.type === value;
    const map: Record<string, string> = {
        amber: active
            ? 'border-amber-400 bg-amber-50 text-amber-800 ring-2 ring-amber-300'
            : 'border-input hover:border-amber-300 hover:bg-amber-50/50',
        green: active
            ? 'border-green-400 bg-green-50 text-green-800 ring-2 ring-green-300'
            : 'border-input hover:border-green-300 hover:bg-green-50/50',
        blue: active
            ? 'border-blue-400  bg-blue-50  text-blue-800  ring-2 ring-blue-300'
            : 'border-input hover:border-blue-300  hover:bg-blue-50/50',
    };
    return map[color];
};

const needsPlate = computed(() => form.vehicle !== 'ninguno');
</script>

<template>
    <AppLayout>
        <Head title="Registrar Ingreso" />

        <div class="mx-auto max-w-2xl p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Registrar Ingreso</h1>
                <p class="text-sm text-muted-foreground">
                    Vigilante: {{ auth.user.first_name }}
                    {{ auth.user.last_name }}
                </p>
            </div>

            <!-- Alerta: AUTORIZACIÓN ACTIVA -->
            <Transition name="alert">
                <div
                    v-if="authorization"
                    class="mb-4 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
                >
                    <CheckCircle2
                        class="mt-0.5 h-4 w-4 shrink-0 text-green-600"
                    />
                    <div>
                        <p class="font-semibold">
                            Autorización activa encontrada
                        </p>
                        <p class="text-green-700">
                            Tipo:
                            <span class="font-medium">{{
                                authorization.type
                            }}</span>
                            <span v-if="authorization.end_date">
                                &mdash; Válida hasta:
                                <span class="font-medium">{{
                                    authorization.end_date
                                }}</span></span
                            >
                            <span v-else>
                                &mdash; Sin fecha de vencimiento</span
                            >
                        </p>
                    </div>
                </div>
            </Transition>

            <!-- Alerta: SIN AUTORIZACIÓN -->
            <Transition name="alert">
                <div
                    v-if="
                        noAuthorization && lookupDone && form.cedula.length >= 3
                    "
                    class="mb-4 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800"
                >
                    <AlertCircle
                        class="mt-0.5 h-4 w-4 shrink-0 text-amber-600"
                    />
                    <div>
                        <p class="font-semibold">Sin autorización previa</p>
                        <p class="text-amber-700">
                            Esta persona no tiene autorización activa. Puedes
                            registrar el ingreso de todas formas.
                        </p>
                    </div>
                </div>
            </Transition>

            <form
                @submit.prevent="submit"
                class="space-y-5 rounded-xl border bg-card p-6 shadow-sm"
            >
                <!-- Cédula con indicador de búsqueda -->
                <div class="grid gap-1.5">
                    <Label for="cedula">Cédula *</Label>
                    <div class="relative">
                        <Input
                            id="cedula"
                            v-model="form.cedula"
                            placeholder="Número de cédula"
                            autocomplete="off"
                            class="pr-24 font-mono text-base"
                        />
                        <span
                            v-if="searching"
                            class="absolute top-2.5 right-3 flex items-center gap-1 text-xs text-muted-foreground"
                        >
                            <Loader2 class="h-3 w-3 animate-spin" />
                            buscando...
                        </span>
                        <span
                            v-else-if="lookupDone && authorization"
                            class="absolute top-2.5 right-3 text-xs font-medium text-green-600"
                        >
                            ✓ Autorizado
                        </span>
                    </div>
                    <InputError :message="form.errors.cedula" />
                </div>

                <!-- Nombres y apellidos -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-1.5">
                        <Label for="first_name">Nombres *</Label>
                        <Input
                            id="first_name"
                            v-model="form.first_name"
                            placeholder="Nombres"
                        />
                        <InputError :message="form.errors.first_name" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="last_name">Apellidos *</Label>
                        <Input
                            id="last_name"
                            v-model="form.last_name"
                            placeholder="Apellidos"
                        />
                        <InputError :message="form.errors.last_name" />
                    </div>
                </div>

                <!-- Apartamento -->
                <div class="grid gap-1.5">
                    <Label for="apartment">Apartamento destino *</Label>
                    <Input
                        id="apartment"
                        v-model="form.apartment"
                        placeholder="Ej: 101, Torre A-302"
                    />
                    <InputError :message="form.errors.apartment" />
                </div>

                <!-- Tipo — botones visuales -->
                <div class="grid gap-2">
                    <Label>Tipo de persona *</Label>
                    <div class="grid grid-cols-3 gap-2">
                        <button
                            v-for="opt in typeOptions"
                            :key="opt.value"
                            type="button"
                            @click="form.type = opt.value"
                            :class="[
                                'flex flex-col items-center rounded-lg border-2 py-3 text-sm font-semibold transition-all',
                                typeButtonClass(opt.value, opt.color),
                            ]"
                        >
                            {{ opt.label }}
                        </button>
                    </div>
                    <InputError :message="form.errors.type" />
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

                <!-- Placa — solo si tiene vehículo -->
                <Transition name="alert">
                    <div v-if="needsPlate" class="grid gap-1.5">
                        <Label for="plate">Placa del vehículo</Label>
                        <Input
                            id="plate"
                            v-model="form.plate"
                            placeholder="Ej: ABC-123"
                            class="font-mono tracking-widest uppercase"
                            @input="
                                form.plate = (form.plate ?? '').toUpperCase()
                            "
                        />
                        <InputError :message="form.errors.plate" />
                    </div>
                </Transition>

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

                <Button
                    type="submit"
                    class="w-full"
                    size="lg"
                    :disabled="form.processing"
                >
                    <Loader2
                        v-if="form.processing"
                        class="mr-2 h-4 w-4 animate-spin"
                    />
                    {{
                        form.processing ? 'Registrando...' : 'Registrar Ingreso'
                    }}
                </Button>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.alert-enter-active,
.alert-leave-active {
    transition: all 0.2s ease;
}
.alert-enter-from,
.alert-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}
</style>
