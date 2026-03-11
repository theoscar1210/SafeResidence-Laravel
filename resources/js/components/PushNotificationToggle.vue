<script setup lang="ts">
import { Bell, BellOff, Loader2 } from 'lucide-vue-next';
import { onMounted } from 'vue';
import { usePushNotifications } from '@/composables/usePushNotifications';

const { supported, subscribed, loading, checkSubscription, requestAndSubscribe, unsubscribe } = usePushNotifications();

onMounted(() => checkSubscription());

function toggle() {
    if (subscribed.value) unsubscribe();
    else requestAndSubscribe();
}
</script>

<template>
    <button v-if="supported" @click="toggle" :disabled="loading"
        :class="[
            'flex items-center gap-2 rounded-xl border px-4 py-3 text-sm font-semibold transition-all disabled:opacity-50',
            subscribed
                ? 'border-emerald-400/30 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-300 dark:hover:bg-emerald-500/20'
                : 'border-border bg-muted/40 text-muted-foreground hover:bg-muted',
        ]">
        <Loader2 v-if="loading" class="h-4 w-4 animate-spin" />
        <Bell v-else-if="!subscribed" class="h-4 w-4" />
        <BellOff v-else class="h-4 w-4" />
        <span>
            {{ loading ? 'Procesando...' : subscribed ? 'Notificaciones activas' : 'Activar notificaciones push' }}
        </span>
    </button>
</template>
