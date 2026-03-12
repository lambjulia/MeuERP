<script setup>
import ApiTokenManager from '@/Pages/API/Partials/ApiTokenManager.vue';
import ErpLayout from '@/Layouts/ErpLayout.vue';

defineProps({
    tokens: Array,
    availablePermissions: Array,
    defaultPermissions: Array,
});
</script>

<template>
    <ErpLayout title="Tokens de API">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tokens de API
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-6">
            <ApiTokenManager
                :tokens="tokens"
                :available-permissions="availablePermissions"
                :default-permissions="defaultPermissions"
            />
        </div>
    </ErpLayout>
</template>
