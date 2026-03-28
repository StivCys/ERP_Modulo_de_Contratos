<template>
    <Head :title="props.regra ? 'Editar Regra' : 'Nova Regra'" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ props.regra ? 'Editar Regra Adicional' : 'Nova Regra Adicional' }}
                    </h2>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nome da Regra</label>
                                    <input v-model="form.nome" type="text" class="mt-1 block w-full border rounded" required placeholder="Ex: Cupom Natal 10%" />
                                    <div v-if="form.errors.nome" class="text-red-500 text-sm mt-1">{{ form.errors.nome }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Ativo</label>
                                    <select v-model="form.ativo" class="mt-1 block w-full border rounded" required>
                                        <option :value="true">Sim</option>
                                        <option :value="false">Não</option>
                                    </select>
                                    <div v-if="form.errors.ativo" class="text-red-500 text-sm mt-1">{{ form.errors.ativo }}</div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo de Estratégia</label>
                                <select v-model="form.tipo_regra" class="mt-1 block w-full border rounded" required>
                                    <option value="" disabled>Selecione uma mecânica...</option>
                                    <option value="quantidade_servicos">Desconto por Quantidade (Se X ou +, ganha Y%)</option>
                                    <option value="desconto_progressivo">Desconto Progressivo (Ganha Y% * Qtd, teto Z%)</option>
                                    <option value="servico_especifico">Acrésc/Desc por Serviço (Ex: +R$50 se incluir Setup)</option>
                                    <option value="fidelidade">Fidelidade (Desconto após X meses de contrato ativo)</option>
                                </select>
                                <div v-if="form.errors.tipo_regra" class="text-red-500 text-sm mt-1">{{ form.errors.tipo_regra }}</div>
                            </div>

                            <hr class="my-4" />

                            <h3 class="text-lg font-medium text-gray-900">Parâmetros da Regra</h3>

                            <!-- Params view dynamically based on selected tipo_regra -->
                            <div v-if="form.tipo_regra === 'quantidade_servicos'" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Quantidade Mínima de Serviços (Qtd)</label>
                                    <input v-model="formParams.quantidade_minima" type="number" min="1" class="mt-1 block w-full border rounded" required />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Desconto Percentual (%)</label>
                                    <input v-model="formParams.desconto_percentual" type="number" step="0.01" min="0" class="mt-1 block w-full border rounded" required />
                                </div>
                            </div>

                            <div v-if="form.tipo_regra === 'desconto_progressivo'" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Desconto por Item (%)</label>
                                    <input v-model="formParams.desconto_por_item_percentual" type="number" step="0.01" min="0" class="mt-1 block w-full border rounded" required />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Teto Máximo do Desconto (%)</label>
                                    <input v-model="formParams.desconto_maximo_percentual" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full border rounded" required />
                                </div>
                                <p class="text-sm text-gray-500 col-span-2">Exemplo: Se desconto por item for 2% e Teto for 10%. 3 serviços = 6%. 6 serviços = 10% (teto).</p>
                            </div>

                            <div v-if="form.tipo_regra === 'servico_especifico'" class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">ID do Serviço Específico</label>
                                    <input v-model="formParams.servico_id" type="number" min="1" class="mt-1 block w-full border rounded" required />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Acréscimo Fixo (R$)</label>
                                    <input v-model="formParams.acrescimo_fixo" type="number" step="0.01" min="0" class="mt-1 block w-full border rounded" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Ou Desconto Percentual (%)</label>
                                    <input v-model="formParams.desconto_percentual" type="number" step="0.01" min="0" class="mt-1 block w-full border rounded" />
                                </div>
                            </div>

                            <div v-if="form.tipo_regra === 'fidelidade'" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Meses de Fidelidade (Ex: 12)</label>
                                    <input v-model="formParams.meses_fidelidade" type="number" min="1" class="mt-1 block w-full border rounded" required />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Desconto Percentual (%)</label>
                                    <input v-model="formParams.desconto_percentual" type="number" step="0.01" min="0" class="mt-1 block w-full border rounded" required />
                                </div>
                            </div>

                            <div class="flex gap-4 pt-4 border-t">
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600" :disabled="form.processing">
                                    Salvar Regra
                                </button>
                                <Link href="/regra-adicional" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                    Cancelar
                                </Link>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';

const props = defineProps({
    regra: Object,
});

// JSON parameters model
const formParams = reactive(props.regra ? props.regra.parametros : {});

const form = useForm({
    nome: props.regra ? props.regra.nome : '',
    tipo_regra: props.regra ? props.regra.tipo_regra : '',
    parametros: formParams,
    ativo: props.regra ? props.regra.ativo : true,
});

// Clear params when completely changing rule type
watch(() => form.tipo_regra, (newVal, oldVal) => {
    if (oldVal && newVal !== oldVal && !props.regra) { // only clean if it's changing and it's new
       for (const key in formParams) {
           delete formParams[key];
       }
    }
});

const submit = () => {
    // Sync params
    form.parametros = formParams;
    
    if (props.regra) {
        form.put(`/regra-adicional/${props.regra.id}`);
    } else {
        form.post('/regra-adicional');
    }
};
</script>
