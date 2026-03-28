<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'

const props = defineProps({
    servico: Object
})

const form = useForm({
    id: props.servico?.id ?? null,
    nome: props.servico?.nome ?? '',
    valor: props.servico?.valor ?? ''
})

const submit = () => {
    if (props.servico) {
        form.put(route('servico.update', props.servico.id))
    } else {
        form.post(route('servico.store'))
    }
}

const back = () => {
    router.visit(route('servico.index'))
}
</script>

<template>
    <Head :title="props.servico ? 'Edit Service' : 'Create Service'" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Header movido para dentro do layout principal -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ props.servico ? 'Editar Serviço' : 'Novo Serviço' }}
                    </h2>
                </div>
                
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nome do Serviço</label>
                                <input v-model="form.nome" type="text" class="mt-1 block w-full border rounded" required />
                                <div v-if="form.errors.nome" class="text-red-500 text-sm mt-1">{{ form.errors.nome }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Valor Base Mensal</label>
                                <input v-model="form.valor" type="number" step="0.01" min="0" class="mt-1 block w-full border rounded" required />
                                <div v-if="form.errors.valor" class="text-red-500 text-sm mt-1">{{ form.errors.valor }}</div>
                            </div>
                            <div class="flex gap-4 mt-4">
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    {{ props.servico ? 'Atualizar' : 'Salvar' }}
                                </button>
                                <button type="button" @click="back" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                    Voltar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
