<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import AppLayout from '@/layouts/AppLayout.vue';

interface Articulo {
  id: number;
  nombre: string;
  precio: number;
  estado: string;
  imagenes: { id: number; ruta: string }[];
}

const articulos = ref<Articulo[]>([]);
const search = ref('');

onMounted(async () => {
  try {
    const response = await axios.get('/api/articulos');
    articulos.value = response.data;
  } catch (error) {
    console.error('Error al cargar artículos:', error);
  }
});

const articulosFiltrados = computed(() => {
  const term = search.value.toLowerCase();
  return articulos.value.filter(articulo =>
    articulo.nombre.toLowerCase().includes(term)
  );
});

const deleteArticulo = async (id: number) => {
  const result = await Swal.fire({
    title: '¿Estás seguro?',
    text: 'Esta acción eliminará el artículo y sus imágenes.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/articulos/${id}`);
      articulos.value = articulos.value.filter(a => a.id !== id);
      await Swal.fire('Eliminado', 'El artículo fue eliminado correctamente.', 'success');
    } catch (error) {
      Swal.fire('Error', 'No se pudo eliminar el artículo.', 'error');
    }
  }
};
</script>

<template>
  <AppLayout>
    <div class="p-8 space-y-6">
      <h1 class="text-3xl font-semibold text-white text-center w-full">PANEL DE ARTÍCULOS</h1>

      <!-- Búsqueda -->
      <div class="relative mb-6">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <input
          v-model="search"
          type="search"
          class="bg-white/5 backdrop-blur-md border border-white/10 text-white placeholder-gray-400 text-sm rounded-lg block w-full pl-10 p-2.5"
          placeholder="Buscar artículos por nombre..."
        />
      </div>

      <!-- Tabla de artículos -->
      <div class="overflow-hidden rounded-xl shadow ring-1 ring-white/10 bg-white/5 backdrop-blur-md">
        <table class="min-w-full divide-y divide-white/10 text-sm text-white">
          <thead class="bg-white/10">
            <tr>
              <th class="px-6 py-3 text-left font-semibold">ID</th>
              <th class="px-6 py-3 text-left font-semibold">Nombre</th>
              <th class="px-6 py-3 text-left font-semibold">Precio</th>
              <th class="px-6 py-3 text-left font-semibold">Estado</th>
              <th class="px-6 py-3 text-left font-semibold">Imagen</th>
              <th class="px-6 py-3 text-left font-semibold">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <!-- Muestra los artículos filtrados -->
            <tr v-for="articulo in articulosFiltrados" :key="articulo.id" class="hover:bg-white/5">
              <td class="px-6 py-4">{{ articulo.id }}</td>
              <td class="px-6 py-4">{{ articulo.nombre }}</td>
              <td class="px-6 py-4">${{ articulo.precio }}</td>
              <td class="px-6 py-4 capitalize">{{ articulo.estado }}</td>
              <td class="px-6 py-4">
                <div class="flex gap-2 mt-2">
                  <img
                    v-for="imagen in articulo.imagenes"
                    :key="imagen.id"
                    :src="'/storage/' + imagen.ruta"
                    class="w-24 h-24 object-cover rounded"
                    alt="Imagen del artículo"
                  />
                </div>
              </td>
              <td class="px-6 py-4 space-x-2">
                <button
                  @click="deleteArticulo(articulo.id)"
                  class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium"
                >
                  Eliminar
                </button>
              </td>
            </tr>
            <!-- Mensaje cuando no hay artículos -->
            <tr v-if="articulosFiltrados.length === 0">
              <td colspan="6" class="text-center py-6 text-gray-400">
                No hay artículos disponibles{{ search ? ' con esa búsqueda' : '' }}.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
