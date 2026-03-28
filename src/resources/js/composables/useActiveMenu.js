// resources/js/composables/useActiveMenu.js
import { ref } from 'vue'

// fora do composable = estado compartilhado entre todos os componentes
const activeGroup = ref(null)

export function useActiveMenu() {
  const setGroup = (key) => {
    activeGroup.value = activeGroup.value === key ? null : key
  }

  return { activeGroup, setGroup }
}