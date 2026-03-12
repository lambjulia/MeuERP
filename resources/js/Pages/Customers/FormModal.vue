<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import InputSwitch from 'primevue/inputswitch';

const props = defineProps({
    visible: Boolean,
    customer: { type: Object, default: null },
});

const emit = defineEmits(['update:visible']);

const form = useForm({
    name: '',
    document: '',
    email: '',
    phone: '',
    address: '',
    active: true,
});

watch(() => props.visible, (val) => {
    if (val) {
        if (props.customer) {
            form.name = props.customer.name;
            form.document = props.customer.document ?? '';
            form.email = props.customer.email ?? '';
            form.phone = props.customer.phone ?? '';
            form.address = props.customer.address ?? '';
            form.active = props.customer.active ?? true;
        } else {
            form.reset();
            form.active = true;
        }
    }
});

function close() {
    emit('update:visible', false);
}

function submit() {
    if (props.customer) {
        form.put(route('customers.update', props.customer.id), { onSuccess: () => close() });
    } else {
        form.post(route('customers.store'), { onSuccess: () => close() });
    }
}
</script>

<template>
    <Dialog :visible="visible" @update:visible="$emit('update:visible', $event)"
        :header="customer ? 'Editar Cliente' : 'Novo Cliente'"
        :style="{ width: '480px' }" modal :draggable="false">
        <form @submit.prevent="submit" class="flex flex-col gap-4 mt-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
                <InputText v-model="form.name" class="w-full" :class="{ 'p-invalid': form.errors.name }" />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CPF/CNPJ</label>
                <InputText v-model="form.document" class="w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                <InputText v-model="form.email" type="email" class="w-full" :class="{ 'p-invalid': form.errors.email }" />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                <InputText v-model="form.phone" class="w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                <InputText v-model="form.address" class="w-full" />
            </div>
            <div class="flex items-center gap-3">
                <InputSwitch v-model="form.active" inputId="active" />
                <label for="active" class="text-sm font-medium text-gray-700">Ativo</label>
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
                    {{ customer ? 'Salvar' : 'Criar' }}
                </button>
            </div>
        </template>
    </Dialog>
</template>
