<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';

const props = defineProps({
    visible: Boolean,
    account: { type: Object, default: null },
    suppliers: { type: Array, default: () => [] },
    statuses: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:visible']);

const form = useForm({
    supplier_id: null,
    description: '',
    amount: null,
    due_date: '',
    notes: '',
});

watch(() => props.visible, (val) => {
    if (val) {
        if (props.account) {
            form.supplier_id = props.account.supplier_id ?? null;
            form.description = props.account.description ?? '';
            form.amount = props.account.amount ?? null;
            form.due_date = props.account.due_date ?? '';
            form.notes = props.account.notes ?? '';
        } else {
            form.reset();
        }
    }
});

function close() {
    emit('update:visible', false);
}

function submit() {
    if (props.account) {
        form.put(route('accounts-payable.update', props.account.id), { onSuccess: () => close() });
    } else {
        form.post(route('accounts-payable.store'), { onSuccess: () => close() });
    }
}
</script>

<template>
    <Dialog :visible="visible" @update:visible="$emit('update:visible', $event)"
        :header="account ? 'Editar Conta a Pagar' : 'Nova Conta a Pagar'"
        :style="{ width: '500px' }" modal :draggable="false">
        <form @submit.prevent="submit" class="flex flex-col gap-4 mt-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fornecedor</label>
                <Select v-model="form.supplier_id" :options="suppliers" optionLabel="name" optionValue="id"
                    placeholder="Selecione..." class="w-full" showClear />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição <span class="text-red-500">*</span></label>
                <InputText v-model="form.description" class="w-full" :class="{ 'p-invalid': form.errors.description }" />
                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valor <span class="text-red-500">*</span></label>
                    <InputNumber v-model="form.amount" mode="currency" currency="BRL" locale="pt-BR" class="w-full"
                        :class="{ 'p-invalid': form.errors.amount }" />
                    <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">{{ form.errors.amount }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vencimento <span class="text-red-500">*</span></label>
                    <InputText v-model="form.due_date" type="date" class="w-full" :class="{ 'p-invalid': form.errors.due_date }" />
                    <p v-if="form.errors.due_date" class="mt-1 text-sm text-red-600">{{ form.errors.due_date }}</p>
                </div>
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
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">
                    {{ account ? 'Salvar' : 'Criar' }}
                </button>
            </div>
        </template>
    </Dialog>
</template>
