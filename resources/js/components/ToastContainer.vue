<script setup lang="ts">
import { X } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';

const { toasts, dismiss } = useToast();

const variantClasses: Record<string, string> = {
    success: 'border-green-200 bg-green-50 text-green-800',
    error: 'border-red-200   bg-red-50   text-red-800',
    warning: 'border-amber-200 bg-amber-50 text-amber-800',
    info: 'border-blue-200  bg-blue-50  text-blue-800',
};

const iconClasses: Record<string, string> = {
    success: 'text-green-500',
    error: 'text-red-500',
    warning: 'text-amber-500',
    info: 'text-blue-500',
};

const icons: Record<string, string> = {
    success: '✓',
    error: '✕',
    warning: '⚠',
    info: 'ℹ',
};
</script>

<template>
    <Teleport to="body">
        <div
            class="pointer-events-none fixed right-5 bottom-5 z-50 flex flex-col gap-2"
            aria-live="polite"
        >
            <TransitionGroup name="toast" tag="div" class="flex flex-col gap-2">
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    :class="[
                        'pointer-events-auto flex max-w-sm min-w-72 items-start gap-3 rounded-xl border px-4 py-3 shadow-lg',
                        variantClasses[toast.variant],
                    ]"
                >
                    <span
                        :class="[
                            'mt-0.5 text-base leading-none font-bold',
                            iconClasses[toast.variant],
                        ]"
                    >
                        {{ icons[toast.variant] }}
                    </span>
                    <span class="flex-1 text-sm leading-snug font-medium">{{
                        toast.message
                    }}</span>
                    <button
                        @click="dismiss(toast.id)"
                        class="mt-0.5 shrink-0 opacity-50 hover:opacity-100"
                    >
                        <X class="h-3.5 w-3.5" />
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.25s ease;
}
.toast-enter-from {
    opacity: 0;
    transform: translateX(1.5rem);
}
.toast-leave-to {
    opacity: 0;
    transform: translateX(1.5rem);
}
</style>
