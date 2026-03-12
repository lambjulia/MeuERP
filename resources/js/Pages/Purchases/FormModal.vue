<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch, computed } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import Button from 'primevue/button';

const props = defineProps({
    visible: Boolean,
    purchase: { type: Object, default: null },
    suppliers: { type: Array, default: () => [] },
    products: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:visible']);

const form = useForm({
    supplier_id: null,
    number: '',
    issue_date: '',
    notes: '',
    items: [],
});

watch(() => props.visible, (val) => {
    if (val) {
        if (props.purchase) {
            form.supplier_id = props.purchase.supplier_id;
            form.number = props.purchase.number ?? '';
            form.issue_date = props.purchase.issue_date ?? '';
            form.notes = props.purchase.notes ?? '';
            form.items = (props.purchase.items ?? []).map(item => ({
                product_id: item.product_id,
                quantity: item.quantity,
                unit_price: item.unit_price,
            }));
        } else {
            form.reset();
            form.items = [];
            form.issue_date = new Date().toISOString().slice(0, 10);
        }
    }
});

function addItem() {
    form.items.push({ product_id: null, quantity: 1, unit_price: 0 });
}

function removeItem(idx) {
    form.items.splice(idx, 1);
}

function onProductChange(idx) {
    const item = form.items[idx];
    const product = props.products.find(p => p.id === item.product_id);
    if (product) {
        item.unit_price = product.cost_price ?? 0;
    }
}

const total = computed(() =>
    form.items.reduce((sum, item) => sum + (item.quantity || 0) * (item.unit_price || 0), 0)
);

function close() {
    emit('update:visible', false);
}

function submit() {
    if (props.purchase) {
        form.put(route('purchases.update', props.purchase.id), { onSuccess: () => close() });
    } else {
        form.post(route('purchases.store'), { onSuccess: () => close() });
    }
}
</script>

<template>
    <Dialog :visible="visible" @update:visible="$emit('update:visible', $event)"
        :header="purchase ? 'Editar Compra' : 'Nova Compra'"
        :style="{ width: '700px' }" modal :draggable="false">
        <form @submit.prevent="submit" class="flex flex-col gap-4 mt-2">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fornecedor <span class="text-red-500">*</span></label>
                    <Select v-model="form.supplier_id" :options="suppliers" optionLabel="name" optionValue="id"
                        placeholder="Selecione..." class="w-full" :class="{ 'p-invalid': form.errors.supplier_id }" />
                    <p v-if="form.errors.supplier_id" class="mt-1 text-sm text-red-600">{{ form.errors.supplier_id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                    <InputText v-model="form.number" class="w-full" placeholder="Ex: NF-001" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data <span class="text-red-500">*</span></label>
                <InputText v-model="form.issue_date" type="date" class="w-full" :class="{ 'p-invalid': form.errors.issue_date }" />
                <p v-if="form.errors.issue_date" class="mt-1 text-sm text-red-600">{{ form.errors.issue_date }}</p>
            </div>

            <!-- Items -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-medium text-gray-700">Itens <span class="text-red-500">*</span></label>
                    <button type="button" @click="addItem"
                        class="flex items-center gap-1 text-xs text-primary hover:text-primary/80 font-medium">
                        <i class="pi pi-plus text-xs"></i> Adicionar Item
                    </button>
                </div>
                <div v-if="form.errors['items']" class="mb-2 text-sm text-red-600">{{ form.errors['items'] }}</div>
                <div class="rounded-lg border border-gray-200 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Produto</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 w-28">Qtd</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 w-32">Preço Unit.</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 w-28">Total</th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, idx) in form.items" :key="idx" class="border-t border-gray-100">
                                <td class="px-3 py-2">
                                    <Select v-model="item.product_id" :options="products" optionLabel="name" optionValue="id"
                                        placeholder="Produto..." class="w-full" @change="onProductChange(idx)" />
                                </td>
                                <td class="px-3 py-2">
                                    <InputNumber v-model="item.quantity" :min="0.01" :minFractionDigits="0" :maxFractionDigits="2"
                                        class="w-full" inputClass="text-right" />
                                </td>
                                <td class="px-3 py-2">
                                    <InputNumber v-model="item.unit_price" mode="currency" currency="BRL" locale="pt-BR"
                                        class="w-full" inputClass="text-right" />
                                </td>
                                <td class="px-3 py-2 text-right font-medium text-gray-700">
                                    {{ ((item.quantity || 0) * (item.unit_price || 0)).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}
                                </td>
                                <td class="px-3 py-2">
                                    <button type="button" @click="removeItem(idx)"
                                        class="text-red-400 hover:text-red-600">
                                        <i class="pi pi-times text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="form.items.length === 0">
                                <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-400">Nenhum item adicionado</td>
                            </tr>
                        </tbody>
                        <tfoot v-if="form.items.length > 0" class="border-t border-gray-200 bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-3 py-2 text-right text-sm font-semibold text-gray-700">Total:</td>
                                <td class="px-3 py-2 text-right text-sm font-bold text-gray-900">
                                    {{ total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
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
                    class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:brightness-90 disabled:opacity-50">
                    {{ purchase ? 'Salvar' : 'Criar' }}
                </button>
            </div>
        </template>
    </Dialog>
</template>
