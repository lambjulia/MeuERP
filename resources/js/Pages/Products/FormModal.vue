<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import InputSwitch from 'primevue/inputswitch';

const props = defineProps({
    visible: Boolean,
    product: { type: Object, default: null },
    categories: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:visible']);

const form = useForm({
    name: '',
    category_id: null,
    sku: '',
    barcode: '',
    description: '',
    cost_price: 0,
    sale_price: 0,
    minimum_stock: 0,
    active: true,
});

watch(() => props.visible, (val) => {
    if (val) {
        if (props.product) {
            form.name = props.product.name;
            form.category_id = props.product.category_id ?? null;
            form.sku = props.product.sku ?? '';
            form.barcode = props.product.barcode ?? '';
            form.description = props.product.description ?? '';
            form.cost_price = props.product.cost_price ?? 0;
            form.sale_price = props.product.sale_price ?? 0;
            form.minimum_stock = props.product.minimum_stock ?? 0;
            form.active = props.product.active ?? true;
        } else {
            form.reset();
            form.active = true;
            form.cost_price = 0;
            form.sale_price = 0;
            form.minimum_stock = 0;
        }
    }
});

function close() {
    emit('update:visible', false);
}

function submit() {
    if (props.product) {
        form.put(route('products.update', props.product.id), { onSuccess: () => close() });
    } else {
        form.post(route('products.store'), { onSuccess: () => close() });
    }
}
</script>

<template>
    <Dialog :visible="visible" @update:visible="$emit('update:visible', $event)"
        :header="product ? 'Editar Produto' : 'Novo Produto'"
        :style="{ width: '560px' }" modal :draggable="false">
        <form @submit.prevent="submit" class="flex flex-col gap-4 mt-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
                <InputText v-model="form.name" class="w-full" :class="{ 'p-invalid': form.errors.name }" />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                <Select v-model="form.category_id" :options="categories" optionLabel="name" optionValue="id"
                    placeholder="Selecione..." class="w-full" showClear />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                    <InputText v-model="form.sku" class="w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código de Barras</label>
                    <InputText v-model="form.barcode" class="w-full" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <Textarea v-model="form.description" class="w-full" rows="2" autoResize />
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preço de Custo <span class="text-red-500">*</span></label>
                    <InputNumber v-model="form.cost_price" mode="currency" currency="BRL" locale="pt-BR" class="w-full"
                        :class="{ 'p-invalid': form.errors.cost_price }" />
                    <p v-if="form.errors.cost_price" class="mt-1 text-sm text-red-600">{{ form.errors.cost_price }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preço de Venda <span class="text-red-500">*</span></label>
                    <InputNumber v-model="form.sale_price" mode="currency" currency="BRL" locale="pt-BR" class="w-full"
                        :class="{ 'p-invalid': form.errors.sale_price }" />
                    <p v-if="form.errors.sale_price" class="mt-1 text-sm text-red-600">{{ form.errors.sale_price }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estoque Mínimo</label>
                    <InputNumber v-model="form.minimum_stock" :min="0" class="w-full" />
                </div>
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
                    {{ product ? 'Salvar' : 'Criar' }}
                </button>
            </div>
        </template>
    </Dialog>
</template>
