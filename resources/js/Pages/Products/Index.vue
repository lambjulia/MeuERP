<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import ErpLayout from '@/Layouts/ErpLayout.vue';
import BasePageHeader from '@/Components/BasePageHeader.vue';
import BaseFilterPanel from '@/Components/BaseFilterPanel.vue';
import BaseEmptyState from '@/Components/BaseEmptyState.vue';
import BaseConfirmDialog from '@/Components/BaseConfirmDialog.vue';
import BaseMoney from '@/Components/BaseMoney.vue';
import FormModal from './FormModal.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Button from 'primevue/button';

const props = defineProps({ products: Object, filters: Object, categories: Array });
const search = ref(props.filters?.search || '');
const deleteId = ref(null);
const modalVisible = ref(false);
const editingProduct = ref(null);

function applyFilters() {
    router.get(route('products.index'), { search: search.value }, { preserveState: true });
}

function openCreate() {
    editingProduct.value = null;
    modalVisible.value = true;
}

function openEdit(product) {
    editingProduct.value = product;
    modalVisible.value = true;
}

function confirmDelete(id) { deleteId.value = id; }
function destroy() {
    router.delete(route('products.destroy', deleteId.value), { onFinish: () => (deleteId.value = null) });
}
</script>

<template>
    <ErpLayout title="Produtos">
        <template #header><h2 class="text-lg font-semibold text-gray-800">Produtos</h2></template>
        <BasePageHeader title="Produtos" description="Gerencie seu catálogo de produtos" create-label="Novo Produto" @new="openCreate" />
        <BaseFilterPanel v-model:search="search" @search="applyFilters" />

        <div class="bg-white rounded-xl border border-gray-200">
            <DataTable :value="products.data" stripedRows v-if="products.data.length > 0">
                <Column field="name" header="Nome" />
                <Column header="Categoria">
                    <template #body="{ data }">{{ data.category?.name || '—' }}</template>
                </Column>
                <Column field="sku" header="SKU" />
                <Column header="Preço Venda">
                    <template #body="{ data }"><BaseMoney :value="data.sale_price" /></template>
                </Column>
                <Column header="Estoque" style="width: 100px">
                    <template #body="{ data }">
                        <span :class="data.stock_quantity <= data.minimum_stock ? 'text-red-600 font-bold' : 'text-gray-900'">
                            {{ data.stock_quantity }}
                        </span>
                    </template>
                </Column>
                <Column header="Status">
                    <template #body="{ data }">
                        <Tag :value="data.active ? 'Ativo' : 'Inativo'" :severity="data.active ? 'success' : 'danger'" rounded />
                    </template>
                </Column>
                <Column header="Ações" style="width: 120px">
                    <template #body="{ data }">
                        <div class="flex gap-1">
                            <Button icon="pi pi-pencil" severity="info" text rounded size="small" @click="openEdit(data)" />
                            <Button icon="pi pi-trash" severity="danger" text rounded size="small" @click="confirmDelete(data.id)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
            <BaseEmptyState v-else title="Nenhum produto" description="Cadastre seu primeiro produto." />
        </div>

        <FormModal v-model:visible="modalVisible" :product="editingProduct" :categories="categories" />
        <BaseConfirmDialog :visible="deleteId !== null" title="Excluir produto" message="Tem certeza?" @confirm="destroy" @cancel="deleteId = null" />
    </ErpLayout>
</template>
