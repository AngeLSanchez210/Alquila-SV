<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import type { Plan } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';

const planes = ref<Plan[]>([]);
const searchTerm = ref('');

onMounted(async () => {
  try {
    const response = await axios.get('/api/planes');
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
      await Swal.fire('¡Eliminado!', 'El plan fue eliminado correctamente.', 'success');
    } catch (error) {
      Swal.fire('Error', 'No se pudo eliminar el plan.', 'error');
    }
  }
};

const openCreateModal = async () => {
  const { value: formValues } = await Swal.fire({
    title: 'Crear Nuevo Plan',
    html: `
      <input id="swal-nombre" class="swal2-input" placeholder="Nombre del plan">
      <textarea id="swal-descripcion" class="swal2-textarea" placeholder="Descripción (separa características con ',')"></textarea>
      <input id="swal-duracion" type="number" class="swal2-input" placeholder="Duración (días)">
      <input id="swal-max-publicaciones" type="number" class="swal2-input" placeholder="Máx. Publicaciones">
      <input id="swal-precio" type="number" class="swal2-input" placeholder="Precio">
      <label style="display: flex; align-items: center; gap: 8px; margin-top: 10px;">
        <input id="swal-destacar" type="checkbox"> Destacar
      </label>
    `,
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
        Swal.showValidationMessage('Completa todos los campos obligatorios.');
        return null;
      }

      return { nombre, descripcion, duracion, max_publicaciones: maxPublicaciones, precio, destacar };
    },
  });

  if (formValues) {
    try {
      await axios.post('/planes', formValues);
      const response = await axios.get('/api/planes');
      planes.value = response.data;
      Swal.fire('¡Creado!', 'El plan fue creado correctamente.', 'success');
    } catch (error) {
      Swal.fire('Error', 'No se pudo crear el plan.', 'error');
    }
  }
};

const openEditModal = async (plan: Plan) => {
  const { value: formValues } = await Swal.fire({
    title: 'Editar Plan',
    html: `
      <input id="swal-nombre" class="swal2-input" placeholder="Nombre del plan" value="${plan.nombre}">
      <textarea id="swal-descripcion" class="swal2-textarea" placeholder="Descripción (separa características con ',')">${plan.descripcion || ''}</textarea>
      <input id="swal-duracion" type="number" class="swal2-input" placeholder="Duración (días)" value="${plan.duracion}">
      <input id="swal-max-publicaciones" type="number" class="swal2-input" placeholder="Máx. Publicaciones" value="${plan.max_publicaciones}">
      <input id="swal-precio" type="number" class="swal2-input" placeholder="Precio" value="${plan.precio}">
      <label style="display: flex; align-items: center; gap: 8px; margin-top: 10px;">
        <input id="swal-destacar" type="checkbox" ${plan.destacar ? 'checked' : ''}> Destacar
      </label>
    `,
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
        Swal.showValidationMessage('Completa todos los campos obligatorios.');
        return null;
      }

      return { nombre, descripcion, duracion, max_publicaciones: maxPublicaciones, precio, destacar };
    },
  });

  if (formValues) {
    try {
      const response = await axios.put(`/planes/${plan.id}`, formValues);
      const updatedPlan = response.data;
      const index = planes.value.findIndex(p => p.id === plan.id);
      if (index !== -1) {
        planes.value[index] = updatedPlan;
      }
      Swal.fire('¡Actualizado!', 'El plan fue actualizado correctamente.', 'success');
    } catch (error) {
      Swal.fire('Error', 'No se pudo actualizar el plan.', 'error');
    }
  }
};
</script>

<template>
  <AppLayout>
    <div class="p-8 bg-gray-100 min-h-screen space-y-8">
      
      <div class="flex justify-between items-center">
        <h1 class="text-4xl font-bold text-gray-800">Planes Disponibles</h1>
        <button
          @click="openCreateModal"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-semibold shadow transition"
        >
          + Crear Plan
        </button>
      </div>

      <div class="relative max-w-md">
        <input
          v-model="searchTerm"
          type="search"
          placeholder="Buscar planes por nombre o descripción..."
          class="w-full p-3 pl-10 rounded-lg border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-800 placeholder-gray-400"
        />
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>

      <div class="overflow-x-auto">
        <div class="bg-white rounded-2xl shadow-md p-6">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left font-bold text-gray-700">ID</th>
                <th class="px-6 py-3 text-left font-bold text-gray-700">Nombre</th>
                <th class="px-6 py-3 text-left font-bold text-gray-700">Descripción</th>
                <th class="px-6 py-3 text-left font-bold text-gray-700">Duración</th>
                <th class="px-6 py-3 text-left font-bold text-gray-700">Máx. Publicaciones</th>
                <th class="px-6 py-3 text-left font-bold text-gray-700">Precio</th>
                <th class="px-6 py-3 text-left font-bold text-gray-700">Destacado</th>
                <th class="px-6 py-3 text-left font-bold text-gray-700">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="plan in filteredPlanes" :key="plan.id" class="hover:bg-gray-50 transition duration-200 text-black">
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
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition"
                  >
                    Editar
                  </button>
                  <button
                    @click="deletePlan(plan.id)"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition"
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

    </div>
  </AppLayout>
</template>
