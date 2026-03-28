<template>
    <div class="flex flex-col h-screen">
        <AppNavbar />

        <div class="flex flex-1 overflow-hidden">
            <AppSidebar />

            <main class="flex-1 overflow-y-auto p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import AppNavbar from '@/Components/AppNavbar.vue'
import AppSidebar from '@/Components/AppSidebar.vue'
</script>