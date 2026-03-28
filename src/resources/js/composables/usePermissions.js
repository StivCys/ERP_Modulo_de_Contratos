//composable de acesso a permissões do usuário, baseado no Spatie
// resources/js/composables/usePermissions.js
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export function usePermissions() {
  const page = usePage()

  

  const permissions = computed(() => page.props.auth?.permissions ?? [])
  const roles = computed(() => page.props.auth?.roles ?? [])

  const can = (permission) => {
    if (!permission) return true // sem restrição definida = visível
    return permissions.value.includes(permission)
  }

  const hasRole = (role) => {
    if (!role) return true
    return roles.value.includes(role)
  }

  const canSeeItem = (item) => {
    return can(item.permission) && hasRole(item.role)
  }

  const filterMenu = (items) => {

    // console.log('filterMenu input:', items)
    // console.log('permissions:', permissions.value)
    return items
      .filter(canSeeItem)
      .map(item => ({
        ...item,
        children: item.children ? filterMenu(item.children) : undefined,
      }))
      .filter(item => item.type !== 'group' || item.children?.length > 0)
  }

  return { can, hasRole, canSeeItem, filterMenu }
}