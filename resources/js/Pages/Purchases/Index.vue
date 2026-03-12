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
import { PurchaseStatus } from '@/Enums';

const props = defineProps({ purchases: Object, filters: Object, suppliers: Array, products: Array });
const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');

const confirmDelete = ref(false);
const deleteId = ref(null);
const modalVisible = ref(false);
const editingPurchase = ref(null);

function applyFilters() {
    router.get(route('purchases.index'), { search: search.value, status: status.value }, { preserveState: true, replace: true });
}

watch([search, status], () => applyFilters());

function openCreate() {
    editingPurchase.value = null;
    modalVisible.value = true;
}

function openEdit(purchase) {
    editingPurchase.value = purchase;
    modalVisible.value = true;
}

function confirmDeletePurchase(id) { deleteId.value = id; confirmDelete.value = true; }

function deletePurchase() {
    router.delete(route('purchases.destroy', deleteId.value));
    confirmDelete.value = false;
}

function confirmPurchase(id) { router.post(route('purchases.confirm', id)); }
function cancelPurchase(id) { router.post(route('purchases.cancel', id)); }
</script>

<template>
    <ErpLayout title="Compras">
        <template #header><h2 class="text-lg font-semibold text-gray-800">Compras</h2></template>

        <BasePageHeader title="Compras" create-label="Nova Compra" @new="openCreate" />

        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-4 border-b border-gray-100 flex flex-wrap gap-3 items-center">
                <input v-model="search" type="text" placeholder="Buscar por número..." class="w-72 rounded-lg border-gray-300 text-sm" />
                <select v-model="status" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Todos os Status</option>
                    <option v-for="(opt, key) in PurchaseStatus" :key="key" :value="key">{{ opt.label }}</option>
                </select>
            </div>

            <DataTable :value="purchases.data" stripedRows>
                <Column field="number" header="Número" />
                <Column field="supplier.name" header="Fornecedor" />
                <Column field="issue_date" header="Data"><template #body="{ data }"><BaseDate :value="data.issue_date" /></template></Column>
                <Column field="status" header="Status">
                    <template #body="{ data }"><BaseStatusBadge :value="PurchaseStatus[data.status]?.label" :severity="PurchaseStatus[data.status]?.color" /></template>
                </Column>
                <Column field="total" header="Total" class="text-right"><template #body="{ data }"><BaseMoney :value="data.total" /></template></Column>
                <Column header="Ações" style="width: 180px">
                    <template #body="{ data }">
                        <div class="flex gap-1 flex-wrap">
                            <Button v-if="data.status === 'draft'" icon="pi pi-pencil" severity="info" text rounded size="small" @click="openEdit(data)" />
                            <Button v-if="data.status === 'draft'" icon="pi pi-check" severity="success" text rounded size="small" title="Confirmar" @click="confirmPurchase(data.id)" />
                            <Button v-if="data.status === 'draft'" icon="pi pi-times" severity="warning" text rounded size="small" title="Cancelar" @click="cancelPurchase(data.id)" />
                            <Button v-if="data.status === 'draft'" icon="pi pi-trash" severity="danger" text rounded size="small" @click="confirmDeletePurchase(data.id)" />
                        </div>
                    </template>
                </Column>
            </DataTable>

            <div v-if="purchases.links" class="p-4 border-t border-gray-100 flex justify-center gap-1">
                <template v-for="link in purchases.links" :key="link.label">
                    <a v-if="link.url" :href="link.url" v-html="link.label"
                       class="px-3 py-1.5 text-sm rounded-lg" :class="link.active ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'" />
                    <span v-else v-html="link.label" class="px-3 py-1.5 text-sm text-gray-400" />
                </template>
            </div>
        </div>

        <FormModal v-model:visible="modalVisible" :purchase="editingPurchase" :suppliers="suppliers" :products="products" />
        <BaseConfirmDialog v-model:visible="confirmDelete" title="Excluir Compra" message="Tem certeza que deseja excluir esta compra?" @confirm="deletePurchase" />
    </ErpLayout>
</template>
