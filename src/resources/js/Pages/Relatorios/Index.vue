<script setup>
import { ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({ relatorios: Array });

const selectedIds = ref([]);
const page = usePage();
const flashMessage = ref(page.props.flash?.success);

// Watch flash messages
import { watch } from 'vue';
watch(() => page.props.flash?.success, (msg) => {
    if (msg) {
        flashMessage.value = msg;
        setTimeout(() => flashMessage.value = null, 5000);
    }
}, { immediate: true });

const toggleSelectAll = () => {
    selectedIds.value = selectedIds.value.length === props.relatorios.length 
        ? [] 
        : props.relatorios.map(r => r.id);
};

const toggleSelect = (id) => {
    selectedIds.value = selectedIds.value.includes(id)
        ? selectedIds.value.filter(i => i !== id)
        : [...selectedIds.value, id];
};

const deleteSelected = () => {
    if (selectedIds.value.length === 0) return;
    
    if (confirm(`Tem certeza que deseja excluir ${selectedIds.value.length} relatório(s)?\nEssa ação não pode ser desfeita.`)) {
        router.delete(route('relatorios.destroy-multiple'), {
            data: { ids: selectedIds.value },
            preserveScroll: true,
            onSuccess: () => {
                selectedIds.value = [];
            }
        });
    }
};
</script>

<template>
    <Head title="Meus Relatórios" />
    <AuthenticatedLayout>
        <div class="py-2 max-w-8xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">📊 Meus Relatórios</h2>
                
                <button 
                    v-if="selectedIds.length > 0"
                    @click="deleteSelected"
                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition flex items-center gap-2"
                >
                    🗑️ Excluir Selecionados ({{ selectedIds.length }})
                </button>
            </div>

            <div v-if="flashMessage" class="p-4 mb-6 text-green-800 bg-green-200 rounded">
                {{ flashMessage }}
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 w-12">
                                <input 
                                    type="checkbox" 
                                    :checked="selectedIds.length === relatorios.length && relatorios.length > 0"
                                    @change="toggleSelectAll"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gerado em</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="r in relatorios" :key="r.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input 
                                    type="checkbox" 
                                    :checked="selectedIds.includes(r.id)"
                                    @change="toggleSelect(r.id)"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ r.nome }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.created_at }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="{
                                    'bg-yellow-100 text-yellow-800': ['pending', 'processing'].includes(r.status),
                                    'bg-green-100 text-green-800': r.status === 'completed',
                                    'bg-red-100 text-red-800': r.status === 'failed'
                                }" class="px-2 py-1 text-xs rounded-full">
                                    {{ { pending: 'Aguardando', processing: 'Processando', completed: 'Pronto', failed: 'Falhou' }[r.status] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a v-if="r.download_url" :href="r.download_url" class="text-blue-600 hover:underline font-medium">
                                    📥 Baixar
                                </a>
                                <span v-else-if="r.status === 'failed'" class="text-red-500 text-xs" :title="r.error">
                                    ❌ Ver logs
                                </span>
                                <span v-else class="text-gray-400 text-xs">⏳ Aguarde...</span>
                            </td>
                        </tr>
                        <tr v-if="relatorios.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Nenhum relatório gerado ainda.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>