<script setup>
const props = defineProps({
    value: { type: String, default: null },
});

const formatted = props.value
    ? new Date(props.value).toLocaleDateString('pt-BR')
    : '—';
</script>

<template>
    <span>{{ formatted }}</span>
</template>
