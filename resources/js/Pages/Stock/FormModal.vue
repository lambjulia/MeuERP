<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';

const props = defineProps({
    visible: Boolean,
    products: { type: Array, default: () => [] },
    types: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:visible']);

const form = useForm({
    product_id: null,
    type: null,
    quantity: null,
    notes: '',
});

watch(() => props.visible, (val) => {
    if (val) {
        form.reset();
    }
});

function close() {
    emit('update:visible', false);
}

function submit() {
    form.post(route('stock.store'), { onSuccess: () => close() });
}
</script>

<template>
    <Dialog :visible="visible" @update:visible="$emit('update:visible', $event)"
        header="Novo Ajuste de Estoque"
        :style="{ width: '460px' }" modal :draggable="false">
        <form @submit.prevent="submit" class="flex flex-col gap-4 mt-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Produto <span class="text-red-500">*</span></label>
                <Select v-model="form.product_id" :options="products" optionLabel="name" optionValue="id"
                    placeholder="Selecione um produto..." class="w-full" :class="{ 'p-invalid': form.errors.product_id }" />
                <p v-if="form.errors.product_id" class="mt-1 text-sm text-red-600">{{ form.errors.product_id }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                <Select v-model="form.type" :options="types" optionLabel="label" optionValue="value"
                    placeholder="Selecione o tipo..." class="w-full" :class="{ 'p-invalid': form.errors.type }" />
                <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade <span class="text-red-500">*</span></label>
                <InputNumber v-model="form.quantity" :min="0.01" :minFractionDigits="0" :maxFractionDigits="2" class="w-full"
                    :class="{ 'p-invalid': form.errors.quantity }" />
                <p v-if="form.errors.quantity" class="mt-1 text-sm text-red-600">{{ form.errors.quantity }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <Textarea v-model="form.notes" class="w-full" rows="2" autoResize />
            </div>
        </form>
        <template #footer>
            <div class="flex justify-end gap-2">
                <button type="button" @click="close"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="button" @click="submit" :disabled="form.processing"
                    class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:brightness-90 disabled:opacity-50">
                    Registrar
                </button>
            </div>
        </template>
    </Dialog>
</template>
