<template>
    <div class="min-h-screen flex">
        <!-- Painel lateral esquerdo -->
        <div class="hidden lg:flex lg:w-1/2 bg-base items-center justify-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-10 left-10 w-64 h-64 bg-primary/30 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-10 w-80 h-80 bg-secondary/25 rounded-full blur-3xl"></div>
            </div>
            <div class="relative z-10 text-center px-8">
                <slot name="logo" />
                <p class="mt-4 text-muted text-lg max-w-sm mx-auto">Sistema de gestão empresarial completo para o seu negócio.</p>
            </div>
        </div>

        <!-- Painel do formulário -->
        <div class="w-full lg:w-1/2 flex flex-col items-center justify-center bg-surface px-6 py-12">
            <div class="lg:hidden mb-8">
                <slot name="logo" />
            </div>
            <div class="w-full sm:max-w-md bg-white rounded-xl border border-gray-200 shadow-sm px-8 py-8">
                <slot />
            </div>
        </div>
    </div>
</template>
