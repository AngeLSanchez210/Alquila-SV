<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { Head } from '@inertiajs/vue3';
import type { User } from '@/types';

import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import { objectPick } from '@vueuse/core';


import { router } from '@inertiajs/vue3'; // Asegúrate de importar router
const verArticulo = (id: number) => {
  router.visit(`/articulos/ver/${id}`);
};

const verPerfil = (id: number) => {
  router.visit(`/profile/${id}`);
};


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

const userImage = ref(null);

const fetchUserImage = async () => {
  try {
    const response = await axios.get(`/api/users/${props.user.id}/image`);
    userImage.value = response.data.image_url;
  } catch (error) {
    console.error('Error al cargar la imagen del usuario:', error);
  }
};

const fetchFollowinImage = async (id_following) => {
  const followingImage = ref(null);
  console.log(id_following);
  
  try {
    const response = await axios.get(`/api/users/${id_following}/image`);
    followingImage.value = response.data.image_url;
  } catch (error) {
    console.error('Error al cargar la imagen del usuario:', error);
  }
  return followingImage.value;
};

const uploadUserImage = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('image', file);

  try {
    const response = await axios.post(`/api/users/${props.user.id}/image`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    userImage.value = response.data.image_url;
    Swal.fire('Éxito', 'La imagen fue actualizada correctamente.', 'success');
  } catch (error) {
    Swal.fire('Error', 'No se pudo actualizar la imagen.', 'error');
  }
};

const suscripcionActiva = ref(null);

const fetchSuscripcionActiva = async () => {
  try {
    const response = await axios.get(`/api/users/${props.user.id}/suscripcion-activa`);
    suscripcionActiva.value = response.data;
  } catch (error) {
    console.error('Error al cargar la suscripción activa:', error);
  }
};

const planes = ref([]);
const mostrarPlanes = ref(false);

const fetchPlanes = async () => {
  try {
    const response = await axios.get('/api/planes');
    planes.value = response.data;
  } catch (error) {
    console.error('Error al cargar los planes:', error);
  }
};

const mejorarPlan = () => {
  mostrarPlanes.value = true;
};

const mostrarModalPlanes = ref(false);

const abrirModalPlanes = () => {
  mostrarModalPlanes.value = true;
};

const cerrarModalPlanes = () => {
  mostrarModalPlanes.value = false;
};

const siguiendo = ref([]);

const fetchSiguiendo = async () => {
  try {
    const response = await axios.get(`/api/seguidores/lista/${props.user.id}`);
    siguiendo.value = await Promise.all(
      response.data.map(async (persona) => ({
        ...persona,
        image: persona.image || `/storage/${(await fetchFollowinImage(persona.id))}`, // Esperar a que fetchFollowinImage se resuelva
      }))
    );
  } catch (error) {
    console.error('Error al cargar las personas que sigues:', error);
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
  fetchUserImage();
  fetchSuscripcionActiva();
  fetchPlanes();
});
const redirectToCreateArticulo = () => {
  window.location.href = '/articulos/create';
};

const imagenSeleccionada = ref(null);
const mostrarImagenModal = ref(false);



const cerrarImagenModal = () => {
  mostrarImagenModal.value = false;
  imagenSeleccionada.value = null;
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
          <button
            class="block w-full text-left text-gray-700 font-medium hover:text-blue-500 transition-colors duration-200"
            :class="{ 'text-blue-500 font-semibold': activeSection === 'planuser' }"
            @click="setActiveSection('planuser')"
          >
            Mis suscripciones
          </button>
          <button
            class="block w-full text-left text-gray-700 font-medium hover:text-blue-500 transition-colors duration-200"
            :class="{ 'text-blue-500 font-semibold': activeSection === 'siguiendo' }"
            @click="setActiveSection('siguiendo'); fetchSiguiendo()"
          >
            Personas que sigo
          </button>
        </nav>
      </aside>

      <!-- Contenido Principal -->
      <section class="w-3/4 ml-6">
        <!-- Información del Usuario -->
        <section v-if="activeSection === 'info'" class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-2xl font-bold mb-4 text-gray-700">Información Personal</h2>
          <div class="space-y-4">
            <!-- Imagen del usuario -->
            <div>
              <label class="block text-sm font-medium text-gray-700">Imagen de Perfil:</label>
              <div class="flex items-center gap-4">
                <img
                  v-if="userImage"
                  :src="`/storage/${userImage}`"
                  alt="Imagen del usuario"
                  class="w-24 h-24 object-cover rounded-full border cursor-pointer"
                  @click="abrirImagenModal(`/storage/${userImage}`)"
                />
                <input type="file" @change="uploadUserImage" class="text-sm text-gray-600">
              </div>
            </div>
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
              <img
                      @click="verArticulo(articulo.id)"
                      :src="'/storage/' + articulo.imagenes[0]?.link"
                      alt="Imagen del artículo"
                      class="w-24 h-24 object-cover rounded cursor-pointer hover:opacity-80 transition"
                    />

   
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
                        @click="verArticulo(favorito.articulo.id)"
                        :src="favorito.articulo.imagenes && favorito.articulo.imagenes.length > 0 
                          ? '/storage/' + favorito.articulo.imagenes[0].link 
                          : '/images/default-placeholder.png'"
                        alt="Imagen del artículo"
                        class="w-24 h-24 object-cover rounded cursor-pointer hover:opacity-80 transition"
                      />

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
        <section
          v-if="activeSection === 'planuser'"
          class="bg-white p-6 rounded-lg shadow-md mb-auto text-teal-950">
          <h2 class="text-2xl font-bold mb-4 text-gray-700">Suscripción Activa</h2>
          <div v-if="suscripcionActiva" class="space-y-4">
            <p><strong>Plan:</strong> {{ suscripcionActiva.plan.nombre }}</p>
            <p><strong>Precio:</strong> ${{ suscripcionActiva.plan.precio }}</p>
            <p><strong>Fecha de Inicio:</strong> {{ new Date(suscripcionActiva.fecha_inicio).toLocaleDateString() }}</p>
            <p><strong>Fecha de Fin:</strong> {{ new Date(suscripcionActiva.fecha_fin).toLocaleDateString() }}</p>
            <p><strong>Estado:</strong> {{ suscripcionActiva.estado }}</p>
            <button
              @click="abrirModalPlanes"
              class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
            >
              Mejorar Plan
            </button>
          </div>
          <p v-else class="text-gray-600">No tienes una suscripción activa.</p>
        </section>

        <!-- Modal para mostrar planes -->
        <div v-if="mostrarModalPlanes" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <div class="bg-white rounded-lg max-w-4xl w-full p-6">
            <h3 class="text-xl font-bold mb-4 text-gray-700">Selecciona un Nuevo Plan</h3>
            <button
              @click="cerrarModalPlanes"
              class="absolute top-4 right-4 bg-red-600 text-white rounded-full p-2 hover:bg-red-700"
            >
              ✕
            </button>
            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 lg:grid-cols-3 gap-x-4">
              <div
                v-for="(plan, index) in planes"
                :key="plan.id"
                :class="[
                  'relative rounded-3xl p-8 ring-1',
                  plan.precio === Math.max(...planes.map(p => p.precio))
                    ? 'bg-gray-900 text-gray-300 shadow-2xl ring-gray-900/10'
                    : 'bg-white text-gray-600 ring-gray-900/10',
                ]"
              >
                <h3
                  :id="`tier-${plan.nombre.toLowerCase()}`"
                  :class="plan.precio === Math.max(...planes.map(p => p.precio)) ? 'text-indigo-400' : 'text-indigo-600'"
                >
                  {{ plan.nombre }}
                </h3>
                <p class="mt-4 flex items-baseline gap-x-2">
                  <span
                    :class="plan.precio === Math.max(...planes.map(p => p.precio)) ? 'text-white' : 'text-gray-900'"
                    class="text-5xl font-semibold tracking-tight"
                  >
                    ${{ plan.precio }}
                  </span>
                  <span
                    :class="plan.precio === Math.max(...planes.map(p => p.precio)) ? 'text-gray-400' : 'text-gray-500'"
                    class="text-base"
                  >
                    /mes
                  </span>
                </p>
                <p
                  class="mt-6 text-base/7"
                  :class="plan.precio === Math.max(...planes.map(p => p.precio)) ? 'text-gray-300' : 'text-gray-600'"
                >
                  {{ plan.descripcion.split(',')[0] }}
                </p>
                <ul
                  role="list"
                  class="mt-8 space-y-3 text-sm/6"
                  :class="plan.precio === Math.max(...planes.map(p => p.precio)) ? 'text-gray-300' : 'text-gray-600'"
                >
                  <li
                    v-for="(item, i) in plan.descripcion.split(',').slice(1)"
                    :key="i"
                    class="flex gap-x-3"
                  >
                    <svg
                      :class="plan.precio === Math.max(...planes.map(p => p.precio)) ? 'text-indigo-400' : 'text-indigo-600'"
                      class="h-6 w-5 flex-none"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                      aria-hidden="true"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    {{ item }}
                  </li>
                </ul>
                <a
                  v-if="plan.precio > 0"
                  :href="`/pay?plan_id=${plan.id}`"
                  :aria-describedby="`tier-${plan.nombre.toLowerCase()}`"
                  class="mt-8 block rounded-md px-3.5 py-2.5 text-center text-sm font-semibold"
                  :class="plan.precio === Math.max(...planes.map(p => p.precio))
                    ? 'bg-indigo-500 text-white hover:bg-indigo-400 focus-visible:outline-indigo-500'
                    : 'text-indigo-600 ring-1 ring-indigo-200 ring-inset hover:ring-indigo-300 focus-visible:outline-indigo-600'"
                >
                  {{ plan.precio === Math.max(...planes.map(p => p.precio)) ? 'Contactar ventas' : 'Seleccionar Plan' }}
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Nueva sección: Personas que sigo -->
        <section
          v-if="activeSection === 'siguiendo'"
          class="bg-white p-6 rounded-lg shadow-md mb-auto"
        >
          <h2 class="text-2xl font-bold mb-4 text-gray-700">Personas que sigues</h2>
          <ul v-if="siguiendo.length" class="space-y-4">
            <li
              v-for="persona in siguiendo"
              :key="persona.id"
              @click="verPerfil(persona.id)"
              class="flex items-center gap-4 hover:bg-gray-100 p-2 rounded-lg cursor-pointer transition"
            >
              <img
                :src="persona.image || '/images/default-user.svg'"
                alt="Imagen del usuario"
                class="w-12 h-12 object-cover rounded-full border"
                @error="persona.image = '/images/default-user.svg'"
              />
              <span class="text-gray-800 font-medium">{{ persona.name }}</span>
            </li>
          </ul>
          <p v-else class="text-gray-500 text-center">No sigues a nadie aún.</p>
        </section>
      </section>
    </div>
    <div v-if="mostrarImagenModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
    <div class="relative">
      <img :src="imagenSeleccionada" alt="Imagen ampliada" class="max-w-full max-h-screen rounded-lg">
      <button @click="cerrarImagenModal" class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-2 hover:bg-red-700">✕</button>
    </div>
  </div>
  </body>
  <Footer></Footer>
</template>
