<template>
    <div class="flex flex-col h-screen relative">
        <AppNavbar />

        <div class="flex flex-1 overflow-hidden">
            <AppSidebar />

            <main class="flex-1 overflow-y-auto p-6 relative">
                <!-- Toast for Error -->
                <transition
                    enter-active-class="transform ease-out duration-300 transition"
                    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                    leave-active-class="transition ease-in duration-100"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="flashError" class="fixed top-4 right-4 z-50 p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100 border border-red-200 shadow-lg flex items-center justify-between min-w-[300px]" role="alert">
                        <span>{{ flashError }}</span>
                        <button @click="flashError = null" class="ml-4 -mx-1.5 -my-1.5 bg-red-100 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex h-8 w-8" aria-label="Close">
                            <span class="sr-only">Close</span>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>
                </transition>

                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppNavbar from '@/Components/AppNavbar.vue'
import AppSidebar from '@/Components/AppSidebar.vue'

const page = usePage()
const flashError = ref(page.props.flash?.error)

watch(
    () => page.props.flash?.error,
    (message) => {
        if (message) {
            flashError.value = message
            setTimeout(() => {
                if (flashError.value === message) {
                    flashError.value = null
                }
            }, 6000)
        }
    },
    { immediate: true }
)
</script>