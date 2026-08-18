<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    role: Object,
    permissions: Array,
});

const isEdit = !!props.role;

const form = useForm({
    name: props.role?.name || '',
    permissions: props.role?.permissions?.map(perm => perm.name) || [],
});

const getErrorMessage = (field) => form.errors[field];

const save = () => {
    if (isEdit) {
        form.put(route('roles.update', props.role.id));
    } else {
        form.post(route('roles.store'));
    }
};

const togglePermission = (permName) => {
    const index = form.permissions.indexOf(permName);
    if (index === -1) {
        form.permissions.push(permName);
    } else {
        form.permissions.splice(index, 1);
    }
};

const selectAll = () => {
    if (form.permissions.length === props.permissions.length) {
        form.permissions = [];
    } else {
        form.permissions = props.permissions.map(p => p.name);
    }
};

// Accordeon Logic
const groupedPermissions = computed(() => {
    const groups = {};
    props.permissions.forEach(perm => {
        const parts = perm.name.split('.');
        const topic = parts.length > 1 ? parts[0] : 'geral';
        const action = parts.length > 1 ? parts.slice(1).join('.') : perm.name;
        
        if (!groups[topic]) groups[topic] = [];
        groups[topic].push({ id: perm.id, name: perm.name, actionLabel: action });
    });
    return groups;
});

const openTabs = ref(Object.keys(groupedPermissions.value).reduce((acc, key) => {
    acc[key] = false; // Começa fechado para melhorar a UX
    return acc;
}, {}));

const toggleTab = (topic) => {
    openTabs.value[topic] = !openTabs.value[topic];
};

const selectTopicAll = (topicPermissions) => {
    const allSelected = topicPermissions.every(p => form.permissions.includes(p.name));
    if (allSelected) {
        topicPermissions.forEach(p => {
            const idx = form.permissions.indexOf(p.name);
            if (idx !== -1) form.permissions.splice(idx, 1);
        });
    } else {
        topicPermissions.forEach(p => {
            if (!form.permissions.includes(p.name)) {
                form.permissions.push(p.name);
            }
        });
    }
};

const formatTopicName = (topic) => {
    const map = {
        'users': 'Usuários',
        'roles': 'Perfis de Acesso',
        'cliente': 'Clientes',
        'servico': 'Serviços',
        'contrato': 'Contratos',
        'geral': 'Geral'
    };
    return map[topic] || topic.charAt(0).toUpperCase() + topic.slice(1);
};

const formatActionName = (action) => {
    const map = {
        'view': 'Visualizar',
        'create': 'Criar',
        'edit': 'Editar',
        'delete': 'Excluir',
    };
    return map[action] || action;
};
</script>

<template>
    <Head :title="isEdit ? 'Editar Perfil' : 'Novo Perfil'" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="p-6 bg-white shadow-sm sm:rounded-lg">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold">{{ isEdit ? 'Editar Perfil' : 'Novo Perfil' }}</h2>
                        <a :href="route('roles.index')" class="text-gray-600 hover:text-gray-900">Voltar</a>
                    </div>
                    
                    <form @submit.prevent="save">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Nome do Perfil</label>
                            <input v-model="form.name" type="text" :disabled="isEdit && form.name === 'admin'" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            <p v-if="getErrorMessage('name')" class="mt-1 text-sm text-red-500">{{ getErrorMessage('name') }}</p>
                            <p v-if="isEdit && role.name === 'admin'" class="mt-1 text-xs text-gray-500">O nome do perfil administrador não pode ser alterado.</p>
                        </div>
                        
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <label class="block text-sm font-medium text-gray-700">Permissões do Sistema</label>
                                <button type="button" @click="selectAll" class="text-sm px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded transition-colors">
                                    {{ form.permissions.length === permissions.length ? 'Desmarcar Todas' : 'Selecionar Todas' }}
                                </button>
                            </div>
                            
                            <div class="border rounded-md shadow-sm overflow-hidden bg-white">
                                <p v-if="permissions.length === 0" class="p-4 text-sm text-gray-500 text-center">Nenhuma permissão cadastrada no sistema.</p>
                                
                                <div v-for="(topicPermissions, topic) in groupedPermissions" :key="topic" class="border-b last:border-b-0 border-gray-200">
                                    <!-- Header do Accordion -->
                                    <div class="flex items-center justify-between bg-gray-50 p-4 cursor-pointer hover:bg-gray-100 transition-colors" @click="toggleTab(topic)">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-gray-700">{{ formatTopicName(topic) }}</span>
                                            <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full">
                                                {{ topicPermissions.filter(p => form.permissions.includes(p.name)).length }} / {{ topicPermissions.length }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <!-- Botão para selecionar todas daquele tópico (prevent propagation para não fechar accordion) -->
                                            <button type="button" @click.stop="selectTopicAll(topicPermissions)" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                                {{ topicPermissions.every(p => form.permissions.includes(p.name)) ? 'Desmarcar' : 'Selecionar Tudo' }}
                                            </button>
                                            
                                            <!-- Ícone de seta -->
                                            <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="{ 'rotate-180': openTabs[topic] }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Conteúdo do Accordion -->
                                    <div v-show="openTabs[topic]" class="p-4 bg-white grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                                        <label v-for="perm in topicPermissions" :key="perm.id" class="inline-flex items-center p-2 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200 transition-colors cursor-pointer">
                                            <input type="checkbox" :value="perm.name" :checked="form.permissions.includes(perm.name)" @change="togglePermission(perm.name)" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" />
                                            <span class="ml-2 text-sm text-gray-600 capitalize">{{ formatActionName(perm.actionLabel) }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <p v-if="getErrorMessage('permissions')" class="mt-2 text-sm text-red-500">{{ getErrorMessage('permissions') }}</p>
                        </div>

                        <div class="flex justify-end pt-4 border-t">
                            <a :href="route('roles.index')" class="px-4 py-2 mr-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                                Cancelar
                            </a>
                            <button type="submit" :disabled="form.processing" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600 disabled:opacity-50">
                                {{ form.processing ? 'Salvando...' : 'Salvar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
