<script setup>
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';

const props = defineProps({
    visible: { type: Boolean, default: false },
    title: { type: String, default: 'Confirmar ação' },
    message: { type: String, default: 'Tem certeza que deseja continuar?' },
    confirmLabel: { type: String, default: 'Confirmar' },
    cancelLabel: { type: String, default: 'Cancelar' },
    severity: { type: String, default: 'danger' },
});

const emit = defineEmits(['confirm', 'cancel']);
</script>

<template>
    <Dialog
        :visible="visible"
        @update:visible="emit('cancel')"
        :header="title"
        modal
        :style="{ width: '400px' }"
    >
        <p class="text-gray-600">{{ message }}</p>
        <template #footer>
            <Button :label="cancelLabel" severity="secondary" text @click="emit('cancel')" />
            <Button :label="confirmLabel" :severity="severity" @click="emit('confirm')" />
        </template>
    </Dialog>
</template>
