<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch } from 'vue'
import { Head, usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

defineProps({
    clientes: Object
});

const page = usePage()
const flashMessage = ref(page.props.flash.success)

// console.log(page.props)

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

const editCliente = (cliente) => {
    router.get(`/cliente/${cliente.id}/edit`)
}

const createCliente = () => {
    router.get(route('cliente.create'))
}
// const deleteCliente = (cliente) => {
//     if (confirm('Are you sure you want to delete this cliente?')) {
//         router.delete(route('cliente.destroy', cliente.id), {
//             onSuccess: () => {
//                 console.log('deleted')
//             }
//         })
//     }
// }

</script>

<template>

    <Head title="cliente" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Clientes
                    </h2>
                    <button @click="createCliente" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                        Novo Cliente
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
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">
                                            ID
                                        </th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">
                                            Nome
                                        </th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">
                                            Email
                                        </th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">
                                            Cpf/Cnpj
                                        </th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">
                                            Status
                                        </th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">
                                            Ações
                                        </th>

                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="cliente in clientes.data" :key="cliente.id">
                                        <td class="px-4 py-2 border-b">
                                            {{ cliente.id }}
                                        </td>

                                        <td class="px-4 py-2 border-b">
                                            {{ cliente.nome }}
                                        </td>

                                        <td class="px-4 py-2 border-b">
                                            {{ cliente.email }}
                                        </td>
                                        <td class="px-4 py-2 border-b">
                                            {{ cliente.cpf_cnpj }}
                                        </td>
                                        <td class="px-4 py-2 border-b">
                                            {{ cliente.ativo === 'sim' ? 'Sim' : 'Não' }}
                                        </td>
                                        <td class="px-4 py-2 border-b">
                                            <button v-on:click="editCliente(cliente)"
                                                class="px-2 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                                                Edit
                                            </button>
                                            <!-- <button v-on:click="deleteCliente(cliente)"
                                                class="px-2 py-1 text-sm text-white bg-red-500 rounded hover:bg-red-600">
                                                Delete
                                            </button> -->
                                        </td>
                                    </tr>

                                    <tr v-if="clientes.data.length === 0">
                                        <td colspan="3" class="px-4 py-4 text-center text-gray-500">
                                            No cliente found
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="flex gap-2 mt-4 items-center justify-center">
                                <button v-for="link in clientes.links" :key="link.label" v-html="link.label"
                                    :disabled="!link.url" @click="router.visit(link.url)"
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
