<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <!-- Forzar modo oscuro en la página de auth -->
    <div class="dark auth-page relative flex min-h-svh items-center justify-center overflow-hidden p-4">

        <!-- Fondo base -->
        <div class="absolute inset-0 bg-[#050c1a]" />

        <!-- Grid hexagonal SVG de fondo -->
        <svg class="absolute inset-0 h-full w-full opacity-[0.07]" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="hexgrid" x="0" y="0" width="56" height="64" patternUnits="userSpaceOnUse">
                    <polygon points="28,2 54,16 54,48 28,62 2,48 2,16"
                        fill="none" stroke="#1E6FFF" stroke-width="0.8"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#hexgrid)"/>
        </svg>

        <!-- Orbes de luz animados -->
        <div class="orb-blue absolute top-[-10%] left-[-5%] h-[520px] w-[520px] rounded-full" />
        <div class="orb-teal absolute bottom-[-15%] right-[-5%] h-[420px] w-[420px] rounded-full" />
        <div class="orb-blue2 absolute top-[40%] right-[20%] h-[200px] w-[200px] rounded-full" />

        <!-- Línea horizontal decorativa -->
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-[#1E6FFF]/60 to-transparent" />
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-[#00D9B8]/40 to-transparent" />

        <!-- Card glassmorphism -->
        <div class="relative z-10 w-full max-w-sm">

            <!-- Logo centrado arriba -->
            <div class="mb-8 flex flex-col items-center gap-3">
                <Link :href="home()" class="group flex flex-col items-center gap-3">
                    <!-- Isotipo con glow -->
                    <div class="logo-glow relative">
                        <svg width="56" height="64" viewBox="0 0 52 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="26,2 49,14.5 49,43.5 26,56 3,43.5 3,14.5" fill="#1E6FFF" fill-opacity="0.2"/>
                            <polygon points="26,2 49,14.5 49,43.5 26,56 3,43.5 3,14.5" fill="none" stroke="#1E6FFF" stroke-width="1.8"/>
                            <path d="M18 30 C18 23.37 21.58 18 26 18 C30.42 18 34 23.37 34 30" stroke="#00D9B8" stroke-width="2.4" fill="none" stroke-linecap="round"/>
                            <rect x="19" y="29" width="14" height="12" rx="2.5" fill="#1E6FFF"/>
                            <circle cx="26" cy="35" r="2" fill="#00D9B8"/>
                            <line x1="26" y1="35" x2="26" y2="38" stroke="#00D9B8" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <!-- Brillo detrás del isotipo -->
                        <div class="absolute inset-0 -z-10 blur-xl bg-[#1E6FFF]/30 scale-150 rounded-full" />
                    </div>
                    <!-- Wordmark -->
                    <div class="flex flex-col items-center leading-none gap-0.5">
                        <span class="text-2xl font-black tracking-[0.22em] text-white" style="font-family:'Syne',sans-serif;">ACCESO</span>
                        <span class="text-[10px] font-semibold tracking-[0.5em] text-[#1E6FFF]" style="font-family:'DM Sans',sans-serif;">GUARD</span>
                    </div>
                </Link>

                <!-- Separador con brillo -->
                <div class="mt-1 h-px w-24 bg-gradient-to-r from-transparent via-[#1E6FFF]/70 to-transparent" />
            </div>

            <!-- Panel glassmorphism -->
            <div class="glass-card relative rounded-2xl border border-white/10 bg-white/5 px-8 py-8 shadow-[0_0_60px_rgba(30,111,255,0.12)] backdrop-blur-2xl">

                <!-- Borde top con gradiente -->
                <div class="absolute top-0 left-8 right-8 h-px bg-gradient-to-r from-transparent via-[#1E6FFF]/50 to-transparent rounded-full" />

                <!-- Título -->
                <div class="mb-6 text-center">
                    <h1 class="text-xl font-bold text-white tracking-wide" style="font-family:'Syne',sans-serif;">
                        {{ title }}
                    </h1>
                    <p class="mt-1 text-sm text-white/50">{{ description }}</p>
                </div>

                <!-- Slot del formulario -->
                <slot />
            </div>

            <!-- Footer -->
            <p class="mt-6 text-center text-xs text-white/25" style="font-family:'DM Sans',sans-serif;">
                Sistema de control de accesos · ACCESO·GUARD
            </p>
        </div>
    </div>
</template>

<style scoped>
/* Orbes de luz */
.orb-blue {
    background: radial-gradient(circle, rgba(30, 111, 255, 0.22) 0%, transparent 70%);
    animation: pulse-slow 8s ease-in-out infinite alternate;
}
.orb-teal {
    background: radial-gradient(circle, rgba(0, 217, 184, 0.16) 0%, transparent 70%);
    animation: pulse-slow 11s ease-in-out infinite alternate-reverse;
}
.orb-blue2 {
    background: radial-gradient(circle, rgba(30, 111, 255, 0.12) 0%, transparent 70%);
    animation: pulse-slow 6s ease-in-out infinite alternate;
}

@keyframes pulse-slow {
    0%   { transform: scale(1) translate(0, 0); opacity: 0.7; }
    100% { transform: scale(1.15) translate(2%, 3%); opacity: 1; }
}

/* Glow del logo */
.logo-glow {
    animation: logo-breathe 4s ease-in-out infinite;
}
@keyframes logo-breathe {
    0%, 100% { filter: drop-shadow(0 0 8px rgba(30,111,255,0.5)); }
    50%       { filter: drop-shadow(0 0 20px rgba(30,111,255,0.9)) drop-shadow(0 0 40px rgba(0,217,184,0.3)); }
}

/* Inputs y labels dentro del slot (dark mode forzado) */
:deep(.auth-page input),
:deep(input) {
    background: rgba(255,255,255,0.06) !important;
    border-color: rgba(255,255,255,0.12) !important;
    color: white !important;
}
:deep(input:focus) {
    border-color: #1E6FFF !important;
    box-shadow: 0 0 0 3px rgba(30,111,255,0.2) !important;
    background: rgba(30,111,255,0.08) !important;
}
:deep(input::placeholder) {
    color: rgba(255,255,255,0.3) !important;
}
:deep(label),
:deep(.text-sm.font-medium) {
    color: rgba(255,255,255,0.75) !important;
}
:deep(button[type="submit"]) {
    background: #1E6FFF !important;
    color: white !important;
    font-weight: 600;
    letter-spacing: 0.05em;
    box-shadow: 0 4px 20px rgba(30,111,255,0.4);
    transition: box-shadow 0.2s, transform 0.1s;
}
:deep(button[type="submit"]:hover) {
    box-shadow: 0 4px 30px rgba(30,111,255,0.65);
    transform: translateY(-1px);
}
:deep(button[type="submit"]:active) {
    transform: translateY(0);
}
:deep(a.text-sm) {
    color: #1E6FFF !important;
}
:deep(.text-muted-foreground) {
    color: rgba(255,255,255,0.45) !important;
}
/* Checkbox */
:deep([data-state="checked"]) {
    background: #1E6FFF !important;
    border-color: #1E6FFF !important;
}
</style>
