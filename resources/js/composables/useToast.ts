import { reactive } from 'vue';

export type ToastVariant = 'success' | 'error' | 'warning' | 'info';

export interface Toast {
    id: number;
    message: string;
    variant: ToastVariant;
}

const toasts = reactive<Toast[]>([]);
let nextId = 0;

export function useToast() {
    function show(message: string, variant: ToastVariant = 'info', duration = 3500) {
        const id = ++nextId;
        toasts.push({ id, message, variant });
        setTimeout(() => dismiss(id), duration);
    }

    function dismiss(id: number) {
        const idx = toasts.findIndex((t) => t.id === id);
        if (idx !== -1) toasts.splice(idx, 1);
    }

    return { toasts, show, dismiss };
}
