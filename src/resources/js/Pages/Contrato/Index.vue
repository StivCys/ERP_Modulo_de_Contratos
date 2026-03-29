<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
    contratos: Object,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const dateStart = ref(props.filters?.date_start || '')
const dateEnd = ref(props.filters?.date_end || '')

const filter = () => {
    router.get(route('contrato.index'), {
        search: search.value,
        date_start: dateStart.value,
        date_end: dateEnd.value,
    }, { preserveState: true, replace: true })
}

const clearFilter = () => {
    search.value = ''
    dateStart.value = ''
    dateEnd.value = ''
    filter()
}

const page = usePage()
const flashMessage = ref(page.props.flash.success)
const flashError = ref(page.props.flash.error)

watch(
    () => page.props.flash.success,
    (message) => {
        if (message) {
            flashMessage.value = message
            setTimeout(() => { flashMessage.value = null }, 5000)
        }
    },
    { immediate: true }
)

watch(
    () => page.props.flash.error,
    (message) => {
        if (message) {
            flashError.value = message
            setTimeout(() => { flashError.value = null }, 5000)
        }
    },
    { immediate: true }
)

const editContrato = (contrato) => {
    router.get(route('contrato.edit', contrato.id))
}

const createContrato = () => {
    router.get(route('contrato.create'))
}

const deleteContrato = (contrato) => {
    if (confirm('Tem certeza que deseja apagar este contrato?')) {
        router.delete(route('contrato.destroy', contrato.id))
    }
}
</script>

<template>
    <Head title="Contratos" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Contratos
                    </h2>
                    <button @click="createContrato" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                        Novo Contrato
                    </button>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap gap-4 mb-6 items-end">
                    <div class="flex flex-col w-full sm:w-auto">
                        <label class="text-xs text-transparent mb-1 hidden sm:block">&nbsp;</label>
                        <input v-model="search" @keyup.enter="filter" type="text" placeholder="Buscar por Cliente" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" />
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xs text-gray-600 mb-1 font-medium">Data de Cadastro Inicio</label>
                        <input v-model="dateStart" type="date" class="border-gray-300 rounded-md shadow-sm" />
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xs text-gray-600 mb-1 font-medium">Data de Cadastro Fim</label>
                        <input v-model="dateEnd" type="date" class="border-gray-300 rounded-md shadow-sm" />
                    </div>
                    <button @click="filter" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">Buscar</button>
                    <button @click="clearFilter" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Limpar</button>
                </div>
                <div v-if="flashMessage" class="p-4 mb-6 text-green-800 bg-green-200 rounded">
                    {{ flashMessage }}
                </div>
                <div v-if="flashError" class="p-4 mb-6 text-red-800 bg-red-200 rounded">
                    {{ flashError }}
                </div>
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-200 rounded-lg">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">ID</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Cliente</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Data Início</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Status</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Valor Total</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Regras Modificadoras Atuando</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="contrato in contratos.data" :key="contrato.id">
                                        <td class="px-4 py-2 border-b">{{ contrato.id }}</td>
                                        <td class="px-4 py-2 border-b">{{ contrato.cliente?.nome }}</td>
                                        <td class="px-4 py-2 border-b">{{ contrato.data_inicio }}</td>
                                        <td class="px-4 py-2 border-b">
                                            <span :class="{'text-green-600': contrato.status === 'ativo', 'text-red-600': contrato.status === 'cancelado'}">
                                                {{ contrato.status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border-b font-semibold">R$ {{ Number(contrato.valor_total).toFixed(2) }}</td>
                                        <td class="px-4 py-2 border-b">
                                            <div v-if="contrato.regras_aplicadas && contrato.regras_aplicadas.length > 0" class="flex flex-col gap-1">
                                                <div v-for="(rule, i) in contrato.regras_aplicadas" :key="i" 
                                                    class="text-xs px-2 py-1 rounded w-max"
                                                    :class="rule.tipo === 'desconto' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                                    {{ rule.regra }}
                                                </div>
                                            </div>
                                            <span v-else class="text-gray-500 text-sm">Nenhuma</span>
                                        </td>
                                        <td class="px-4 py-2 border-b flex gap-2">
                                            <button @click="editContrato(contrato)" class="px-2 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                                                Edit / Detalhes
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="contratos.data && contratos.data.length === 0">
                                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">Nenhum contrato encontrado</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="flex items-center justify-center gap-2 mt-4" v-if="contratos.data && contratos.data.length > 0">
                                <button v-for="link in contratos.links" :key="link.label" v-html="link.label"
                                    :disabled="!link.url" @click="link.url && router.visit(link.url)"
                                    class="px-3 py-1 border rounded"
                                    :class="{ 'bg-blue-500 text-white': link.active }" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
