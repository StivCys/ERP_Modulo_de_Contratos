<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'


const props = defineProps({
    cliente: Object
})

const home = () => {
    router.get(route('cliente.index'))
}

const form = useForm({
    id: props.cliente?.id ?? null,
    nome: props.cliente?.nome ?? '',
    email: props.cliente?.email ?? '',
    cpf_cnpj: props.cliente?.cpf_cnpj ?? '',
    ativo: props.cliente?.ativo ?? ''
})

const submit = () => {
    if (props.cliente) {
        form.put(route('cliente.update', props.cliente.id))
    } else {
        form.post(route('cliente.store'))
    }
}
</script>
<template>

    <Head title="Customer Form" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Header movido para dentro do layout principal -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Create or Edit Customer
                    </h2>
                </div>
                
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <form @submit.prevent="submit" class="space-y-4">

                            <div>
                                <input v-model="form.id" type="hidden" />
                                <label class="block text-sm font-medium text-gray-700">Nome</label>
                                <input v-model="form.nome" type="text" class="mt-1 block w-full border rounded" />
                                <div v-if="form.errors.nome" class="text-red-500 text-sm mt-1">{{ form.errors.nome }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input v-model="form.email" type="email" class="mt-1 block w-full border rounded" />
                                <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cpf/Cnpj</label>
                                <input v-model="form.cpf_cnpj" type="text" class="mt-1 block w-full border rounded" />
                                <div v-if="form.errors.cpf_cnpj" class="text-red-500 text-sm mt-1">{{ form.errors.cpf_cnpj }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ativo</label>

                                <select v-model="form.ativo" class="mt-1 block w-full border rounded">
                                    <option value="sim">Sim</option>
                                    <option value="nao">Não</option>
                                </select>
                                <div v-if="form.errors.ativo" class="text-red-500 text-sm mt-1">{{ form.errors.ativo }}</div>
                            </div>

                            <div class="mt-4 flex justify-between">

                                <div>
                                    <button type="submit"
                                        class="px-4 py-2 mt-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                        {{ props.cliente ? 'Update' : 'Create' }}
                                    </button>
                                </div>

                                <div>
                                    <button @click="home" type="button"
                                        class="px-4 py-2 mt-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                        Back
                                    </button>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>