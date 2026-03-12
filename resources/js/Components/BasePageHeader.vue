<script setup>
defineProps({
    title: { type: String, required: true },
    description: { type: String, default: null },
    createLabel: { type: String, default: 'Novo' },
    showCreate: { type: Boolean, default: true },
});

defineEmits(['new']);
</script>

<template>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ title }}</h1>
            <p v-if="description" class="mt-1 text-sm text-gray-500">{{ description }}</p>
        </div>
        <div class="flex items-center gap-2">
            <slot name="actions" />
            <button
                v-if="showCreate"
                type="button"
                @click="$emit('new')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:brightness-90 transition-colors"
            >
                <i class="pi pi-plus text-sm"></i>
                {{ createLabel }}
            </button>
        </div>
    </div>
</template>
