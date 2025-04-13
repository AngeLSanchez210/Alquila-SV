<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import Swal from 'sweetalert2';
import AppLayout from '@/Layouts/AppLayout.vue';

interface User {
  id: number;
  name: string;
  email: string;
  direccion?: string;
  telefono?: string;
  role: string;
}

const users = ref<User[]>([]);

onMounted(async () => {
  const response = await axios.get('/users');
  users.value = response.data;
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
</script>

<template>
  <Head title="Usuarios" />

  <AppLayout>
    <div class="p-8 space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-semibold text-white">Usuarios</h1>
        <button
          @click="router.visit('/users/create')"
          class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium shadow"
        >
          + Crear Usuario
        </button>
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
              v-for="user in users"
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
            <tr v-if="users.length === 0">
              <td colspan="4" class="text-center py-6 text-gray-400">
                No hay usuarios disponibles.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
