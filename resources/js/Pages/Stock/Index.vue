<script setup>
import ErpLayout from '@/Layouts/ErpLayout.vue';
import BasePageHeader from '@/Components/BasePageHeader.vue';
import BaseFilterPanel from '@/Components/BaseFilterPanel.vue';
import BaseDate from '@/Components/BaseDate.vue';
import FormModal from './FormModal.vue';
import Tag from 'primevue/tag';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { StockMovementType } from '@/Enums';

const props = defineProps({ movements: Object, filters: Object, products: Array, types: Array });
const search = ref(props.filters?.search || '');
const type = ref(props.filters?.type || '');
const modalVisible = ref(false);

function applyFilters() {
    router.get(route('stock.index'), { search: search.value, type: type.value }, { preserveState: true, replace: true });
}

watch([search, type], () => applyFilters());
</script>

<template>
    <ErpLayout title="Movimentações de Estoque">
        <template #header><h2 class="text-lg font-semibold text-gray-800">Movimentações de Estoque</h2></template>

        <BasePageHeader title="Movimentações de Estoque" create-label="Novo Ajuste" @new="modalVisible = true" />

        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-4 border-b border-gray-100 flex flex-wrap gap-3 items-center">
                <input v-model="search" type="text" placeholder="Buscar por produto..." class="w-72 rounded-lg border-gray-300 text-sm" />
                <select v-model="type" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Todos os Tipos</option>
                    <option v-for="(opt, key) in StockMovementType" :key="key" :value="key">{{ opt.label }}</option>
                </select>
            </div>

            <DataTable :value="movements.data" stripedRows>
                <Column field="created_at" header="Data">
                    <template #body="{ data }"><BaseDate :value="data.created_at" /></template>
                </Column>
                <Column field="product.name" header="Produto" />
                <Column field="type" header="Tipo">
                    <template #body="{ data }">
                        <Tag :value="StockMovementType[data.type]?.label || data.type" :severity="StockMovementType[data.type]?.color || 'info'" rounded />
                    </template>
                </Column>
                <Column field="quantity" header="Quantidade" class="text-right">
                    <template #body="{ data }"><span class="font-mono">{{ data.quantity }}</span></template>
                </Column>
                <Column field="balance_after" header="Saldo Após" class="text-right">
                    <template #body="{ data }"><span class="font-mono">{{ data.balance_after }}</span></template>
                </Column>
                <Column field="notes" header="Observação">
                    <template #body="{ data }">{{ data.notes || '—' }}</template>
                </Column>
            </DataTable>

            <div v-if="movements.links" class="p-4 border-t border-gray-100 flex justify-center gap-1">
                <template v-for="link in movements.links" :key="link.label">
                    <a v-if="link.url" :href="link.url" v-html="link.label"
                       class="px-3 py-1.5 text-sm rounded-lg" :class="link.active ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100'" />
                    <span v-else v-html="link.label" class="px-3 py-1.5 text-sm text-gray-400" />
                </template>
            </div>
        </div>

        <FormModal v-model:visible="modalVisible" :products="products" :types="types" />
    </ErpLayout>
</template>
