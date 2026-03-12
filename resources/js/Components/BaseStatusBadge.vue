<script setup>
import Tag from 'primevue/tag';

defineProps({
    label: { type: String, required: true },
    severity: { type: String, default: 'info' },
});
</script>

<template>
    <Tag :value="label" :severity="severity" rounded />
</template>
