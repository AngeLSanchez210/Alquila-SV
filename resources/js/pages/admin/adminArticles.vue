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
  imagenes: { id: number; link: string }[];
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

const imagenSeleccionada = ref<string | null>(null);
const mostrarModalImagen = ref(false);

const verImagen = (ruta: string) => {
  imagenSeleccionada.value = `/storage/${ruta}`;
  mostrarModalImagen.value = true;
};

const cerrarModal = () => {
  mostrarModalImagen.value = false;
  imagenSeleccionada.value = null;
};


</script>

<template>
  <AppLayout>
    <div class="p-8 bg-gray-100 min-h-screen space-y-8">

      <h1 class="text-4xl font-bold text-gray-800 text-center">Panel de Artículos</h1>

      <!-- Búsqueda -->
      <div class="relative max-w-md mx-auto">
        <input
          v-model="search"
          type="search"
          placeholder="Buscar artículos por nombre..."
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
                <th class="px-6 py-3 text-left font-bold text-gray-700">Precio</th>
                <th class="px-6 py-3 text-left font-bold text-gray-700">Estado</th>
                <th class="px-6 py-3 text-left font-bold text-gray-700">Imagen</th>
                <th class="px-6 py-3 text-left font-bold text-gray-700">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="articulo in articulosFiltrados" :key="articulo.id" class="hover:bg-gray-50 text-black">
                <td class="px-6 py-4">{{ articulo.id }}</td>
                <td class="px-6 py-4">{{ articulo.nombre }}</td>
                <td class="px-6 py-4">${{ articulo.precio.toFixed(2) }}</td>
                <td class="px-6 py-4 capitalize">{{ articulo.estado }}</td>
                <td class="px-6 py-4">
                  <div class="flex gap-2 mt-2 flex-wrap">
                    <div class="flex gap-2 mt-2 flex-wrap">
                      <img
                        v-for="imagen in articulo.imagenes"
                        :key="imagen.id"
                        :src="'/storage/' + imagen.link"
                        @click="verImagen(imagen.link)"
                        class="w-20 h-20 object-cover rounded-md shadow-sm cursor-pointer hover:ring-2 hover:ring-indigo-500 transition"
                        alt="Imagen del artículo"
                      />
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 space-x-2">
                  <button
                    @click="deleteArticulo(articulo.id)"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition"
                  >
                    Eliminar
                  </button>
                </td>
              </tr>
              <tr v-if="articulosFiltrados.length === 0">
                <td colspan="6" class="text-center py-6 text-gray-400">
                  No hay artículos disponibles{{ search ? ' con esa búsqueda' : '' }}.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Modal para vista previa de imagen -->
          <div
            v-if="mostrarModalImagen"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click.self="cerrarModal"
          >
            <div class="bg-white rounded-xl p-4 shadow-lg max-w-md w-full text-center">
              <img
              :src="imagenSeleccionada ?? ''"
                alt="Vista previa"
                class="w-full h-auto rounded-md max-h-[70vh] object-contain"
              />
              <button
                @click="cerrarModal"
                class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg"
              >
                Cerrar
              </button>
            </div>
          </div>

    </div>
  </AppLayout>
</template>
