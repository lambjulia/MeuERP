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

const props = defineProps({ categories: Object, filters: Object });
const search = ref(props.filters?.search || '');
const deleteId = ref(null);
const modalVisible = ref(false);
const editingCategory = ref(null);

function applyFilters() {
    router.get(route('categories.index'), { search: search.value }, { preserveState: true });
}

function openCreate() {
    editingCategory.value = null;
    modalVisible.value = true;
}

function openEdit(category) {
    editingCategory.value = category;
    modalVisible.value = true;
}

function confirmDelete(id) { deleteId.value = id; }
function destroy() {
    router.delete(route('categories.destroy', deleteId.value), { onFinish: () => (deleteId.value = null) });
}
</script>

<template>
    <ErpLayout title="Categorias">
        <template #header><h2 class="text-lg font-semibold text-gray-800">Categorias</h2></template>
        <BasePageHeader title="Categorias" description="Gerencie as categorias de produtos" create-label="Nova Categoria" @new="openCreate" />
        <BaseFilterPanel v-model:search="search" @search="applyFilters" />
        <div class="bg-white rounded-xl border border-gray-200">
            <DataTable :value="categories.data" stripedRows v-if="categories.data.length > 0">
                <Column field="name" header="Nome" />
                <Column field="description" header="Descrição" />
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
            <BaseEmptyState v-else title="Nenhuma categoria" description="Crie sua primeira categoria." />
        </div>
        <FormModal v-model:visible="modalVisible" :category="editingCategory" />
        <BaseConfirmDialog :visible="deleteId !== null" title="Excluir categoria" message="Tem certeza?" @confirm="destroy" @cancel="deleteId = null" />
    </ErpLayout>
</template>
