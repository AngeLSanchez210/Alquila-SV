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

      await Swal.fire({
        title: '¡Eliminado!',
        text: 'El usuario fue eliminado correctamente.',
        icon: 'success',
        confirmButtonColor: '#3085d6',
      });
    } catch (error) {
      Swal.fire({
        title: 'Error',
        text: 'No se pudo eliminar el usuario.',
        icon: 'error',
      });
    }
  }
};

const openEditModal = (user: User) => {
  Swal.fire({
    title: 'Editar Usuario',
    html:
      `<input id="swal-name" class="swal2-input" placeholder="Nombre" value="${user.name}">` +
      `<input id="swal-email" class="swal2-input" placeholder="Correo" value="${user.email}">` +
      `<input id="swal-direccion" class="swal2-input" placeholder="Dirección" value="${user.direccion || ''}">` +
      `<input id="swal-telefono" class="swal2-input" placeholder="Teléfono" value="${user.telefono || ''}">` +
      `<input id="swal-role" class="swal2-input" placeholder="Rol" value="${user.role}">`,
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
          name,
          email,
          direccion,
          telefono,
          role,
        });

        const response = await axios.get('/users');
        users.value = response.data;

        Swal.fire('Actualizado', 'El usuario fue editado correctamente.', 'success');
      } catch (error: any) {
        const msg = error?.response?.data?.message ?? 'Error al actualizar el usuario.';
        Swal.fire('Error', msg, 'error');
      }
    },
  });
};


const openCreateModal = () => {
  Swal.fire({
    title: 'Crear Usuario',
    html:
      `<input id="swal-name" class="swal2-input" placeholder="Nombre">` +
      `<input id="swal-email" class="swal2-input" placeholder="Correo">` +
      `<input id="swal-password" type="password" class="swal2-input" placeholder="Contraseña">` +
      `<small style="display: block; margin-top: -10px; margin-bottom: 10px; color: #ccc;">La contraseña se podrá cambiar luego</small>` +
      `<input id="swal-direccion" class="swal2-input" placeholder="Dirección">` +
      `<input id="swal-telefono" class="swal2-input" placeholder="Teléfono">` +
      `<input id="swal-role" class="swal2-input" placeholder="Rol">`,
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
        await axios.post('/users', {
          name,
          email,
          password,
          direccion,
          telefono,
          role,
        });

        const response = await axios.get('/users');
        users.value = response.data;

        Swal.fire('Creado', 'El usuario fue creado correctamente.', 'success');
      } catch (error: any) {
        const msg = error?.response?.data?.message ?? 'Error al crear el usuario.';
        Swal.fire('Error', msg, 'error');
      }
    },
  });
};


</script>

<template>
  <Head title="Usuarios" />

  <AppLayout>
    <div class="p-8 space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-semibold text-white text-center w-full">PANEL DE ADMINISTRACIÓN</h1>
        <button
        @click="openCreateModal"
        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium shadow"
      >
        + Crear Usuario
      </button>

      </div>

      <!-- Componente de búsqueda -->
      <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        <input
          v-model="searchTerm"
          type="search"
          class="bg-white/5 backdrop-blur-md border border-white/10 text-white placeholder-gray-400 text-sm rounded-lg block w-full pl-10 p-2.5 focus:ring-blue-500 focus:border-blue-500"
          placeholder="Buscar usuarios por nombre, correo o rol..."
        />
      </div>

      <div class="overflow-hidden rounded-xl shadow ring-1 ring-white/10 bg-white/5 backdrop-blur-md">
        <table class="min-w-full divide-y divide-white/10 text-sm text-white">
          <thead class="bg-white/10">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold">ID</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Nombre</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Correo</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="user in filteredUsers"
              :key="user.id"
              class="hover:bg-white/5 transition duration-200"
            >
              <td class="px-6 py-4">{{ user.id }}</td>
              <td class="px-6 py-4">{{ user.name }}</td>
              <td class="px-6 py-4">{{ user.email }}</td>
              <td class="px-6 py-4 space-x-3">
                <button
                  @click="openEditModal(user)"
                  class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium"
                >
                  Editar
                </button>
                <button
                  @click="deleteUser(user.id)"
                  class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium"
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
  </AppLayout>
</template>