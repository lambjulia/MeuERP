<script setup>
import ErpLayout from '@/Layouts/ErpLayout.vue';
import BasePageHeader from '@/Components/BasePageHeader.vue';
import BaseMoney from '@/Components/BaseMoney.vue';
import BaseDate from '@/Components/BaseDate.vue';
import BaseStatusBadge from '@/Components/BaseStatusBadge.vue';
import BaseConfirmDialog from '@/Components/BaseConfirmDialog.vue';
import FormModal from './FormModal.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { SaleStatus } from '@/Enums';

const props = defineProps({ sales: Object, filters: Object, customers: Array, products: Array });
const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');

const confirmDelete = ref(false);
const deleteId = ref(null);
const modalVisible = ref(false);
const editingSale = ref(null);

function applyFilters() {
    router.get(route('sales.index'), { search: search.value, status: status.value }, { preserveState: true, replace: true });
}

watch([search, status], () => applyFilters());

function openCreate() {
    editingSale.value = null;
    modalVisible.value = true;
}

function openEdit(sale) {
    editingSale.value = sale;
    modalVisible.value = true;
}

function confirmDeleteSale(id) { deleteId.value = id; confirmDelete.value = true; }
function deleteSale() { router.delete(route('sales.destroy', deleteId.value)); confirmDelete.value = false; }
function confirmSale(id) { router.post(route('sales.confirm', id)); }
function cancelSale(id) { router.post(route('sales.cancel', id)); }
</script>

<template>
    <ErpLayout title="Vendas">
        <template #header><h2 class="text-lg font-semibold text-gray-800">Vendas</h2></template>

        <BasePageHeader title="Vendas" create-label="Nova Venda" @new="openCreate" />

        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-4 border-b border-gray-100 flex flex-wrap gap-3 items-center">
                <input v-model="search" type="text" placeholder="Buscar por número..." class="w-72 rounded-lg border-gray-300 text-sm" />
                <select v-model="status" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Todos os Status</option>
                    <option v-for="(opt, key) in SaleStatus" :key="key" :value="key">{{ opt.label }}</option>
                </select>
            </div>

            <DataTable :value="sales.data" stripedRows>
                <Column field="number" header="Número" />
                <Column field="customer.name" header="Cliente" />
                <Column field="issue_date" header="Data"><template #body="{ data }"><BaseDate :value="data.issue_date" /></template></Column>
                <Column field="status" header="Status">
                    <template #body="{ data }"><BaseStatusBadge :value="SaleStatus[data.status]?.label" :severity="SaleStatus[data.status]?.color" /></template>
                </Column>
                <Column field="total" header="Total" class="text-right"><template #body="{ data }"><BaseMoney :value="data.total" /></template></Column>
                <Column header="Ações" style="width: 180px">
                    <template #body="{ data }">
                        <div class="flex gap-1 flex-wrap">
                            <Button v-if="data.status === 'draft'" icon="pi pi-pencil" severity="info" text rounded size="small" @click="openEdit(data)" />
                            <Button v-if="data.status === 'draft'" icon="pi pi-check" severity="success" text rounded size="small" title="Confirmar" @click="confirmSale(data.id)" />
                            <Button v-if="data.status === 'draft'" icon="pi pi-times" severity="warning" text rounded size="small" title="Cancelar" @click="cancelSale(data.id)" />
                            <Button v-if="data.status === 'draft'" icon="pi pi-trash" severity="danger" text rounded size="small" @click="confirmDeleteSale(data.id)" />
                        </div>
                    </template>
                </Column>
            </DataTable>

            <div v-if="sales.links" class="p-4 border-t border-gray-100 flex justify-center gap-1">
                <template v-for="link in sales.links" :key="link.label">
                    <a v-if="link.url" :href="link.url" v-html="link.label"
                       class="px-3 py-1.5 text-sm rounded-lg" :class="link.active ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100'" />
                    <span v-else v-html="link.label" class="px-3 py-1.5 text-sm text-gray-400" />
                </template>
            </div>
        </div>

        <FormModal v-model:visible="modalVisible" :sale="editingSale" :customers="customers" :products="products" />
        <BaseConfirmDialog v-model:visible="confirmDelete" title="Excluir Venda" message="Tem certeza que deseja excluir esta venda?" @confirm="deleteSale" />
    </ErpLayout>
</template>
