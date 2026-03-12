<script setup>
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
import { ref } from 'vue';

const props = defineProps({
    companies: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const deleteId = ref(null);
const modalVisible = ref(false);
const editingCompany = ref(null);

function applyFilters() {
    router.get(route('companies.index'), { search: search.value }, { preserveState: true });
}

function openCreate() {
    editingCompany.value = null;
    modalVisible.value = true;
}

function openEdit(company) {
    editingCompany.value = company;
    modalVisible.value = true;
}

function confirmDelete(id) {
    deleteId.value = id;
}

function deleteCompany() {
    router.delete(route('companies.destroy', deleteId.value), {
        onFinish: () => (deleteId.value = null),
    });
}
</script>

<template>
    <ErpLayout title="Empresas">
        <template #header>
            <h2 class="text-lg font-semibold text-gray-800">Empresas</h2>
        </template>

        <BasePageHeader
            title="Empresas"
            description="Gerencie as empresas do sistema"
            create-label="Nova Empresa"
            @new="openCreate"
        />

        <BaseFilterPanel v-model:search="search" @search="applyFilters" />

        <div class="bg-white rounded-xl border border-gray-200">
            <DataTable :value="companies.data" :rows="15" stripedRows v-if="companies.data.length > 0">
                <Column field="name" header="Nome" />
                <Column field="document" header="Documento" />
                <Column field="email" header="E-mail" />
                <Column field="phone" header="Telefone" />
                <Column header="Status">
                    <template #body="{ data }">
                        <Tag :value="data.active ? 'Ativa' : 'Inativa'" :severity="data.active ? 'success' : 'danger'" rounded />
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
            <BaseEmptyState v-else title="Nenhuma empresa" description="Crie sua primeira empresa." />
        </div>

        <FormModal v-model:visible="modalVisible" :company="editingCompany" />

        <BaseConfirmDialog
            :visible="deleteId !== null"
            title="Excluir empresa"
            message="Tem certeza que deseja excluir esta empresa? Todos os dados vinculados serão removidos."
            @confirm="deleteCompany"
            @cancel="deleteId = null"
        />
    </ErpLayout>
</template>
