<script setup>
defineProps({
    icon: { type: String, default: 'pi pi-inbox' },
    title: { type: String, default: 'Nenhum registro encontrado' },
    description: { type: String, default: 'Comece criando um novo registro.' },
});
</script>

<template>
    <div class="text-center py-12">
        <i :class="icon" class="text-4xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-600">{{ title }}</h3>
        <p class="text-sm text-gray-400 mt-1">{{ description }}</p>
        <div class="mt-4">
            <slot />
        </div>
    </div>
</template>
