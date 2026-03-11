<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import QRCode from 'qrcode';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Building2,
    CreditCard,
    IdCard,
    Phone,
    User,
    Users,
} from 'lucide-vue-next';

interface Property {
    number: string;
    block: string | null;
    type: string;
    full_label: string;
}
interface FamilyMember {
    id: number;
    full_name: string;
    cedula: string;
    relationship: string;
    phone: string | null;
}
interface Carnet {
    id: number;
    first_name: string;
    last_name: string;
    full_name: string;
    cedula: string;
    phone: string | null;
    username: string;
    email: string;
    role: string;
    property: Property | null;
    family: FamilyMember[];
    avatar_url: string | null;
}

const props = defineProps<{ carnet: Carnet }>();

const qrDataUrl = ref<string>('');

onMounted(async () => {
    try {
        qrDataUrl.value = await QRCode.toDataURL(props.carnet.cedula, {
            width: 200,
            margin: 1,
            color: { dark: '#0f172a', light: '#ffffff' },
        });
    } catch (_) { /* noop */ }
});

const roleConfig: Record<string, { label: string; color: string; gradient: string; badge: string }> = {
    Propietario: {
        label: 'PROPIETARIO',
        color: 'text-blue-400',
        gradient: 'from-blue-600/20 via-blue-500/5 to-transparent',
        badge: 'bg-blue-500/20 text-blue-300 border border-blue-500/30',
    },
    Residente: {
        label: 'RESIDENTE',
        color: 'text-violet-400',
        gradient: 'from-violet-600/20 via-violet-500/5 to-transparent',
        badge: 'bg-violet-500/20 text-violet-300 border border-violet-500/30',
    },
};

const cfg = roleConfig[props.carnet.role] ?? {
    label: props.carnet.role.toUpperCase(),
    color: 'text-primary',
    gradient: 'from-primary/20 via-primary/5 to-transparent',
    badge: 'bg-primary/20 text-primary border border-primary/30',
};

const initials = (props.carnet.first_name[0] ?? '') + (props.carnet.last_name[0] ?? '');

const typeLabel: Record<string, string> = {
    apartment: 'Apartamento',
    house: 'Casa',
    local: 'Local',
    office: 'Oficina',
};
</script>

<template>
    <AppLayout>
        <Head title="Mi Carnet" />
        <div class="flex flex-col gap-6 p-4 sm:p-6">

            <!-- Header -->
            <div class="page-header">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">ACCESO · GUARD</p>
                <h1 class="mt-1 text-2xl font-bold">Mi Carnet Digital</h1>
                <p class="mt-0.5 text-sm text-muted-foreground">Presenta este carnet al vigilante para identificarte</p>
            </div>

            <!-- Carnet card -->
            <div class="mx-auto w-full max-w-sm">
                <div :class="['relative overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br shadow-2xl shadow-black/40', cfg.gradient]"
                    style="background-color: #0d1117;">

                    <!-- Hexagon pattern background -->
                    <div class="pointer-events-none absolute inset-0 opacity-[0.035]"
                        style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2228%22 height=%2232%22%3E%3Cpolygon points=%2214,1 27,8 27,24 14,31 1,24 1,8%22 fill=%22none%22 stroke=%22white%22 stroke-width=%221%22/%3E%3C/svg%3E'); background-size: 28px 32px;" />

                    <!-- Top accent line -->
                    <div :class="['absolute top-0 left-0 right-0 h-[2px]', carnet.role === 'Propietario' ? 'bg-gradient-to-r from-transparent via-blue-400 to-transparent' : 'bg-gradient-to-r from-transparent via-violet-400 to-transparent']" />

                    <!-- Card content -->
                    <div class="relative flex flex-col gap-5 p-6">

                        <!-- Top row: logo + role badge -->
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[9px] font-black tracking-[0.25em] text-white/40">ACCESO·GUARD</p>
                                <p class="text-[8px] tracking-widest text-white/20 uppercase">Sistema de Control de Acceso</p>
                            </div>
                            <span :class="['rounded-full px-3 py-1 text-[10px] font-black tracking-[0.15em] uppercase', cfg.badge]">
                                {{ cfg.label }}
                            </span>
                        </div>

                        <!-- Avatar + Name -->
                        <div class="flex items-center gap-4">
                            <div :class="['relative flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl text-xl font-black text-white shadow-lg', carnet.role === 'Propietario' ? 'bg-blue-600 shadow-blue-600/30' : 'bg-violet-600 shadow-violet-600/30']">
                                <img v-if="carnet.avatar_url" :src="carnet.avatar_url" alt="Foto" class="h-full w-full object-cover" />
                                <span v-else>{{ initials.toUpperCase() }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-white/40">Nombre completo</p>
                                <p class="mt-0.5 text-lg font-bold leading-tight text-white">{{ carnet.full_name }}</p>
                                <p class="text-xs text-white/40">@{{ carnet.username }}</p>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="h-px bg-white/8" />

                        <!-- Info grid -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex items-start gap-2">
                                <CreditCard class="mt-0.5 h-3.5 w-3.5 shrink-0 text-white/30" />
                                <div>
                                    <p class="text-[9px] uppercase tracking-widest text-white/30">Cédula</p>
                                    <p class="font-mono text-xs font-semibold text-white">{{ carnet.cedula }}</p>
                                </div>
                            </div>
                            <div v-if="carnet.phone" class="flex items-start gap-2">
                                <Phone class="mt-0.5 h-3.5 w-3.5 shrink-0 text-white/30" />
                                <div>
                                    <p class="text-[9px] uppercase tracking-widest text-white/30">Teléfono</p>
                                    <p class="text-xs font-semibold text-white">{{ carnet.phone }}</p>
                                </div>
                            </div>
                            <div v-if="carnet.property" class="col-span-2 flex items-start gap-2">
                                <Building2 class="mt-0.5 h-3.5 w-3.5 shrink-0 text-white/30" />
                                <div>
                                    <p class="text-[9px] uppercase tracking-widest text-white/30">Unidad</p>
                                    <p class="text-sm font-bold text-white">
                                        {{ carnet.property.full_label }}
                                        <span class="ml-1 text-[10px] font-normal text-white/40 uppercase">
                                            {{ typeLabel[carnet.property.type] ?? carnet.property.type }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div v-else class="col-span-2 flex items-start gap-2">
                                <Building2 class="mt-0.5 h-3.5 w-3.5 shrink-0 text-white/30" />
                                <div>
                                    <p class="text-[9px] uppercase tracking-widest text-white/30">Unidad</p>
                                    <p class="text-xs text-white/40 italic">Sin unidad asignada</p>
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="h-px bg-white/8" />

                        <!-- QR Code -->
                        <div class="flex flex-col items-center gap-2">
                            <div v-if="qrDataUrl" class="rounded-xl bg-white p-2.5 shadow-inner">
                                <img :src="qrDataUrl" alt="QR" class="h-32 w-32" />
                            </div>
                            <div v-else class="flex h-36 w-36 items-center justify-center rounded-xl bg-white/5">
                                <span class="text-xs text-white/20">Generando QR...</span>
                            </div>
                            <div class="text-center">
                                <p class="text-[9px] uppercase tracking-[0.2em] text-white/30">Escanear para registro</p>
                                <p class="font-mono text-[10px] text-white/20">{{ carnet.cedula }}</p>
                            </div>
                        </div>

                        <!-- Bottom serial line -->
                        <div class="flex items-center justify-between border-t border-white/8 pt-3">
                            <p class="font-mono text-[8px] text-white/20 uppercase tracking-widest">ID-{{ String(carnet.id).padStart(6, '0') }}</p>
                            <p class="font-mono text-[8px] text-white/20">ACCESO·GUARD © 2026</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Núcleo familiar -->
            <div v-if="carnet.family.length > 0" class="overflow-hidden rounded-xl border border-primary/10 bg-card shadow-sm">
                <div class="flex items-center gap-2 border-b border-primary/10 bg-primary/[0.03] px-5 py-4">
                    <Users class="h-4 w-4 text-primary" />
                    <h2 class="font-semibold">Núcleo Familiar</h2>
                    <span class="rounded-full bg-primary/10 px-2 py-0.5 text-xs font-bold text-primary">{{ carnet.family.length }}</span>
                </div>
                <div class="divide-y divide-border/50">
                    <div v-for="member in carnet.family" :key="member.id"
                        class="flex items-center gap-4 px-5 py-3.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-xs font-bold text-primary">
                            {{ (member.full_name[0] ?? '?').toUpperCase() }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold leading-tight">{{ member.full_name }}</p>
                            <p class="text-xs text-muted-foreground capitalize">{{ member.relationship }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-mono text-xs text-muted-foreground">{{ member.cedula }}</p>
                            <p v-if="member.phone" class="text-xs text-muted-foreground">{{ member.phone }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sin familia -->
            <div v-else-if="carnet.role === 'Propietario'" class="rounded-xl border border-dashed border-muted-foreground/20 p-6 text-center">
                <Users class="mx-auto mb-2 h-8 w-8 text-muted-foreground/30" />
                <p class="text-sm text-muted-foreground">No tienes familiares registrados.</p>
                <p class="mt-0.5 text-xs text-muted-foreground/60">El administrador puede añadirlos desde el panel.</p>
            </div>

        </div>
    </AppLayout>
</template>
