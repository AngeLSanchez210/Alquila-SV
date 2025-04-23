<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { Head } from '@inertiajs/vue3';
import type { User } from '@/types';

import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import { objectPick } from '@vueuse/core';

const props = defineProps<{ user: User }>();

const activeSection = ref('info'); // Estado para controlar la sección activa

const setActiveSection = (section: string) => {
  activeSection.value = section;
};

const articulos = ref([]);
const articuloSeleccionado = ref(null);
const mostrarModal = ref(false);

const fetchArticulos = async () => {
  try {
    const response = await axios.get('/api/user/articulos');
    articulos.value = response.data;
  } catch (error) {
    console.error('Error al cargar los artículos:', error);
  }
};

const eliminarArticulo = async (id) => {
  const result = await Swal.fire({
    title: '¿Estás seguro?',
    text: 'Esta acción eliminará el artículo de forma permanente.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/articulos/${id}`);
      articulos.value = articulos.value.filter((articulo) => articulo.id !== id);
      Swal.fire('Eliminado', 'El artículo fue eliminado correctamente.', 'success');
    } catch (error) {
      Swal.fire('Error', 'No se pudo eliminar el artículo.', 'error');
    }
  }
};

const abrirModalEditar = (articulo) => {
  articuloSeleccionado.value = { ...articulo };
  mostrarModal.value = true;
};

const guardarCambios = async () => {
  try {
    await axios.put(`/api/articulos/${articuloSeleccionado.value.id}`, articuloSeleccionado.value);
    Swal.fire('Actualizado', 'El artículo fue actualizado correctamente.', 'success');
    mostrarModal.value = false;
    fetchArticulos();
  } catch (error) {
    Swal.fire('Error', 'No se pudo actualizar el artículo.', 'error');
  }
};

const eliminarImagen = async (imagenId) => {
  if (articuloSeleccionado.value.imagenes.length <= 1) {
    Swal.fire('Error', 'No puedes eliminar todas las imágenes. Debe quedar al menos una.', 'error');
    return;
  }

  try {
    await axios.delete(`/api/articulos/imagenes/${imagenId}`);
    articuloSeleccionado.value.imagenes = articuloSeleccionado.value.imagenes.filter((img) => img.id !== imagenId);
    Swal.fire('Eliminada', 'La imagen fue eliminada correctamente.', 'success');
  } catch (error) {
    Swal.fire('Error', 'No se pudo eliminar la imagen.', 'error');
  }
};

const agregarImagenes = async (event) => {
  const files = event.target.files;
  if (!files || files.length === 0) return;

  const formData = new FormData();
  for (const file of files) {
    formData.append('imagenes[]', file);
  }

  try {
    const response = await axios.post(`/api/articulos/${articuloSeleccionado.value.id}/imagenes`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    articuloSeleccionado.value.imagenes.push(...response.data);
    Swal.fire('Agregadas', 'Las imágenes fueron agregadas correctamente.', 'success');
  } catch (error) {
    Swal.fire('Error', 'No se pudieron agregar las imágenes.', 'error');
  }
};

const mostrarModalUsuario = ref(false);
const usuarioEditado = ref({ name: '', email: '', direccion: '', telefono: '' });

const abrirModalUsuario = (userActually: any) => {
  usuarioEditado.value = { ...userActually }; // Cambiar 'user' por 'props.user'
  mostrarModalUsuario.value = true;
};

const guardarUsuario = async () => {
  if (!usuarioEditado.value.name.trim()) {
    Swal.fire('Error', 'El nombre no puede estar vacío.', 'error');
    return;
  }
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(usuarioEditado.value.email)) {
    Swal.fire('Error', 'El formato del correo electrónico no es válido.', 'error');
    return;
  }
  try {
    await axios.put('/users/' + props.user.id, usuarioEditado.value);
    Swal.fire('Actualizado', 'La información del usuario fue actualizada correctamente.', 'success');
    mostrarModalUsuario.value = false;
    // Actualizar la información del usuario en la interfaz
    Object.assign(props.user, usuarioEditado.value); // Cambiar 'user' por 'props.user'
  } catch (error) {
    console.error('Error al guardar el usuario:', error);
    Swal.fire('Error', 'No se pudo actualizar la información del usuario.', 'error');
  }
};

const favoritos = ref([]);

const fetchFavoritos = async () => {
  try {
    const response = await axios.get('/api/favoritos'); // Asegúrate de que esta ruta devuelva los datos completos del artículo
    favoritos.value = response.data.map((favorito) => ({
      ...favorito,
      articulo: favorito.articulo || { nombre: 'Artículo no encontrado', descripcion: '', precio: 0, imagenes: [] },
    }));
  } catch (error) {
    console.error('Error al cargar los favoritos:', error);
  }
};

const eliminarFavorito = async (favoritoId) => {
  const result = await Swal.fire({
    title: '¿Estás seguro?',
    text: 'Esta acción eliminará el artículo de tus favoritos.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`/favoritos/${favoritoId}`);
      favoritos.value = favoritos.value.filter((favorito) => favorito.id !== favoritoId);
      Swal.fire('Eliminado', 'El artículo fue eliminado de tus favoritos.', 'success');
    } catch (error) {
      Swal.fire('Error', 'No se pudo eliminar el artículo de tus favoritos.', 'error');
    }
  }
};

onMounted(() => {
  // Obtener parámetros de la URL
  const params = new URLSearchParams(window.location.search);
  const section = params.get('section'); // Obtener el valor del parámetro 'section'

  // Establecer la sección activa si existe en la URL
  if (section) {
    setActiveSection(section);
  }

  fetchArticulos();
  fetchFavoritos();
});
const redirectToCreateArticulo = () => {
  window.location.href = '/articulos/create';
};
</script>

<template>
  <Header></Header>
  <body class="bg-white">
    <Head title="Perfil de Usuario" />

    <div class="container mx-auto px-2 py-9 flex">
      <!-- Barra Lateral -->
      <aside class="w-1/4 bg-gray-100 p-4 rounded-lg shadow-md pb-72 mb-72">
        <nav class="space-y-4">
          <button
            class="block w-full text-left text-gray-700 font-medium hover:text-blue-500 transition-colors duration-200"
            :class="{ 'text-blue-500 font-semibold': activeSection === 'info' }"
            @click="setActiveSection('info')"
          >
            Información Personal
          </button>
          <button
            class="block w-full text-left text-gray-700 font-medium hover:text-blue-500 transition-colors duration-200"
            :class="{ 'text-blue-500 font-semibold': activeSection === 'articles' }"
            @click="setActiveSection('articles')"
          >
            Artículos Publicados
          </button>
          <button
            class="block w-full text-left text-gray-700 font-medium hover:text-blue-500 transition-colors duration-200"
            :class="{ 'text-blue-500 font-semibold': activeSection === 'favorites' }"
            @click="setActiveSection('favorites')"
          >
            Favoritos
          </button>
        </nav>
      </aside>

      <!-- Contenido Principal -->
      <section class="w-3/4 ml-6">
        <!-- Información del Usuario -->
        <section
          v-if="activeSection === 'info'"
          class="bg-white p-6 rounded-lg shadow-md ">
          <h2 class="text-2xl font-bold mb-4 text-gray-700">Información Personal</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Nombre:</label>
              <p class="text-gray-900">{{ user.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Correo Electrónico:</label>
              <p class="text-gray-900">{{ user.email }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Dirección:</label>
              <p class="text-gray-900">{{ user.direccion || 'No especificada' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Teléfono:</label>
              <p class="text-gray-900">{{ user.telefono || 'Sin teléfono agregado' }}</p>
            </div>
          </div>
          <button
            @click="abrirModalUsuario(user)"
            class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            Editar Información
          </button>
        </section>

        <!-- Modal para editar información del usuario -->
        <div v-if="mostrarModalUsuario" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-gray-700">
          <div class="bg-white rounded-lg max-w-lg w-full p-6">
            <h2 class="text-xl font-bold mb-4">Editar Información Personal</h2>
            <form @submit.prevent="guardarUsuario">
              <div class="mb-4">
                <label class="block text-sm font-medium">Nombre</label>
                <input v-model="usuarioEditado.name" type="text" class="w-full border p-2 rounded">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium">Correo Electrónico</label>
                <input v-model="usuarioEditado.email" type="email" class="w-full border p-2 rounded">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium">Dirección</label>
                <input v-model="usuarioEditado.direccion" type="text" class="w-full border p-2 rounded">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium">Teléfono</label>
                <input v-model="usuarioEditado.telefono" type="text" class="w-full border p-2 rounded">
              </div>
              <div class="flex justify-end gap-2">
                <button type="button" @click="mostrarModalUsuario = false" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancelar</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Guardar</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Artículos del Usuario -->
        <section
          v-if="activeSection === 'articles'"
          class="bg-white p-6 rounded-lg shadow-md mb-auto">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-700">Artículos Publicados</h2>
            <button 
              @click="redirectToCreateArticulo" 
              class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
              Agregar Artículo
            </button>
          </div>
          <ul class="space-y-4">
            <li v-for="articulo in articulos" :key="articulo.id" class="border-b pb-4 flex items-center gap-4">
              <img :src="'/storage/' + articulo.imagenes[0]?.link" alt="Imagen del artículo" class="w-24 h-24 object-cover rounded">
              <div class="flex-1">
                <h3 class="text-lg font-medium text-gray-900">{{ articulo.nombre }}</h3>
                <p class="text-sm text-gray-600">{{ articulo.descripcion }}</p>
                <p class="text-sm text-gray-600">Precio: ${{ articulo.precio }}</p>
              </div>
              <div class="flex gap-2">
                <button @click="abrirModalEditar(articulo)" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Editar</button>
                <button @click="eliminarArticulo(articulo.id)" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Eliminar</button>
              </div>
            </li>
          </ul>
        </section>

        <!-- Modal para editar artículo -->
        <div v-if="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 text-gray-700">
          <div class="bg-white rounded-lg max-w-lg w-full p-6">
            <h2 class="text-xl font-bold mb-4">Editar Artículo</h2>
            <form @submit.prevent="guardarCambios">
              <div class="mb-4">
                <label class="block text-sm font-medium">Nombre</label>
                <input v-model="articuloSeleccionado.nombre" type="text" class="w-full border p-2 rounded">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea v-model="articuloSeleccionado.descripcion" class="w-full border p-2 rounded"></textarea>
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Precio</label>
                <input v-model="articuloSeleccionado.precio" type="number" step="0.01" class="w-full border p-2 rounded">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Imágenes</label>
                <div class="flex flex-wrap gap-2 mt-2">
                  <div v-for="imagen in articuloSeleccionado.imagenes" :key="imagen.id" class="relative">
                    <img :src="'/storage/' + imagen.link" alt="Imagen del artículo" class="w-24 h-24 object-cover rounded">
                    <button
                      type="button"
                      @click="eliminarImagen(imagen.id)"
                      class="absolute top-0 right-0 bg-red-600 text-white rounded-full p-1 hover:bg-red-700"
                    >
                      ✕
                    </button>
                  </div>
                </div>
                <input type="file" multiple @change="agregarImagenes" class="mt-2 w-full border p-2 rounded">
              </div>
              <div class="flex justify-end gap-2">
                <button type="button" @click="mostrarModal = false" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancelar</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Guardar</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Favoritos -->
        <section
          v-if="activeSection === 'favorites'"
          class="bg-white p-6 rounded-lg shadow-md mb-auto">
          <h2 class="text-2xl font-bold mb-4 text-gray-700">Favoritos</h2>
          <ul class="space-y-4">
            <li
              v-for="favorito in favoritos"
              :key="favorito.id"
              class="border-b pb-4 flex items-center gap-4"
            >
              <img 
                :src="favorito.articulo.imagenes && favorito.articulo.imagenes.length > 0 
                  ? '/storage/' + favorito.articulo.imagenes[0].link 
                  : '/images/default-placeholder.png'" 
                alt="Imagen del artículo" 
                class="w-24 h-24 object-cover rounded"
              >
              <div class="flex-1">
                <h3 class="text-lg font-medium text-gray-900">{{ favorito.articulo.nombre }}</h3>
                <p class="text-sm text-gray-600">{{ favorito.articulo.descripcion }}</p>
                <p class="text-sm text-gray-600">Precio: ${{ favorito.articulo.precio }}</p>
              </div>
              <button
                @click="eliminarFavorito(favorito.id)"
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
              >
                Eliminar
              </button>
            </li>
          </ul>
          <p v-if="favoritos.length === 0" class="text-gray-600">No tienes artículos en tus favoritos.</p>
        </section>
      </section>
    </div>
  </body>
  <Footer></Footer>
</template>
