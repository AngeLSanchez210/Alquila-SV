<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import axios from 'axios';


interface User {
    id: number;
    name: string;
    email: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];


const users = ref<User[]>([]);

onMounted(async () => {
    const response = await axios.get('/users');
    users.value = response.data;
});

const deleteUser = async (id: number) => {
    if (confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
        await axios.delete(`/users/${id}`);
        users.value = users.value.filter(user => user.id !== id);
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Usuarios</h2>
                    <button @click="router.visit('/users/create')" class="bg-blue-600 text-white px-4 py-2 rounded">Agregar Usuario</button>
                </div>
                <table class="min-w-full border border-gray-300 rounded-lg text-gray-800">
    <thead class="bg-gray-200 text-gray-700">
        <tr>
            <th class="px-4 py-2 text-left border border-gray-300">ID</th>
            <th class="px-4 py-2 text-left border border-gray-300">Nombre</th>
            <th class="px-4 py-2 text-left border border-gray-300">Correo</th>
            <th class="px-4 py-2 text-left border border-gray-300">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="user in users" :key="user.id" class="border-t border-gray-300 odd:bg-gray-100 even:bg-white">
            <td class="px-4 py-2 border border-gray-300">{{ user.id }}</td>
            <td class="px-4 py-2 border border-gray-300">{{ user.name }}</td>
            <td class="px-4 py-2 border border-gray-300">{{ user.email }}</td>
            <td class="px-4 py-2 border border-gray-300">
                <button @click="router.visit(`/users/${user.id}/edit`)" class="text-blue-600 hover:text-blue-800">Editar</button>
                <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-800 ml-2">Eliminar</button>
            </td>
        </tr>
    </tbody>
</table>

            </div>
        </div>
    </AppLayout>
</template>
