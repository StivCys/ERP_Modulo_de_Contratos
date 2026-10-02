<template>
    <Head title="Regras de Precificação" />
    <AuthenticatedLayout>
        <div class="py-2">
            <div class="max-w-8xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Regras Adicionais e Descontos
                    </h2>
                    <Link href="/regra-adicional/create" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Nova Regra
                    </Link>
                </div>

                <div v-if="$page.props.flash.success" class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                    {{ $page.props.flash.success }}
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 overflow-x-auto">
                        <table class="w-full whitespace-nowrap">
                            <thead>
                                <tr class="text-left font-bold">
                                    <th class="pb-4 pt-6 px-6">Nome da Regra</th>
                                    <th class="pb-4 pt-6 px-6">Tipo</th>
                                    <th class="pb-4 pt-6 px-6">Status</th>
                                    <th class="pb-4 pt-6 px-6">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="regra in regras.data" :key="regra.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                                    <td class="border-t px-6 py-4">{{ regra.nome }}</td>
                                    <td class="border-t px-6 py-4">{{ parseTipo(regra.tipo_regra) }}</td>
                                    <td class="border-t px-6 py-4">
                                        <span :class="regra.ativo ? 'text-green-600 font-bold' : 'text-red-500'">
                                            {{ regra.ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="border-t px-6 py-4 flex gap-2">
                                        <Link :href="`/regra-adicional/${regra.id}/edit`" class="text-indigo-600 hover:text-indigo-900">Editar</Link>
                                        <button @click="remover(regra.id)" class="text-red-600 hover:text-red-900">Remover</button>
                                    </td>
                                </tr>
                                <tr v-if="regras.data.length === 0">
                                    <td class="border-t px-6 py-4" colspan="4">Nenhuma regra cadastrada.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    regras: Object,
});

const parseTipo = (tipo) => {
    const tipos = {
        'quantidade_servicos': 'Desconto por Quantidade',
        'desconto_progressivo': 'Desconto Progressivo',
        'servico_especifico': 'Acres./Desc. Serviço Específico',
        'fidelidade': 'Fidelidade'
    };
    return tipos[tipo] || tipo;
};

const remover = (id) => {
    if (confirm('Tem certeza que deseja remover esta regra de precificação?')) {
        router.delete(`/regra-adicional/${id}`);
    }
};
</script>
