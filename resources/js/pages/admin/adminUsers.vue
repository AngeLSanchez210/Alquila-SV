<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import Swal from 'sweetalert2';
import AppLayout from '@/layouts/AppLayout.vue';

interface User {
  id: number;
  name: string;
  email: string;
  direccion?: string;
  telefono?: string;
  role: string;
}

const users = ref<User[]>([]);
const searchTerm = ref('');

onMounted(async () => {
  const response = await axios.get('/users');
  users.value = response.data;
});

const filteredUsers = computed(() => {
  if (!searchTerm.value) return users.value;
  const term = searchTerm.value.toLowerCase();
  return users.value.filter(user =>
    user.name.toLowerCase().includes(term) ||
    user.email.toLowerCase().includes(term) ||
    (user.role && user.role.toLowerCase().includes(term))
  );
});

const deleteUser = async (id: number) => {
  const result = await Swal.fire({
    title: '¿Estás seguro?',
    text: 'Esta acción eliminará al usuario de forma permanente.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`/users/${id}`);
      users.value = users.value.filter(user => user.id !== id);
      await Swal.fire('¡Eliminado!', 'El usuario fue eliminado correctamente.', 'success');
    } catch (error) {
      Swal.fire('Error', 'No se pudo eliminar el usuario.', 'error');
    }
  }
};

const openEditModal = (user: User) => {
  Swal.fire({
    title: 'Editar Usuario',
    html: `
      <input id="swal-name" class="swal2-input" placeholder="Nombre" value="${user.name}">
      <input id="swal-email" class="swal2-input" placeholder="Correo" value="${user.email}">
      <input id="swal-direccion" class="swal2-input" placeholder="Dirección" value="${user.direccion || ''}">
      <input id="swal-telefono" class="swal2-input" placeholder="Teléfono" value="${user.telefono || ''}">
      <input id="swal-role" class="swal2-input" placeholder="Rol" value="${user.role}">
    `,
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: 'Guardar',
    cancelButtonText: 'Cancelar',
    preConfirm: async () => {
      const name = (document.getElementById('swal-name') as HTMLInputElement).value;
      const email = (document.getElementById('swal-email') as HTMLInputElement).value;
      const direccion = (document.getElementById('swal-direccion') as HTMLInputElement).value;
      const telefono = (document.getElementById('swal-telefono') as HTMLInputElement).value;
      const role = (document.getElementById('swal-role') as HTMLInputElement).value;

      try {
        await axios.put(`/users/${user.id}`, {
          name, email, direccion, telefono, role
        });
        const response = await axios.get('/users');
        users.value = response.data;
        Swal.fire('Actualizado', 'El usuario fue editado correctamente.', 'success');
      } catch (error: any) {
        Swal.fire('Error', error?.response?.data?.message || 'Error al actualizar.', 'error');
      }
    },
  });
};

const openCreateModal = () => {
  Swal.fire({
    title: 'Crear Usuario',
    html: `
      <input id="swal-name" class="swal2-input" placeholder="Nombre">
      <input id="swal-email" class="swal2-input" placeholder="Correo">
      <input id="swal-password" type="password" class="swal2-input" placeholder="Contraseña">
      <input id="swal-direccion" class="swal2-input" placeholder="Dirección">
      <input id="swal-telefono" class="swal2-input" placeholder="Teléfono">
      <input id="swal-role" class="swal2-input" placeholder="Rol">
    `,
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: 'Guardar',
    cancelButtonText: 'Cancelar',
    preConfirm: async () => {
      const name = (document.getElementById('swal-name') as HTMLInputElement).value;
      const email = (document.getElementById('swal-email') as HTMLInputElement).value;
      const password = (document.getElementById('swal-password') as HTMLInputElement).value;
      const direccion = (document.getElementById('swal-direccion') as HTMLInputElement).value;
      const telefono = (document.getElementById('swal-telefono') as HTMLInputElement).value;
      const role = (document.getElementById('swal-role') as HTMLInputElement).value;

      try {
        await axios.post('/users', { name, email, password, direccion, telefono, role });
        const response = await axios.get('/users');
        users.value = response.data;
        Swal.fire('Creado', 'El usuario fue creado correctamente.', 'success');
      } catch (error: any) {
        Swal.fire('Error', error?.response?.data?.message || 'Error al crear el usuario.', 'error');
      }
    },
  });
};
</script>

<template>
  <AppLayout>
    <div class="p-8 bg-gray-100 min-h-screen space-y-8">

      <div class="flex justify-between items-center">
        <h1 class="text-4xl font-bold text-gray-800">Panel de Administración</h1>
        <button
          @click="openCreateModal"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-semibold shadow transition"
        >
          + Crear Usuario
        </button>
      </div>

      <!-- Búsqueda -->
      <div class="relative max-w-md">
        <input
          v-model="searchTerm"
          type="search"
          placeholder="Buscar usuarios por nombre, correo o rol..."
          class="w-full p-3 pl-10 rounded-lg border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-800 placeholder-gray-400"
        />
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>

      <!-- Tabla -->
      <div class="overflow-x-auto">
        <div class="bg-white rounded-2xl shadow-md p-6">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left font-bold text-gray-700">ID</th>
                <th class="px-6 py-3 text-left font-bold text-gray-700">Nombre</th>
                <th class="px-6 py-3 text-left font-bold text-gray-700">Correo</th>
                <th class="px-6 py-3 text-left font-bold text-gray-700">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50 transition duration-200 text-black">
                <td class="px-6 py-4">{{ user.id }}</td>
                <td class="px-6 py-4">{{ user.name }}</td>
                <td class="px-6 py-4">{{ user.email }}</td>
                <td class="px-6 py-4 space-x-3">
                  <button
                    @click="openEditModal(user)"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition"
                  >
                    Editar
                  </button>
                  <button
                    @click="deleteUser(user.id)"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition"
                  >
                    Eliminar
                  </button>
                </td>
              </tr>
              <tr v-if="filteredUsers.length === 0">
                <td colspan="4" class="text-center py-6 text-gray-400">
                  No hay usuarios disponibles{{ searchTerm ? ' con esa búsqueda' : '' }}.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </AppLayout>
</template>
