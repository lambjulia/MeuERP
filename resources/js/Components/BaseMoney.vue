<script setup>
const props = defineProps({
    value: { type: [Number, String], default: 0 },
});

const formatted = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
}).format(Number(props.value));
</script>

<template>
    <span>{{ formatted }}</span>
</template>
