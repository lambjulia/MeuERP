<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    cancelRoute: { type: String, required: true },
    loading: { type: Boolean, default: false },
    submitLabel: { type: String, default: 'Salvar' },
});
</script>

<template>
    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
        <Link
            :href="cancelRoute"
            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
        >
            Cancelar
        </Link>
        <button
            type="submit"
            :disabled="loading"
            class="px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:brightness-90 disabled:opacity-50 transition-colors"
        >
            <i v-if="loading" class="pi pi-spinner pi-spin mr-1"></i>
            {{ submitLabel }}
        </button>
    </div>
</template>
