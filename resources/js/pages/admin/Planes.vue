<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import type { Plan } from '@/types'; // Importa el tipo Plan
import AppLayout from '@/layouts/AppLayout.vue'; // Asegúrate de que la ruta sea correcta

const planes = ref<Plan[]>([]); // Define planes como un estado local
const searchTerm = ref('');

onMounted(async () => {
  try {
    const response = await axios.get('/api/planes'); // Asegúrate de que esta ruta exista
    planes.value = response.data;
  } catch (error) {
    console.error('Error al cargar los planes:', error);
  }
});

const filteredPlanes = computed(() => {
  if (!searchTerm.value) return planes.value;

  const term = searchTerm.value.toLowerCase();
  return planes.value.filter(plan =>
    plan.nombre.toLowerCase().includes(term) ||
    (plan.descripcion && plan.descripcion.toLowerCase().includes(term))
  );
});

const deletePlan = async (id: number) => {
  const result = await Swal.fire({
    title: '¿Estás seguro?',
    text: 'Esta acción eliminará el plan de forma permanente.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`/planes/${id}`);
      planes.value = planes.value.filter(plan => plan.id !== id);

      await Swal.fire({
        title: '¡Eliminado!',
        text: 'El plan fue eliminado correctamente.',
        icon: 'success',
        confirmButtonColor: '#3085d6',
      });
    } catch (error) {
      Swal.fire({
        title: 'Error',
        text: 'No se pudo eliminar el plan.',
        icon: 'error',
      });
    }
  }
};

const openCreateModal = async () => {
  const { value: formValues } = await Swal.fire({
    title: 'Crear Nuevo Plan',
    html:
      `<input id="swal-nombre" class="swal2-input" placeholder="Nombre del plan">` +
      `<textarea id="swal-descripcion" class="swal2-textarea" placeholder="Descripción (separa cada caracteristica por una ' , ')"></textarea>` +
      `<input id="swal-duracion" type="number" class="swal2-input" placeholder="Duración (días)">` +
      `<input id="swal-max-publicaciones" type="number" class="swal2-input" placeholder="Máx. Publicaciones">` +
      `<input id="swal-precio" type="number" class="swal2-input" placeholder="Precio">` +
      `<label style="display: flex; align-items: center; gap: 8px; margin-top: 10px;">` +
      `<input id="swal-destacar" type="checkbox"> Destacar` +
      `</label>`,
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: 'Guardar',
    cancelButtonText: 'Cancelar',
    preConfirm: () => {
      const nombre = (document.getElementById('swal-nombre') as HTMLInputElement).value;
      const descripcion = (document.getElementById('swal-descripcion') as HTMLTextAreaElement).value;
      const duracion = parseInt((document.getElementById('swal-duracion') as HTMLInputElement).value, 10);
      const maxPublicaciones = parseInt((document.getElementById('swal-max-publicaciones') as HTMLInputElement).value, 10);
      const precio = parseFloat((document.getElementById('swal-precio') as HTMLInputElement).value);
      const destacar = (document.getElementById('swal-destacar') as HTMLInputElement).checked;

      if (!nombre || !duracion || !maxPublicaciones) {
        Swal.showValidationMessage('Por favor, completa todos los campos obligatorios.');
        return null;
      }

      return { nombre, descripcion, duracion, max_publicaciones: maxPublicaciones, precio, destacar };
    },
  });

  if (formValues) {
    try {
      await axios.post('/planes', formValues);
      const response = await axios.get('/api/planes'); // Recarga los planes desde el servidor
      planes.value = response.data;

      await Swal.fire({
        title: '¡Creado!',
        text: 'El plan fue creado correctamente.',
        icon: 'success',
        confirmButtonColor: '#3085d6',
      });
    } catch (error) {
      console.error('Error al crear el plan:', error.response?.data?.errors || error);
      Swal.fire({
        title: 'Error',
        text: error.response?.data?.message || 'No se pudo crear el plan.',
        icon: 'error',
      });
    }
  }
};

const openEditModal = async (plan: Plan) => {
  const { value: formValues } = await Swal.fire({
    title: 'Editar Plan',
    html:
      `<input id="swal-nombre" class="swal2-input" placeholder="Nombre del plan" value="${plan.nombre}">` +
      `<textarea id="swal-descripcion" class="swal2-textarea" placeholder="Descripción (separa cada caracteristica por una ' , ')">${plan.descripcion || ''}</textarea>` +
      `<input id="swal-duracion" type="number" class="swal2-input" placeholder="Duración (días)" value="${plan.duracion}">` +
      `<input id="swal-max-publicaciones" type="number" class="swal2-input" placeholder="Máx. Publicaciones" value="${plan.max_publicaciones}">` +
      `<input id="swal-precio" type="number" class="swal2-input" placeholder="Precio" value="${plan.precio}">` +
      `<label style="display: flex; align-items: center; gap: 8px; margin-top: 10px;">` +
      `<input id="swal-destacar" type="checkbox" ${plan.destacar ? 'checked' : ''}> Destacar` +
      `</label>`,
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: 'Guardar',
    cancelButtonText: 'Cancelar',
    preConfirm: () => {
      const nombre = (document.getElementById('swal-nombre') as HTMLInputElement).value;
      const descripcion = (document.getElementById('swal-descripcion') as HTMLTextAreaElement).value;
      const duracion = parseInt((document.getElementById('swal-duracion') as HTMLInputElement).value, 10);
      const maxPublicaciones = parseInt((document.getElementById('swal-max-publicaciones') as HTMLInputElement).value, 10);
      const precio = parseFloat((document.getElementById('swal-precio') as HTMLInputElement).value);
      const destacar = (document.getElementById('swal-destacar') as HTMLInputElement).checked;

      if (!nombre || !duracion || !maxPublicaciones) {
        Swal.showValidationMessage('Por favor, completa todos los campos obligatorios.');
        return null;
      }

      return { nombre, descripcion, duracion, max_publicaciones: maxPublicaciones, precio, destacar };
    },
  });

  if (formValues) {
    try {
      const response = await axios.put(`/planes/${plan.id}`, formValues);
      const updatedPlan = response.data;

      // Actualiza el plan en la lista local
      const index = planes.value.findIndex(p => p.id === plan.id);
      if (index !== -1) {
        planes.value[index] = updatedPlan;
      }

      await Swal.fire({
        title: '¡Actualizado!',
        text: 'El plan fue actualizado correctamente.',
        icon: 'success',
        confirmButtonColor: '#3085d6',
      });
    } catch (error) {
      Swal.fire({
        title: 'Error',
        text: 'No se pudo actualizar el plan.',
        icon: 'error',
      });
    }
  }
};
</script>

<template>
  <AppLayout>
    <div class="p-8 space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-semibold text-center">Planes Disponibles</h1>
        <button
          @click="openCreateModal"
          class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium shadow"
        >
          + Crear Plan
        </button>
      </div>

      <div class="relative">
        <input
          v-model="searchTerm"
          type="search"
          class="bg-white/5 backdrop-blur-md border border-white/10 text-white placeholder-gray-400 text-sm rounded-lg block w-full pl-10 p-2.5 focus:ring-blue-500 focus:border-blue-500"
          placeholder="Buscar planes por nombre o descripción..."
        />
      </div>

      <div class="overflow-hidden rounded-xl shadow ring-1 ring-white/10 bg-white/5 backdrop-blur-md">
        <table class="min-w-full divide-y divide-white/10 text-sm text-white">
          <thead class="bg-white/10">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold">ID</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Nombre</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Descripción</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Duración</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Máx. Publicaciones</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Precio</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Destacado</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="plan in filteredPlanes"
              :key="plan.id"
              class="hover:bg-white/5 transition duration-200"
            >
              <td class="px-6 py-4">{{ plan.id }}</td>
              <td class="px-6 py-4">{{ plan.nombre }}</td>
              <td class="px-6 py-4">{{ plan.descripcion || 'Sin descripción' }}</td>
              <td class="px-6 py-4">{{ plan.duracion }} días</td>
              <td class="px-6 py-4">{{ plan.max_publicaciones }}</td>
              <td class="px-6 py-4">${{ plan.precio }}</td>
              <td class="px-6 py-4">{{ plan.destacar ? 'Sí' : 'No' }}</td>
              <td class="px-6 py-4 space-x-3">
                <button
                  @click="openEditModal(plan)"
                  class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium"
                >
                  Editar
                </button>
                <button
                  @click="deletePlan(plan.id)"
                  class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium"
                >
                  Eliminar
                </button>
              </td>
            </tr>
            <tr v-if="filteredPlanes.length === 0">
              <td colspan="8" class="text-center py-6 text-gray-400">
                No hay planes disponibles{{ searchTerm ? ' con esa búsqueda' : '' }}.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
