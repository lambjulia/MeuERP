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
import { FinancialStatus } from '@/Enums';

const props = defineProps({ accounts: Object, filters: Object, suppliers: Array, statuses: Array });
const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');

const confirmDelete = ref(false);
const deleteId = ref(null);
const modalVisible = ref(false);
const editingAccount = ref(null);

function applyFilters() {
    router.get(route('accounts-payable.index'), { search: search.value, status: status.value }, { preserveState: true, replace: true });
}

watch([search, status], () => applyFilters());

function openCreate() {
    editingAccount.value = null;
    modalVisible.value = true;
}

function openEdit(account) {
    editingAccount.value = account;
    modalVisible.value = true;
}

function confirmDeleteAccount(id) { deleteId.value = id; confirmDelete.value = true; }
function deleteAccount() { router.delete(route('accounts-payable.destroy', deleteId.value)); confirmDelete.value = false; }
function markPaid(id) { router.post(route('accounts-payable.pay', id)); }
</script>

<template>
    <ErpLayout title="Contas a Pagar">
        <template #header><h2 class="text-lg font-semibold text-gray-800">Contas a Pagar</h2></template>

        <BasePageHeader title="Contas a Pagar" create-label="Nova Conta" @new="openCreate" />

        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-4 border-b border-gray-100 flex flex-wrap gap-3 items-center">
                <input v-model="search" type="text" placeholder="Buscar por descrição ou fornecedor..." class="w-72 rounded-lg border-gray-300 text-sm" />
                <select v-model="status" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Todos os Status</option>
                    <option v-for="(opt, key) in FinancialStatus" :key="key" :value="key">{{ opt.label }}</option>
                </select>
            </div>

            <DataTable :value="accounts.data" stripedRows>
                <Column field="description" header="Descrição" />
                <Column field="supplier.name" header="Fornecedor" />
                <Column field="amount" header="Valor" class="text-right"><template #body="{ data }"><BaseMoney :value="data.amount" /></template></Column>
                <Column field="due_date" header="Vencimento"><template #body="{ data }"><BaseDate :value="data.due_date" /></template></Column>
                <Column field="status" header="Status">
                    <template #body="{ data }"><BaseStatusBadge :value="FinancialStatus[data.status]?.label" :severity="FinancialStatus[data.status]?.color" /></template>
                </Column>
                <Column header="Ações" style="width: 160px">
                    <template #body="{ data }">
                        <div class="flex gap-1">
                            <button v-if="data.status === 'pending'" @click="markPaid(data.id)"
                                    class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200" title="Marcar como Pago">
                                <i class="pi pi-check"></i> Pagar
                            </button>
                            <Button icon="pi pi-pencil" severity="info" text rounded size="small" @click="openEdit(data)" />
                            <button @click="confirmDeleteAccount(data.id)" class="p-2 text-gray-400 hover:text-red-600"><i class="pi pi-trash"></i></button>
                        </div>
                    </template>
                </Column>
            </DataTable>

            <div v-if="accounts.links" class="p-4 border-t border-gray-100 flex justify-center gap-1">
                <template v-for="link in accounts.links" :key="link.label">
                    <a v-if="link.url" :href="link.url" v-html="link.label"
                       class="px-3 py-1.5 text-sm rounded-lg" :class="link.active ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'" />
                    <span v-else v-html="link.label" class="px-3 py-1.5 text-sm text-gray-400" />
                </template>
            </div>
        </div>

        <FormModal v-model:visible="modalVisible" :account="editingAccount" :suppliers="suppliers" :statuses="statuses" />
        <BaseConfirmDialog v-model:visible="confirmDelete" title="Excluir Conta" message="Tem certeza que deseja excluir esta conta a pagar?" @confirm="deleteAccount" />
    </ErpLayout>
</template>
