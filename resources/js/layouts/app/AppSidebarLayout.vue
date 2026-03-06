<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import ToastContainer from '@/components/ToastContainer.vue';
import { useToast } from '@/composables/useToast';
import { usePage } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { watch } from 'vue';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const { show } = useToast();
const page = usePage<{ flash?: Record<string, string> }>();

// Mostrar flash messages de Inertia como toasts en cada navegación
watch(
    () => page.props.flash,
    (flash) => {
        if (!flash) return;
        if (flash.success) show(flash.success, 'success');
        if (flash.error)   show(flash.error,   'error');
        if (flash.warning) show(flash.warning, 'warning');
        if (flash.info)    show(flash.info,    'info');
    },
    { deep: true },
);
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>
    <ToastContainer />
</template>
