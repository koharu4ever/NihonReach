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
        viewBox="0 0 50 42"
        :class="className"
        v-bind="$attrs"
    >
        <path
            fill="currentColor"
            d="M3 34V8h5l9 16V8h5v26h-5L8 18v16H3Zm23 0V8h10c6 0 9 3 9 8 0 4-2 6-5 7l7 11h-6l-6-10h-4v10h-5Zm5-15h5c3 0 4-1 4-3s-1-3-4-3h-5v6Z"
        />
    </svg>
</template>
