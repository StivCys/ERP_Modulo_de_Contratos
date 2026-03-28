<template>
    <nav class="h-14 border-b flex items-center justify-between px-4">
        
        <!-- ESQUERDA -->
        <div class="flex items-center gap-4">
            <ApplicationLogo />

            <template v-for="item in visibleMenu" :key="item.key">
                <!-- Link direto -->
                <Link
                    v-if="item.type === 'link'"
                    :href="route(item.route)"
                    class="nav-item"
                    :class="{ active: $page.component.startsWith(item.key) }"
                >
                    <component :is="icons[item.icon]" :size="16" />
                    {{ item.label }}
                </Link>

                <!-- Grupo -->
                <button
                    v-else-if="item.type === 'group'"
                    class="nav-item flex items-center gap-2"
                    :class="{ active: activeGroup === item.key }"
                    @click="setGroup(item.key)"
                >
                    <component :is="icons[item.icon]" :size="16" />
                    {{ item.label }}
                </button>
            </template>
        </div>

        <!-- DIREITA (USER DROPDOWN) -->
        <div class="flex items-center">
            <Dropdown align="right" width="48">
                <template #trigger>
                    <button class="flex items-center gap-2">
                        <span>{{ $page.props.auth.user.name }}</span>
                        <ChevronDown :size="14" />
                    </button>
                </template>

                <template #content>
                    <DropdownLink :href="route('profile.edit')">
                        Profile
                    </DropdownLink>

                    <DropdownLink
                        :href="route('logout')"
                        method="post"
                        as="button"
                    >
                        Log Out
                    </DropdownLink>
                </template>
            </Dropdown>
        </div>
    </nav>
</template>

<!-- <pre style="font-size:10px; background:#000; color:#0f0; padding:8px"> -->
<!-- visibleMenu: {{ visibleMenu }} -->
<!-- activeGroup: {{ activeGroup }} -->
<!-- </pre> -->

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ChevronDown } from 'lucide-vue-next'
import * as LucideIcons from 'lucide-vue-next'
import { menuConfig } from '@/config/menuConfig'
import { usePermissions } from '@/composables/usePermissions'
import { useActiveMenu } from '@/composables/useActiveMenu'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

const { filterMenu } = usePermissions()
const { activeGroup, setGroup } = useActiveMenu()

const icons = LucideIcons
const visibleMenu = computed(() => filterMenu(menuConfig))

</script>