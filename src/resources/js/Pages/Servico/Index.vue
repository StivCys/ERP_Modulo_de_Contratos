<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
    servicos: Object,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const dateStart = ref(props.filters?.date_start || '')
const dateEnd = ref(props.filters?.date_end || '')

const filter = () => {
    router.get(route('servico.index'), {
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

watch(
    () => page.props.flash.success,
    (message) => {
        if (message) {
            flashMessage.value = message
            setTimeout(() => {
                flashMessage.value = null
            }, 5000)
        }
    },
    { immediate: true }
)

const editServico = (servico) => {
    router.get(route('servico.edit', servico.id))
}

const createServico = () => {
    router.get(route('servico.create'))
}

const deleteServico = (servico) => {
    if (confirm('Tem certeza que deseja apagar este serviço?')) {
        router.delete(route('servico.destroy', servico.id))
    }
}
</script>

<template>
    <Head title="Serviços" />

    <AuthenticatedLayout>
        <div class="py-2">
            <div class="max-w-8xl p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Serviços
                    </h2>
                    <button @click="createServico" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                        Novo Serviço
                    </button>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap gap-4 mb-6 items-end">
                    <div class="flex flex-col w-full sm:w-auto">
                        <label class="text-xs text-transparent mb-1 hidden sm:block">&nbsp;</label>
                        <input v-model="search" @keyup.enter="filter" type="text" placeholder="Buscar por Nome do Serviço" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" />
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xs text-gray-600 mb-1 font-medium">Data de Cadastro I    nicio</label>
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
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-200 rounded-lg">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">ID</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Nome</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Valor Base Mensal</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="servico in servicos.data" :key="servico.id">
                                        <td class="px-4 py-2 border-b">{{ servico.id }}</td>
                                        <td class="px-4 py-2 border-b">{{ servico.nome }}</td>
                                        <td class="px-4 py-2 border-b">R$ {{ servico.valor }}</td>
                                        <td class="px-4 py-2 border-b flex gap-2">
                                            <button @click="editServico(servico)" class="px-2 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                                                Edit
                                            </button>
                                            <button @click="deleteServico(servico)" class="px-2 py-1 text-sm text-white bg-red-500 rounded hover:bg-red-600">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="servicos.data && servicos.data.length === 0">
                                        <td colspan="4" class="px-4 py-4 text-center text-gray-500">Nenhum serviço encontrado</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="flex items-center justify-center gap-2 mt-4" v-if="servicos.data && servicos.data.length > 0">
                                <button v-for="link in servicos.links" :key="link.label" v-html="link.label"
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
