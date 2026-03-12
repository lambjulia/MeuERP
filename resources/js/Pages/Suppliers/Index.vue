<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import ErpLayout from '@/Layouts/ErpLayout.vue';
import BasePageHeader from '@/Components/BasePageHeader.vue';
import BaseFilterPanel from '@/Components/BaseFilterPanel.vue';
import BaseEmptyState from '@/Components/BaseEmptyState.vue';
import BaseConfirmDialog from '@/Components/BaseConfirmDialog.vue';
import FormModal from './FormModal.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Button from 'primevue/button';

const props = defineProps({ suppliers: Object, filters: Object });
const search = ref(props.filters?.search || '');
const deleteId = ref(null);
const modalVisible = ref(false);
const editingSupplier = ref(null);

function applyFilters() {
    router.get(route('suppliers.index'), { search: search.value }, { preserveState: true });
}

function openCreate() {
    editingSupplier.value = null;
    modalVisible.value = true;
}

function openEdit(supplier) {
    editingSupplier.value = supplier;
    modalVisible.value = true;
}

function confirmDelete(id) { deleteId.value = id; }
function destroy() {
    router.delete(route('suppliers.destroy', deleteId.value), { onFinish: () => (deleteId.value = null) });
}
</script>

<template>
    <ErpLayout title="Fornecedores">
        <template #header><h2 class="text-lg font-semibold text-gray-800">Fornecedores</h2></template>
        <BasePageHeader title="Fornecedores" description="Gerencie seus fornecedores" create-label="Novo Fornecedor" @new="openCreate" />
        <BaseFilterPanel v-model:search="search" @search="applyFilters" />
        <div class="bg-white rounded-xl border border-gray-200">
            <DataTable :value="suppliers.data" stripedRows v-if="suppliers.data.length > 0">
                <Column field="name" header="Nome" />
                <Column field="document" header="Documento" />
                <Column field="email" header="E-mail" />
                <Column field="phone" header="Telefone" />
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
            <BaseEmptyState v-else title="Nenhum fornecedor" description="Cadastre seu primeiro fornecedor." />
        </div>
        <FormModal v-model:visible="modalVisible" :supplier="editingSupplier" />
        <BaseConfirmDialog :visible="deleteId !== null" title="Excluir fornecedor" message="Tem certeza que deseja excluir este fornecedor?" @confirm="destroy" @cancel="deleteId = null" />
    </ErpLayout>
</template>
