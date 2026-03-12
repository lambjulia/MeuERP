<script setup>
import InputText from 'primevue/inputtext';

const emit = defineEmits(['search']);

const model = defineModel('search', { type: String, default: '' });
</script>

<template>
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <InputText
                    v-model="model"
                    placeholder="Pesquisar..."
                    class="w-full"
                    @keyup.enter="emit('search')"
                />
            </div>
            <slot />
            <button
                @click="emit('search')"
                class="px-4 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors"
            >
                <i class="pi pi-search mr-1"></i>
                Filtrar
            </button>
        </div>
    </div>
</template>
