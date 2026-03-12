<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'" class="flex items-center gap-3">
        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-primary shadow-lg">
            <i class="pi pi-box text-white text-2xl"></i>
        </div>
        <span class="text-2xl font-bold text-primary lg:text-white">MeuERP</span>
    </Link>
</template>
