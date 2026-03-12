<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import InputSwitch from 'primevue/inputswitch';

const props = defineProps({
    visible: Boolean,
    category: { type: Object, default: null },
});

const emit = defineEmits(['update:visible']);

const form = useForm({
    name: '',
    description: '',
    active: true,
});

watch(() => props.visible, (val) => {
    if (val) {
        if (props.category) {
            form.name = props.category.name;
            form.description = props.category.description ?? '';
            form.active = props.category.active ?? true;
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
    if (props.category) {
        form.put(route('categories.update', props.category.id), { onSuccess: () => close() });
    } else {
        form.post(route('categories.store'), { onSuccess: () => close() });
    }
}
</script>

<template>
    <Dialog :visible="visible" @update:visible="$emit('update:visible', $event)"
        :header="category ? 'Editar Categoria' : 'Nova Categoria'"
        :style="{ width: '440px' }" modal :draggable="false">
        <form @submit.prevent="submit" class="flex flex-col gap-4 mt-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
                <InputText v-model="form.name" class="w-full" :class="{ 'p-invalid': form.errors.name }" />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <Textarea v-model="form.description" class="w-full" rows="3" autoResize />
            </div>
            <div class="flex items-center gap-3">
                <InputSwitch v-model="form.active" inputId="active" />
                <label for="active" class="text-sm font-medium text-gray-700">Ativa</label>
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
                    {{ category ? 'Salvar' : 'Criar' }}
                </button>
            </div>
        </template>
    </Dialog>
</template>
