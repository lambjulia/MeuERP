<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

defineProps({
    title: String,
});

const page = usePage();
const toast = useToast();

const sidebarOpen = ref(true);
const mobileSidebarOpen = ref(false);

const user = computed(() => page.props.auth?.user);

const menuItems = [
    { label: 'Dashboard', icon: 'pi pi-home', route: 'dashboard' },
    {
        label: 'Cadastros',
        icon: 'pi pi-folder',
        children: [
            { label: 'Empresas', icon: 'pi pi-building', route: 'companies.index' },
            { label: 'Clientes', icon: 'pi pi-users', route: 'customers.index' },
            { label: 'Fornecedores', icon: 'pi pi-truck', route: 'suppliers.index' },
            { label: 'Categorias', icon: 'pi pi-tags', route: 'categories.index' },
            { label: 'Produtos', icon: 'pi pi-box', route: 'products.index' },
        ],
    },
    {
        label: 'Operação',
        icon: 'pi pi-cog',
        children: [
            { label: 'Compras', icon: 'pi pi-shopping-cart', route: 'purchases.index' },
            { label: 'Vendas', icon: 'pi pi-dollar', route: 'sales.index' },
            { label: 'Estoque', icon: 'pi pi-warehouse', route: 'stock.index' },
        ],
    },
    {
        label: 'Financeiro',
        icon: 'pi pi-wallet',
        children: [
            { label: 'Contas a Pagar', icon: 'pi pi-arrow-up', route: 'accounts-payable.index' },
            { label: 'Contas a Receber', icon: 'pi pi-arrow-down', route: 'accounts-receivable.index' },
        ],
    },
];

const expandedGroups = ref(['Cadastros', 'Operação', 'Financeiro']);

function toggleGroup(label) {
    const idx = expandedGroups.value.indexOf(label);
    if (idx >= 0) {
        expandedGroups.value.splice(idx, 1);
    } else {
        expandedGroups.value.push(label);
    }
}

function isActive(routeName) {
    try {
        return route().current(routeName);
    } catch {
        return false;
    }
}

function isGroupActive(item) {
    if (!item.children) return false;
    return item.children.some(child => {
        try {
            return route().current(child.route) || route().current(child.route.replace('.index', '.*'));
        } catch {
            return false;
        }
    });
}

const logout = () => {
    router.post(route('logout'));
};

// Flash messages as toasts
const flash = computed(() => page.props.flash || {});
if (flash.value.success) {
    toast.add({ severity: 'success', summary: 'Sucesso', detail: flash.value.success, life: 3000 });
}
if (flash.value.error) {
    toast.add({ severity: 'error', summary: 'Erro', detail: flash.value.error, life: 5000 });
}
</script>

<template>
    <div>
        <Head :title="title" />
        <Toast />

        <div class="min-h-screen flex bg-gray-50">
            <!-- Sidebar -->
            <aside
                :class="[
                    'fixed inset-y-0 left-0 z-30 flex flex-col bg-base text-white transition-all duration-300',
                    sidebarOpen ? 'w-64' : 'w-20',
                    'hidden lg:flex',
                ]"
            >
                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-4 border-b border-gray-800">
                    <Link :href="route('dashboard')" class="flex items-center gap-2">
                        <span class="text-xl font-bold text-primary">
                            <i class="pi pi-box"></i>
                        </span>
                        <span v-if="sidebarOpen" class="text-lg font-bold">MeuERP</span>
                    </Link>
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white">
                        <i :class="sidebarOpen ? 'pi pi-angle-left' : 'pi pi-angle-right'"></i>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto py-4 px-2">
                    <template v-for="item in menuItems" :key="item.label">
                        <!-- Single item -->
                        <template v-if="!item.children">
                            <Link
                                :href="route(item.route)"
                                :class="[
                                    'flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 transition-colors',
                                    isActive(item.route)
                                        ? 'bg-primary text-white'
                                        : 'text-gray-300 hover:bg-gray-800 hover:text-white',
                                ]"
                            >
                                <i :class="item.icon" class="text-base w-5 text-center"></i>
                                <span v-if="sidebarOpen" class="text-sm">{{ item.label }}</span>
                            </Link>
                        </template>

                        <!-- Group -->
                        <template v-else>
                            <button
                                @click="toggleGroup(item.label)"
                                :class="[
                                    'flex items-center justify-between w-full px-3 py-2.5 rounded-lg mb-1 transition-colors',
                                    isGroupActive(item)
                                        ? 'text-primary'
                                        : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                                ]"
                            >
                                <div class="flex items-center gap-3">
                                    <i :class="item.icon" class="text-base w-5 text-center"></i>
                                    <span v-if="sidebarOpen" class="text-sm font-medium">{{ item.label }}</span>
                                </div>
                                <i
                                    v-if="sidebarOpen"
                                    :class="expandedGroups.includes(item.label) ? 'pi pi-angle-up' : 'pi pi-angle-down'"
                                    class="text-xs"
                                ></i>
                            </button>
                            <div
                                v-if="sidebarOpen && expandedGroups.includes(item.label)"
                                class="ml-4 mb-2"
                            >
                                <Link
                                    v-for="child in item.children"
                                    :key="child.route"
                                    :href="route(child.route)"
                                    :class="[
                                        'flex items-center gap-3 px-3 py-2 rounded-lg mb-0.5 transition-colors',
                                            isActive(child.route)
                                            ? 'bg-primary/20 text-primary'
                                            : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                                    ]"
                                >
                                    <i :class="child.icon" class="text-sm w-5 text-center"></i>
                                    <span class="text-sm">{{ child.label }}</span>
                                </Link>
                            </div>
                        </template>
                    </template>
                </nav>

                <!-- User -->
                <div class="border-t border-gray-800 p-4">
                        <div v-if="sidebarOpen" class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-sm font-bold">
                            {{ user?.name?.charAt(0)?.toUpperCase() }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ user?.name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ user?.email }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-3">
                        <Link :href="route('profile.show')" class="flex-1 text-center text-xs text-gray-400 hover:text-white py-1 rounded hover:bg-gray-800">
                            <i class="pi pi-user"></i>
                            <span v-if="sidebarOpen" class="ml-1">Perfil</span>
                        </Link>
                        <button @click="logout" class="flex-1 text-center text-xs text-gray-400 hover:text-accent py-1 rounded hover:bg-gray-800">
                            <i class="pi pi-sign-out"></i>
                            <span v-if="sidebarOpen" class="ml-1">Sair</span>
                        </button>
                    </div>
                </div>
            </aside>

            <!-- Mobile sidebar overlay -->
            <div
                v-if="mobileSidebarOpen"
                class="fixed inset-0 z-40 bg-black/50 lg:hidden"
                @click="mobileSidebarOpen = false"
            ></div>

            <!-- Mobile sidebar -->
            <aside
                :class="[
                    'fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transform transition-transform lg:hidden',
                    mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full',
                ]"
            >
                <div class="flex items-center justify-between h-16 px-4 border-b border-gray-800">
                    <span class="text-lg font-bold text-primary">MeuERP</span>
                    <button @click="mobileSidebarOpen = false" class="text-gray-400 hover:text-white">
                        <i class="pi pi-times"></i>
                    </button>
                </div>
                <nav class="flex-1 overflow-y-auto py-4 px-2">
                    <template v-for="item in menuItems" :key="'m-' + item.label">
                        <template v-if="!item.children">
                            <Link
                                :href="route(item.route)"
                                @click="mobileSidebarOpen = false"
                                :class="[
                                    'flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 transition-colors',
                                    isActive(item.route)
                                                        ? 'bg-primary text-white'
                                                        : 'text-gray-300 hover:bg-gray-800 hover:text-white',
                                ]"
                            >
                                <i :class="item.icon" class="text-base w-5 text-center"></i>
                                <span class="text-sm">{{ item.label }}</span>
                            </Link>
                        </template>
                        <template v-else>
                            <button
                                @click="toggleGroup(item.label)"
                                class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg mb-1 text-gray-400 hover:bg-gray-800 hover:text-white"
                            >
                                <div class="flex items-center gap-3">
                                    <i :class="item.icon" class="text-base w-5 text-center"></i>
                                    <span class="text-sm font-medium">{{ item.label }}</span>
                                </div>
                                <i :class="expandedGroups.includes(item.label) ? 'pi pi-angle-up' : 'pi pi-angle-down'" class="text-xs"></i>
                            </button>
                            <div v-if="expandedGroups.includes(item.label)" class="ml-4 mb-2">
                                <Link
                                    v-for="child in item.children"
                                    :key="child.route"
                                    :href="route(child.route)"
                                    @click="mobileSidebarOpen = false"
                                    :class="[
                                        'flex items-center gap-3 px-3 py-2 rounded-lg mb-0.5 transition-colors',
                                        isActive(child.route)
                                            ? 'bg-primary/20 text-primary'
                                            : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                                    ]"
                                >
                                    <i :class="child.icon" class="text-sm w-5 text-center"></i>
                                    <span class="text-sm">{{ child.label }}</span>
                                </Link>
                            </div>
                        </template>
                    </template>
                </nav>
            </aside>

            <!-- Main content -->
            <div :class="['flex-1 flex flex-col transition-all duration-300', sidebarOpen ? 'lg:ml-64' : 'lg:ml-20']">
                <!-- Top bar -->
                <header class="sticky top-0 z-20 bg-white border-b border-gray-200 h-16 flex items-center px-4 lg:px-8">
                    <button @click="mobileSidebarOpen = true" class="lg:hidden mr-4 text-gray-500 hover:text-gray-700">
                        <i class="pi pi-bars text-xl"></i>
                    </button>
                    <div class="flex-1">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page content -->
                <main class="flex-1 p-4 lg:p-8">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
