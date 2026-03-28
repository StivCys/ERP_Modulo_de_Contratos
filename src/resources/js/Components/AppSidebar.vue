<template>
    <Transition name="sidebar">
        <aside v-if="activeGroup" class="w-64 border-r h-full overflow-y-auto">
            <div class="p-3">
                <p class="text-xs font-medium text-muted-foreground uppercase mb-2 px-2">
                    {{ currentGroup?.label }}
                </p>

                <AppSidebarItem v-for="item in currentGroup?.children" :key="item.route" :item="item" />
            </div>
        </aside>
    </Transition>
</template>

<script setup>
import { computed } from 'vue'
import { menuConfig } from '@/config/menuConfig'
import { usePermissions } from '@/composables/usePermissions'
import { useActiveMenu } from '@/composables/useActiveMenu'
import AppSidebarItem from './AppSidebarItem.vue'

const { activeGroup } = useActiveMenu()
const { filterMenu } = usePermissions()

const currentGroup = computed(() => {
    const group = menuConfig.find(m => m.key === activeGroup.value)
    if (!group) return null
    return { ...group, children: filterMenu(group.children ?? []) }
})

</script>