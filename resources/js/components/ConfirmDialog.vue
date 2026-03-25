<script setup lang="ts">
import { Loader2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

withDefaults(defineProps<{
    title?: string;
    description?: string;
    confirmLabel?: string;
    cancelLabel?: string;
    variant?: 'destructive' | 'default';
}>(), {
    title: '¿Estás seguro?',
    description: 'Esta acción no se puede deshacer.',
    confirmLabel: 'Confirmar',
    cancelLabel: 'Cancelar',
    variant: 'destructive',
});

const emit = defineEmits<{
    confirm: [];
}>();

const open = ref(false);
const loading = ref(false);

function show() {
    open.value = true;
}

function onConfirm() {
    loading.value = true;
    emit('confirm');
    open.value = false;
    loading.value = false;
}

defineExpose({ show });
</script>

<template>
    <Dialog v-model:open="open">
        <slot name="trigger" :show="show" />
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
                <DialogDescription>{{ description }}</DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2 sm:gap-0">
                <Button variant="outline" @click="open = false">{{ cancelLabel }}</Button>
                <Button :variant="variant" :disabled="loading" @click="onConfirm">
                    <Loader2 v-if="loading" class="mr-2 h-4 w-4 animate-spin" />
                    {{ confirmLabel }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
