<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

defineProps({
    servicos: Object
})

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
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Serviços
                    </h2>
                    <button @click="createServico" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                        Novo Serviço
                    </button>
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
