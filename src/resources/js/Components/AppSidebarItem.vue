<template>
    <div>
        <!-- Item simples -->
        <Link v-if="!item.children?.length" :href="safeRoute(item.route)" class="sidebar-item  flex items-center gap-2"
            :class="{ active: $page.url.startsWith('/' + item.route) }">
            <component v-if="item.icon" :is="icons[item.icon]" :size="15" />
            {{ item.label }}
        </Link>

        <!-- Item com sub-itens -->
        <div v-else>
            <button class="sidebar-item w-full justify-between" @click="open = !open">
                <span class="flex items-center gap-2">
                    <component v-if="item.icon" :is="icons[item.icon]" :size="15" />
                    {{ item.label }}
                </span>
                <ChevronRight :size="13" :class="{ 'rotate-90': open }" />
            </button>

            <div v-if="open" class="ml-4 border-l pl-2 mt-1 flex flex-col gap-0.5">
                <!-- recursão -->
                <AppSidebarItem v-for="child in item.children" :key="child.route" :item="child" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ChevronRight } from 'lucide-vue-next'
import * as LucideIcons from 'lucide-vue-next'
import AppSidebarItem from './AppSidebarItem.vue' // importa a si mesmo para recursão


defineProps({ item: Object })
const open = ref(false)
const icons = LucideIcons

// console.log(Ziggy)
// verifica se a rota existe no Ziggy antes de tentar resolver
const safeRoute = (name) => {
    try {
        return route(name)
    } catch (error) {
        console.error('Erro ao gerar rota:', name, error)
        return '##'
    }
}
</script>