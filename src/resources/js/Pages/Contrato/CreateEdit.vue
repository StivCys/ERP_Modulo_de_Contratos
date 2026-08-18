<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import axios from 'axios'

const search = ref('')
const resultados = ref([])
const showDropdown = ref(false)
const loading = ref(false)

let timeout = null

watch(search, (value) => {
    clearTimeout(timeout)

    timeout = setTimeout(async () => {
        if (!value) {
            resultados.value = []
            return
        }

        loading.value = true

        try {
            const res = await axios.get('/clientes/search', {
                params: { search: value }
            })

            resultados.value = res.data
        } finally {
            loading.value = false
        }
    }, 300)
})

const selectCliente = (cliente) => {
    form.cliente_id = cliente.id
    search.value = `${cliente.nome} (${cliente.cpf_cnpj})`
    showDropdown.value = false
}

const historicoAberto = ref(false)

const props = defineProps({
    contrato: Object,
    clientes: Array,
    servicos: Array,
    regras: Array,
    historico: { type: Array, default: () => [] },
})

const isEditDisabled = computed(() => {
    return props.contrato?.status === 'cancelado'
})

const formatDateTime = (date) => {
    if (!date) return ''
    
    const d = new Date(date)
    
    const pad = (n) => String(n).padStart(2, '0')
    
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
}

const form = useForm({
    id: props.contrato?.id ?? null,
    cliente_id: props.contrato?.cliente_id ?? '',
    data_inicio: props.contrato?.data_inicio ?? '',
    data_fim: props.contrato?.data_fim ?? '',
    status: props.contrato?.status ?? 'ativo',
    items: props.contrato?.items?.map(i => ({
        id: i.id,
        servico_id: i.servico_id,
        quantidade: i.quantidade,
        valor_unitario: i.valor_unitario,
        created_at: formatDateTime(i.created_at),
        updated_at: formatDateTime(i.updated_at),
    })) ?? []
})

const addServico = () => {
    if(!isEditDisabled.value) {
        form.items.push({ servico_id: '', quantidade: 1, valor_unitario: 0 })
    }
}

const removeServico = (index) => {
    if(!isEditDisabled.value) {
        form.items.splice(index, 1)
    }
}

const computedSimulation = computed(() => {
    let base = 0;
    
    // Calcula Base
    form.items.forEach(item => {
        const servico = props.servicos.find(s => s.id === item.servico_id)
        if(servico) {
            base += servico.valor * item.quantidade;
        }
    })
    
    let finalValue = base;
    let detalhes = [];

    // processa regras
    if (props.regras && props.regras.length > 0) {
        props.regras.forEach(regra => {
            const params = typeof regra.parametros === 'string' ? JSON.parse(regra.parametros) : regra.parametros;
            
            switch (regra.tipo_regra) {
                case 'quantidade_servicos':
                    if (form.items.length >= (params.quantidade_minima || 0)) {
                        let desc = finalValue * ((params.desconto_percentual || 0) / 100);
                        finalValue -= desc;
                        detalhes.push({nome: regra.nome, desc: desc.toFixed(2), tipo: 'desconto'});
                    }
                    break;
                case 'desconto_progressivo':
                    let pctTotal = form.items.length * (params.desconto_por_item_percentual || 0);
                    let maxPct = params.desconto_maximo_percentual || 100;
                    let descProg = Math.min(pctTotal, maxPct);
                    if (descProg > 0) {
                        let desc = finalValue * (descProg / 100);
                        finalValue -= desc;
                        detalhes.push({nome: regra.nome, desc: desc.toFixed(2), tipo: 'desconto'});
                    }
                    break;
                case 'servico_especifico':
                    let temServico = form.items.some(i => i.servico_id == params.servico_id);
                    if (temServico) {
                        if (params.acrescimo_fixo && params.acrescimo_fixo > 0) {
                            finalValue += parseFloat(params.acrescimo_fixo);
                            detalhes.push({nome: regra.nome, desc: parseFloat(params.acrescimo_fixo).toFixed(2), tipo: 'acrescimo'});
                        }
                        if (params.desconto_percentual && params.desconto_percentual > 0) {
                            let d2 = finalValue * (params.desconto_percentual / 100);
                            finalValue -= d2;
                            detalhes.push({nome: regra.nome, desc: d2.toFixed(2), tipo: 'desconto'});
                        }
                    }
                    break;
                case 'fidelidade':
                    if (form.data_inicio && params.desconto_percentual > 0) {
                        let start = new Date(form.data_inicio);
                        let end = form.data_fim ? new Date(form.data_fim) : new Date();
                        let months = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
                        if (months >= (params.meses_fidelidade || 1)) {
                            let d3 = finalValue * (params.desconto_percentual / 100);
                            finalValue -= d3;
                            detalhes.push({nome: regra.nome, desc: d3.toFixed(2), tipo: 'desconto'});
                        }
                    }
                    break;
                case 'ticket_minimo':
                    if (finalValue < params.valor_minimo) {
                        let d4 = params.valor_minimo - finalValue;
                        finalValue += d4;
                        detalhes.push({nome: regra.nome, desc: d4.toFixed(2), tipo: 'acrescimo'});
                    }
                    break;
            }
        });
    }

    if (finalValue < 0) finalValue = 0;

    return {
        base: base.toFixed(2),
        final: finalValue.toFixed(2),
        detalhes
    }
})

const desabilitadoMotivo = computed(() => {
    if (isEditDisabled.value) return 'Você não pode editar um contrato cancelado.'
    return null
})

const submit = () => {
    if (isEditDisabled.value) return;

    if (props.contrato) {
        form.put(route('contrato.update', props.contrato.id))
    } else {
        form.post(route('contrato.store'))
    }
}

const back = () => {
    router.visit(route('contrato.index'))
}

console.log(props.clientes);
</script>

<template>
    <Head :title="props.contrato ? 'Edit Contract' : 'Create Contract'" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Header movido para dentro do layout principal -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ props.contrato ? 'Editar Contrato' : 'Novo Contrato' }}
                    </h2>
                </div>
                
                <div v-if="desabilitadoMotivo" class="p-4 mb-4 text-red-800 bg-red-200 rounded font-semibold">
                    {{ desabilitadoMotivo }}
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <!-- Dados do Contrato -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-b pb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Cliente</label>
                                    <!-- EDIT -->
                                    <input
                                        v-if="props.contrato"
                                        type="text"
                                        class="mt-1 block w-full border rounded bg-gray-100"
                                        :value="props.contrato.cliente?.nome + ' (' + props.contrato.cliente?.cpf_cnpj + ')'"
                                        disabled
                                    />

                                    <div v-if="!props.contrato" class="relative">
                                    <input
                                        v-model="search"
                                        @focus="showDropdown = true"
                                        type="text"
                                        placeholder="Buscar cliente..."
                                        class="mt-1 block w-full border rounded"
                                    />

                                    <div v-if="showDropdown" class="absolute z-10 w-full bg-white border rounded mt-1 shadow max-h-60 overflow-y-auto">
                                        
                                        <div v-if="loading" class="p-2 text-gray-500">
                                            Buscando...
                                        </div>

                                        <div
                                            v-for="c in resultados"
                                            :key="c.id"
                                            @click="selectCliente(c)"
                                            class="p-2 hover:bg-gray-100 cursor-pointer"
                                        >
                                            {{ c.nome }} ({{ c.cpf_cnpj }})
                                        </div>

                                        <div v-if="!loading && resultados.length === 0" class="p-2 text-gray-500">
                                            Nenhum cliente encontrado
                                        </div>
                                    </div>
                                </div>
                                    <div v-if="form.errors.cliente_id" class="text-red-500 text-sm mt-1">{{ form.errors.cliente_id }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select v-model="form.status" class="mt-1 block w-full border rounded" required :disabled="isEditDisabled">
                                        <option value="ativo">Ativo</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                    <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Data de Início</label>
                                    <input v-model="form.data_inicio" type="date" class="mt-1 block w-full border rounded" required :disabled="isEditDisabled"/>
                                    <div v-if="form.errors.data_inicio" class="text-red-500 text-sm mt-1">{{ form.errors.data_inicio }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Data de Término (Opcional)</label>
                                    <input v-model="form.data_fim" type="date" class="mt-1 block w-full border rounded" :disabled="isEditDisabled" />
                                    <div v-if="form.errors.data_fim" class="text-red-500 text-sm mt-1">{{ form.errors.data_fim }}</div>
                                </div>
                            </div>

                            <!-- Itens do Contrato -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Serviços Contratados</h3>
                                <p class="text-sm text-gray-500 mb-4">Gerencie os serviços. Regras de alteração de preço (ex: Desconto Progressivo) serão calculadas automaticamente no valor final.</p>
                                
                                <div v-for="(item, index) in form.items" :key="index" class="flex gap-4 items-end mb-4 bg-gray-50 p-4 rounded border">
                                    <div class="flex-grow">
                                        <label class="block text-sm font-medium text-gray-700">Serviço</label>
                                        <select v-model="item.servico_id" class="mt-1 block w-full border rounded" required :disabled="isEditDisabled">
                                            <option value="" disabled>Selecione o serviço</option>
                                            <option v-for="s in servicos" :key="s.id" :value="s.id">{{ s.nome }} - R$ {{ s.valor }}</option>
                                        </select>
                                    </div>
                                    <div class="w-32">
                                        <label class="block text-sm font-medium text-gray-700">Qtd.</label>
                                        <input v-model="item.quantidade" type="number" min="1" class="mt-1 block w-full border rounded" required :disabled="isEditDisabled"/>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Adicionado em</label>
                                        <input v-model="item.created_at" type="datetime-local" class="mt-1 block w-full border rounded" readonly="true" />
                                    </div>
                                    <div v-if="!isEditDisabled">
                                        <button type="button" @click="removeServico(index)" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                            X
                                        </button>
                                    </div>
                                </div>

                                <button v-if="!isEditDisabled" type="button" @click="addServico" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 text-sm">
                                    + Adicionar Serviço
                                </button>
                                
                                <div v-if="form.errors.items" class="mt-2 text-sm text-red-600">
                                    É necessário ter pelo menos um serviço.
                                </div>
                            </div>
                            
                            <!-- Totais Simulados -->
                            <div class="bg-blue-50 p-4 rounded border border-blue-200 mt-6 text-right transition-all">
                                <h4 class="text-md text-gray-700">Soma Base: R$ {{ computedSimulation.base }}</h4>
                                
                                <div v-if="computedSimulation.detalhes.length > 0" class="my-2 border-t border-b border-blue-200 py-2">
                                    <p class="text-sm font-semibold text-blue-800 mb-1">Regras Aplicadas:</p>
                                    <div v-for="(rule, idx) in computedSimulation.detalhes" :key="idx" class="text-sm">
                                        <span v-if="rule.tipo === 'desconto'" class="text-green-600">- R$ {{ rule.desc }} ({{ rule.nome }})</span>
                                        <span v-if="rule.tipo === 'acrescimo'" class="text-red-500">+ R$ {{ rule.desc }} ({{ rule.nome }})</span>
                                    </div>
                                </div>
                                
                                <h4 class="text-xl font-bold text-blue-900 mt-2">Valor Simulado Final: R$ {{ computedSimulation.final }}</h4>
                            </div>

                            <div class="flex gap-4 mt-6 pt-6 border-t">
                                <button v-if="!isEditDisabled" type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    {{ props.contrato ? 'Salvar Alterações' : 'Criar Contrato' }}
                                </button>
                                <button type="button" @click="back" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                    Voltar para a Lista
                                </button>
                            </div>
                            <!-- Histórico de Alterações -->
                            <div v-if="props.contrato && historico.length > 0" class="mt-6 border rounded overflow-hidden">
                                <button
                                    type="button"
                                    @click="historicoAberto = !historicoAberto"
                                    class="w-full flex items-center justify-between px-4 py-3 bg-gray-100 hover:bg-gray-200 text-left text-sm font-medium text-gray-700 transition-colors"
                                >
                                    <span>📋 Histórico de Alterações ({{ historico.length }})</span>
                                    <span>{{ historicoAberto ? '▲' : '▼' }}</span>
                                </button>

                                <div v-if="historicoAberto" class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                                    <div
                                        v-for="entrada in historico"
                                        :key="entrada.id"
                                        class="px-4 py-3 text-sm"
                                    >
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1">
                                                <span
                                                    class="inline-block px-2 py-0.5 rounded text-xs font-semibold mr-2"
                                                    :class="{
                                                        'bg-green-100 text-green-700': entrada.acao === 'criado' || entrada.acao === 'item_adicionado',
                                                        'bg-blue-100 text-blue-700': entrada.acao === 'atualizado' || entrada.acao === 'item_atualizado',
                                                        'bg-red-100 text-red-700': entrada.acao === 'excluido' || entrada.acao === 'item_removido',
                                                        'bg-gray-100 text-gray-700': !['criado','item_adicionado','atualizado','item_atualizado','excluido','item_removido'].includes(entrada.acao),
                                                    }"
                                                >{{ entrada.acao }}</span>
                                                <span class="text-gray-800">{{ entrada.descricao }}</span>
                                            </div>
                                            <div class="text-right text-xs text-gray-500 whitespace-nowrap">
                                                <div>{{ entrada.usuario }}</div>
                                                <div>{{ entrada.criado_em }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
