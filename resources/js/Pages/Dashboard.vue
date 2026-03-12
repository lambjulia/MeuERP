<script setup>
import ErpLayout from '@/Layouts/ErpLayout.vue';
import BaseMoney from '@/Components/BaseMoney.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    low_stock_products: Array,
});
</script>

<template>
    <ErpLayout title="Dashboard">
        <template #header>
            <h2 class="text-lg font-semibold text-gray-800">Dashboard</h2>
        </template>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Produtos</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_products }}</p>
                    </div>
                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                        <i class="pi pi-box text-primary"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Clientes</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_customers }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="pi pi-users text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Fornecedores</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_suppliers }}</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="pi pi-truck text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Estoque Baixo</p>
                        <p class="text-2xl font-bold" :class="stats.low_stock > 0 ? 'text-red-600' : 'text-gray-900'">{{ stats.low_stock }}</p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="pi pi-exclamation-triangle text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">A Pagar</p>
                        <p class="text-xl font-bold text-red-600 mt-1"><BaseMoney :value="stats.total_payable" /></p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="pi pi-arrow-up text-red-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">A Receber</p>
                        <p class="text-xl font-bold text-green-600 mt-1"><BaseMoney :value="stats.total_receivable" /></p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="pi pi-arrow-down text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Vendas do Mês</p>
                        <p class="text-xl font-bold text-primary mt-1"><BaseMoney :value="stats.sales_month" /></p>
                    </div>
                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                        <i class="pi pi-dollar text-primary"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Compras do Mês</p>
                        <p class="text-xl font-bold text-orange-600 mt-1"><BaseMoney :value="stats.purchases_month" /></p>
                    </div>
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="pi pi-shopping-cart text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Table -->
        <div v-if="low_stock_products.length > 0" class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="pi pi-exclamation-triangle text-red-500 mr-2"></i>
                Produtos com Estoque Baixo
            </h3>
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b">
                        <th class="pb-3">Produto</th>
                        <th class="pb-3 text-center">Estoque Atual</th>
                        <th class="pb-3 text-center">Mínimo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in low_stock_products" :key="product.id" class="border-b last:border-0">
                        <td class="py-3">
                            <Link :href="route('products.index')" class="text-primary hover:text-primary/80">
                                {{ product.name }}
                            </Link>
                        </td>
                        <td class="py-3 text-center">
                            <span class="text-red-600 font-semibold">{{ product.stock_quantity }}</span>
                        </td>
                        <td class="py-3 text-center text-gray-500">{{ product.minimum_stock }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </ErpLayout>
</template>
