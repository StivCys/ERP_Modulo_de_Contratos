<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch, onUnmounted } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import axios from 'axios'; // ✅ Import explícito

const props = defineProps({ clientes: Object, filters: Object });

const search = ref(props.filters?.search || '');
const dateStart = ref(props.filters?.date_start || '');
const dateEnd = ref(props.filters?.date_end || '');
const ativo = ref(props.filters?.ativo || '');

// Estado do relatório
const reportStatus = ref('idle'); // idle | generating | ready | error
const reportMessage = ref('');
const reportDownloadUrl = ref(null);
let reportPolling = null;

const page = usePage();
const flashMessage = ref(page.props.flash?.success);

// Flash messages
watch(() => page.props.flash?.success, (msg) => {
    if (msg) {
        flashMessage.value = msg;
        setTimeout(() => flashMessage.value = null, 5000);
    }
}, { immediate: true });

const filter = () => {
    router.get(route('cliente.index'), {
        search: search.value,
        date_start: dateStart.value,
        date_end: dateEnd.value,
        ativo: ativo.value,
    }, { preserveState: true, replace: true });
};

const clearFilter = () => {
    search.value = '';
    dateStart.value = '';
    dateEnd.value = '';
    ativo.value = '';
    filter();
};

const generateCsvReport = () => {
    reportStatus.value = 'generating';
    reportMessage.value = 'Iniciando geração do relatório...';
    reportDownloadUrl.value = null;

    router.post(route('cliente.relatorio.csv'), {
        search: search.value,
        date_start: dateStart.value,
        date_end: dateEnd.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (response) => {
            // ✅ Pega o ID que o controller passou via with()
            const relatorioId = response.props.flash?.relatorio_id;
            if (relatorioId) {
                console.log('✅ Polling iniciado para relatório:', relatorioId);
                startPolling(relatorioId);
            } else {
                reportStatus.value = 'error';
                reportMessage.value = 'Erro: ID do relatório não retornado pelo servidor.';
                console.error('❌ relatorio_id não encontrado nos props');
            }
        },
        onError: (errors) => {
            reportStatus.value = 'error';
            reportMessage.value = errors.relatorio || 'Erro ao iniciar relatório';
        }
    });
};

const startPolling = (relatorioId) => {
    const checkStatus = async () => {
        try {
            // Fallback seguro caso a rota Ziggy falhe
            const url = window.route?.('cliente.relatorio.status', relatorioId) 
                       || `/cliente/relatorio/${relatorioId}/status`;
                       
            const response = await axios.get(url);
            const data = response.data;
            
            console.log('📡 Resposta do status:', data); // 👈 OLHE AQUI NO CONSOLE

            if (data.status === 'completed') {
                reportStatus.value = 'ready';
                reportDownloadUrl.value = data.download_url;
                reportMessage.value = '✅ Relatório pronto para download!';
                clearInterval(reportPolling);
            } else if (data.status === 'failed') {
                reportStatus.value = 'error';
                reportMessage.value = `❌ Erro: ${data.error || 'Falha desconhecida'}`;
                clearInterval(reportPolling);
            } else {
                reportMessage.value = `🔄 Status: ${data.status || 'processando'}... (aguarde)`;
            }
        } catch (err) {
            console.error('🔥 Erro no polling:', err.response?.data || err.message);
            // Não pare o polling em erros temporários de rede
        }
    };

    checkStatus(); // 1ª verificação imediata
    reportPolling = setInterval(checkStatus, 3000);
};

onUnmounted(() => {
    if (reportPolling) clearInterval(reportPolling);
});

// Suas funções CRUD mantidas...
const editCliente = (cliente) => router.get(`/cliente/${cliente.id}/edit`);
const createCliente = () => router.get(route('cliente.create'));
const deleteCliente = (cliente) => {
    if (confirm('Tem certeza?')) router.delete(route('cliente.destroy', cliente.id));
};
</script>

<template>
    <Head title="Clientes" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <!-- Header com ações -->
                <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Clientes
                    </h2>
                    
                    <div class="flex flex-wrap gap-2">
                        <!-- Botão Relatório CSV -->
                        <button 
                            @click="generateCsvReport" 
                            :disabled="reportStatus === 'generating'"
                            class="px-4 py-2 text-white bg-purple-500 rounded hover:bg-purple-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <span v-if="reportStatus === 'generating'">⏳</span>
                            <span v-else>📊</span>
                            {{ reportStatus === 'generating' ? 'Gerando...' : 'Gerar Relatório CSV' }}
                        </button>
                        
                        <!-- Botão Novo Cliente -->
                        <button @click="createCliente" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                            Novo Cliente
                        </button>
                    </div>
                </div>

                <!-- Feedback do Relatório -->
                <div v-if="reportMessage" 
                     :class="{
                         'bg-blue-100 text-blue-800': reportStatus === 'generating',
                         'bg-green-100 text-green-800': reportStatus === 'ready',
                         'bg-red-100 text-red-800': reportStatus === 'error',
                         'bg-gray-100 text-gray-800': !reportStatus
                     }"
                     class="p-4 mb-4 rounded flex justify-between items-center"
                >
                    <span>{{ reportMessage }}</span>
                    <div v-if="reportStatus === 'ready'" class="flex gap-2">
                        <a :href="reportDownloadUrl" 
                           class="px-3 py-1 text-sm text-white bg-green-600 rounded hover:bg-green-700"
                           download
                        >
                            📥 Baixar CSV
                        </a>
                        <button @click="reportStatus = null; reportMessage = ''" 
                                class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800"
                        >
                            ✕ Fechar
                        </button>
                    </div>
                    <button v-else-if="reportStatus === 'error'" 
                            @click="reportStatus = null; reportMessage = ''"
                            class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800"
                    >
                        ✕ Fechar
                    </button>
                </div>

                <!-- Flash Messages do Laravel -->
                <div v-if="flashMessage" class="p-4 mb-6 text-green-800 bg-green-200 rounded">
                    {{ flashMessage }}
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap gap-4 mb-6 items-end">
                    <div class="flex flex-col w-full sm:w-auto">
                        <label class="text-xs text-transparent mb-1 hidden sm:block">&nbsp;</label>
                        <input v-model="search" @keyup.enter="filter" type="text" placeholder="Buscar por Nome ou CPF/CNPJ" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" />
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xs text-gray-600 mb-1 font-medium">Data de Cadastro Inicio</label>
                        <input v-model="dateStart" type="date" class="border-gray-300 rounded-md shadow-sm" />
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xs text-gray-600 mb-1 font-medium">Data de Cadastro Fim</label>
                        <input v-model="dateEnd" type="date" class="border-gray-300 rounded-md shadow-sm" />
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xs text-gray-600 mb-1 font-medium">Status</label>
                        <select v-model="ativo" class="border-gray-300 rounded-md shadow-sm">
                            <option value="">Todos</option>
                            <option value="sim">Ativo (Sim)</option>
                            <option value="nao">Inativo (Não)</option>
                        </select>
                    </div>
                    <button @click="filter" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">Buscar</button>
                    <button @click="clearFilter" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Limpar</button>
                </div>

                <!-- Tabela -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-200 rounded-lg">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">ID</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Nome</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Email</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Cpf/Cnpj</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Status</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="cliente in clientes.data" :key="cliente.id">
                                        <td class="px-4 py-2 border-b">{{ cliente.id }}</td>
                                        <td class="px-4 py-2 border-b">{{ cliente.nome }}</td>
                                        <td class="px-4 py-2 border-b">{{ cliente.email }}</td>
                                        <td class="px-4 py-2 border-b">{{ cliente.cpf_cnpj }}</td>
                                        <td class="px-4 py-2 border-b">
                                            <span :class="cliente.ativo === 'sim' ? 'text-green-600' : 'text-red-600'" class="font-medium">
                                                {{ cliente.ativo === 'sim' ? 'Sim' : 'Não' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border-b">
                                            <button @click="editCliente(cliente)" class="px-2 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600 mr-1">
                                                Edit
                                            </button>
                                            <button @click="deleteCliente(cliente)" class="px-2 py-1 text-sm text-white bg-red-500 rounded hover:bg-red-600">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="clientes.data.length === 0">
                                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                            Nenhum cliente encontrado
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <!-- Paginação -->
                            <div class="flex flex-col sm:flex-row items-center justify-between mt-4">
                                <div class="text-sm text-gray-500 mb-2 sm:mb-0">
                                    Exibindo {{ clientes.from || 0 }} a {{ clientes.to || 0 }} de {{ clientes.total }} resultados
                                </div>
                                <div class="flex gap-2 items-center">
                                    <button v-for="link in clientes.links" :key="link.label" 
                                            v-html="link.label"
                                            :disabled="!link.url" 
                                            @click="link.url && router.visit(link.url)"
                                            class="px-3 py-1 border rounded"
                                            :class="{ 'bg-blue-500 text-white': link.active, 'opacity-50 cursor-not-allowed': !link.url }" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>