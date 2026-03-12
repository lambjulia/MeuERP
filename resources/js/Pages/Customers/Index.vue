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

const props = defineProps({ customers: Object, filters: Object });
const search = ref(props.filters?.search || '');
const deleteId = ref(null);
const modalVisible = ref(false);
const editingCustomer = ref(null);

function applyFilters() {
    router.get(route('customers.index'), { search: search.value }, { preserveState: true });
}

function openCreate() {
    editingCustomer.value = null;
    modalVisible.value = true;
}

function openEdit(customer) {
    editingCustomer.value = customer;
    modalVisible.value = true;
}

function confirmDelete(id) { deleteId.value = id; }

function destroy() {
    router.delete(route('customers.destroy', deleteId.value), {
        onFinish: () => (deleteId.value = null),
    });
}
</script>

<template>
    <ErpLayout title="Clientes">
        <template #header><h2 class="text-lg font-semibold text-gray-800">Clientes</h2></template>

        <BasePageHeader title="Clientes" description="Gerencie seus clientes" create-label="Novo Cliente" @new="openCreate" />
        <BaseFilterPanel v-model:search="search" @search="applyFilters" />

        <div class="bg-white rounded-xl border border-gray-200">
            <DataTable :value="customers.data" stripedRows v-if="customers.data.length > 0">
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
            <BaseEmptyState v-else title="Nenhum cliente" description="Cadastre seu primeiro cliente." />
        </div>

        <FormModal v-model:visible="modalVisible" :customer="editingCustomer" />

        <BaseConfirmDialog :visible="deleteId !== null" title="Excluir cliente" message="Tem certeza que deseja excluir este cliente?" @confirm="destroy" @cancel="deleteId = null" />
    </ErpLayout>
</template>
