<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import axios from 'axios';


interface User {
    id: number;
    name: string;
    email: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Principal', href: '/principal' },
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
    <Head title="Principal" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">
                
              
  


            </div>
        </div>
    </AppLayout>
</template>
