<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';

const props = defineProps({ users: Object, filters: Object });
const search = ref(props.filters?.search || '');

const page = usePage();
const flashMessage = ref(page.props.flash?.success);

watch(() => page.props.flash?.success, (msg) => {
    if (msg) {
        flashMessage.value = msg;
        setTimeout(() => flashMessage.value = null, 5000);
    }
}, { immediate: true });

const filter = () => {
    router.get(route('users.index'), {
        search: search.value,
    }, { preserveState: true, replace: true });
};

const clearFilter = () => {
    search.value = '';
    filter();
};

const editUser = (user) => router.get(route('users.edit', user.id));
const createUser = () => router.get(route('users.create'));
const deleteUser = (user) => {
    if (confirm('Tem certeza que deseja excluir?')) router.delete(route('users.destroy', user.id));
};
</script>

<template>
    <Head title="Usuários" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Usuários
                    </h2>
                    
                    <button @click="createUser" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                        Novo Usuário
                    </button>
                </div>

                <div v-if="flashMessage" class="p-4 mb-6 text-green-800 bg-green-200 rounded">
                    {{ flashMessage }}
                </div>
                
                <div v-if="page.props.errors.error" class="p-4 mb-6 text-red-800 bg-red-200 rounded">
                    {{ page.props.errors.error }}
                </div>

                <div class="flex flex-wrap gap-4 mb-6 items-end">
                    <div class="flex flex-col w-full sm:w-auto">
                        <label class="text-xs text-transparent mb-1 hidden sm:block">&nbsp;</label>
                        <input v-model="search" @keyup.enter="filter" type="text" placeholder="Buscar por Nome ou Email" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" />
                    </div>
                    <button @click="filter" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">Buscar</button>
                    <button @click="clearFilter" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Limpar</button>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-200 rounded-lg">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">ID</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Nome</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Email</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Perfis</th>
                                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 border-b">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users.data" :key="user.id">
                                        <td class="px-4 py-2 border-b">{{ user.id }}</td>
                                        <td class="px-4 py-2 border-b">{{ user.name }}</td>
                                        <td class="px-4 py-2 border-b">{{ user.email }}</td>
                                        <td class="px-4 py-2 border-b">
                                            <span v-for="role in user.roles" :key="role.id" class="px-2 py-1 text-xs text-blue-800 bg-blue-100 rounded-full mr-1">
                                                {{ role.name }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border-b">
                                            <button @click="editUser(user)" class="px-2 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600 mr-1">
                                                Edit
                                            </button>
                                            <button @click="deleteUser(user)" class="px-2 py-1 text-sm text-white bg-red-500 rounded hover:bg-red-600">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="users.data.length === 0">
                                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                                            Nenhum usuário encontrado
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div class="flex gap-2 mt-4 items-center justify-center">
                                <button v-for="link in users.links" :key="link.label" 
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
    </AuthenticatedLayout>
</template>
