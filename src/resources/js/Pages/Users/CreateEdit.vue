<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
    roles: Array,
});

const isEdit = !!props.user;

const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    password: '',
    roles: props.user?.roles?.map(role => role.name) || [],
});

const getErrorMessage = (field) => form.errors[field];

const save = () => {
    if (isEdit) {
        form.put(route('users.update', props.user.id));
    } else {
        form.post(route('users.store'));
    }
};

const toggleRole = (roleName) => {
    const index = form.roles.indexOf(roleName);
    if (index === -1) {
        form.roles.push(roleName);
    } else {
        form.roles.splice(index, 1);
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Editar Usuário' : 'Novo Usuário'" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="p-6 bg-white shadow-sm sm:rounded-lg">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold">{{ isEdit ? 'Editar Usuário' : 'Novo Usuário' }}</h2>
                        <a :href="route('users.index')" class="text-gray-600 hover:text-gray-900">Voltar</a>
                    </div>
                    
                    <form @submit.prevent="save">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Nome</label>
                            <input v-model="form.name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                            <p v-if="getErrorMessage('name')" class="mt-1 text-sm text-red-500">{{ getErrorMessage('name') }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input v-model="form.email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                            <p v-if="getErrorMessage('email')" class="mt-1 text-sm text-red-500">{{ getErrorMessage('email') }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Senha <span v-if="isEdit" class="text-xs text-gray-500">(Deixe em branco para manter a atual)</span></label>
                            <input v-model="form.password" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                            <p v-if="getErrorMessage('password')" class="mt-1 text-sm text-red-500">{{ getErrorMessage('password') }}</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Perfis (Roles)</label>
                            <div class="flex flex-wrap gap-2">
                                <label v-for="role in roles" :key="role.id" class="inline-flex items-center">
                                    <input type="checkbox" :value="role.name" :checked="form.roles.includes(role.name)" @change="toggleRole(role.name)" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" />
                                    <span class="ml-2 text-sm text-gray-600">{{ role.name }}</span>
                                </label>
                            </div>
                            <p v-if="getErrorMessage('roles')" class="mt-1 text-sm text-red-500">{{ getErrorMessage('roles') }}</p>
                            <p v-if="roles.length === 0" class="mt-1 text-sm text-gray-500">Nenhum perfil cadastrado.</p>
                        </div>

                        <div class="flex justify-end pt-4 border-t">
                            <a :href="route('users.index')" class="px-4 py-2 mr-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
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
