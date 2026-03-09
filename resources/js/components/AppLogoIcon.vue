<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 52 58"
        :class="className"
        v-bind="$attrs"
        fill="none"
    >
        <polygon points="26,2 49,14.5 49,43.5 26,56 3,43.5 3,14.5" fill="#1E6FFF" fill-opacity="0.15"/>
        <polygon points="26,2 49,14.5 49,43.5 26,56 3,43.5 3,14.5" fill="none" stroke="#1E6FFF" stroke-width="2"/>
        <path d="M18 30 C18 23.37 21.58 18 26 18 C30.42 18 34 23.37 34 30" stroke="#00D9B8" stroke-width="2.4" fill="none" stroke-linecap="round"/>
        <rect x="19" y="29" width="14" height="12" rx="2.5" fill="#1E6FFF"/>
        <circle cx="26" cy="35" r="2" fill="#00D9B8"/>
        <line x1="26" y1="35" x2="26" y2="38" stroke="#00D9B8" stroke-width="1.6" stroke-linecap="round"/>
    </svg>
</template>
