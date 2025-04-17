<script setup>
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

// Swiper.js
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

const priceInput = ref(100);
const priceRange = ref(100);

const mostrarModal = ref(false);
const articuloSeleccionado = ref(null);

const puntuacion = ref(0);
const comentario = ref('');

const updatePrice = (value) => {
  const maxPrice = 1000;
  const numericValue = Math.min(Math.max(value, 0), maxPrice);
  priceInput.value = numericValue;
  priceRange.value = numericValue;
};

const articulos = ref([]);

const fetchArticulos = async () => {
  try {
    const response = await axios.get('/api/articulos');
    articulos.value = response.data;
  } catch (error) {
    console.error('Error al obtener los artículos:', error);
  }
};

const abrirDetalles = (articulo) => {
  articuloSeleccionado.value = articulo;
  mostrarModal.value = true;
};

const cerrarModal = () => {
  mostrarModal.value = false;
};

onMounted(() => {
  fetchArticulos();
});

axios.defaults.withCredentials = true;

const user = usePage().props.auth.user;

const agregarAFavoritos = async () => {
  if (!user || !articuloSeleccionado.value) {
    Swal.fire({
      icon: 'warning',
      title: 'Debes iniciar sesión',
      text: 'Inicia sesión para agregar el artículo a tus favoritos.',
    });
    return;
  }

  try {
    const response = await axios.post('/api/favoritos', {
      articulo_id: articuloSeleccionado.value.id
    });

    if (response.data.message === 'Ya está agregado en favoritos') {
      Swal.fire({
        icon: 'info',
        title: 'Ya en favoritos',
        text: 'Este artículo ya está en tu lista de favoritos.',
      });
    } else {
      Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: response.data.message,
      });
    }
  } catch (error) {
    console.error("Error al agregar a favoritos:", error);
    Swal.fire({
      icon: 'error',
      title: '¡Error!',
      text: 'Hubo un problema al guardar el favorito. Intenta nuevamente.',
    });
  }
};

const agregarPuntuacion = async () => {
  if (!user || !articuloSeleccionado.value || !puntuacion.value) {
    Swal.fire({
      icon: 'warning',
      title: 'Debes iniciar sesión y proporcionar una puntuación',
      text: 'Inicia sesión para agregar una puntuación al artículo.',
    });
    return;
  }

  try {
    await axios.post('/puntuaciones', {
      articulo_id: articuloSeleccionado.value.id,
      puntuacion: puntuacion.value,
      comentario: comentario.value || "",
    });

    Swal.fire({
      icon: 'success',
      title: '¡Puntuación agregada!',
      text: 'Tu puntuación se ha registrado correctamente.',
    });
  } catch (error) {
    console.error("Error al agregar puntuación:", error);
    Swal.fire({
      icon: 'error',
      title: '¡Error!',
      text: 'Hubo un problema al guardar la puntuación. Intenta nuevamente.',
    });
  }
};
</script>

<template>
  <Header />

  <!-- Sección principal -->
  <section class="flex flex-col lg:flex-row gap-6 bg-gray-100 p-16 text-gray-900">
    <!-- Filtros -->
    <div class="w-full lg:w-80 space-y-6 bg-white p-6 rounded-lg shadow-sm max-h-fit">
      <h3 class="text-lg font-semibold mb-4 text-gray-900">Filtros</h3>

      <!-- Rango de precios -->
      <div class="space-y-4">
        <h4 class="text-sm font-medium">Rango de precios</h4>
        <div class="flex items-center gap-3">
          <input
            type="number"
            v-model="priceInput"
            min="0"
            max="1000"
            step="10"
            class="w-24 px-3 py-2 border rounded-md text-sm"
            @input="updatePrice(priceInput)"
          />
          <span class="text-sm text-gray-500">máx.</span>
        </div>
        <div class="relative pt-2">
          <input
            type="range"
            v-model="priceRange"
            min="0"
            max="1000"
            step="10"
            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
            @input="updatePrice(priceRange)"
          />
          <div class="flex justify-between text-sm text-gray-500 mt-2">
            <span>$0</span>
            <span>$1000</span>
          </div>
        </div>
      </div>

      <!-- Categorías -->
      <div class="border-t pt-6">
        <h4 class="text-sm font-medium mb-3">Categorías</h4>
        <div class="space-y-2">
          <label v-for="(categoria, index) in ['Camisetas', 'Pantalones', 'Sudaderas', 'Accesorios']" :key="index" class="flex items-center gap-2">
            <input type="checkbox" class="h-4 w-4 text-indigo-600 rounded" />
            <span class="text-sm">{{ categoria }}</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Productos -->
    <div class="flex-1 bg-white">
      <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <h2 class="text-2xl font-bold tracking-tight text-gray-900">Mostrando artículos</h2>
        <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-8">
          <div v-for="articulo in articulos" :key="articulo.id" class="group relative">
            <swiper :modules="[Navigation, Pagination]" navigation pagination class="rounded-md shadow-lg">
              <swiper-slide v-for="imagen in articulo.imagenes" :key="imagen.id">
                <img
                  :src="imagen.link ? `/storage/${imagen.link}` : 'https://via.placeholder.com/150'"
                  :alt="articulo.nombre"
                  class="w-full h-60 object-cover rounded-md"
                />
              </swiper-slide>
            </swiper>
            <div class="mt-4 flex justify-between">
              <div>
                <h3 class="text-sm text-gray-700">
                  <a href="#" @click.prevent>
                    <span aria-hidden="true" class="absolute inset-0"></span>
                    {{ articulo.nombre }}
                  </a>
                </h3>
                <p class="mt-1 text-sm text-gray-500">{{ articulo.descripcion }}</p>
                <button
                  @click.stop.prevent="abrirDetalles(articulo)"
                  class="mt-2 px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 relative z-10"
                >
                  Ver más
                </button>
              </div>
              <p class="text-sm font-medium text-gray-900">${{ articulo.precio }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Modal Detalles -->
  <div v-if="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-center p-6 border-b">
        <h2 class="text-2xl font-bold text-gray-900">{{ articuloSeleccionado?.nombre }}</h2>
        <button @click="cerrarModal" class="text-gray-500 hover:text-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="p-6">
        <div v-if="articuloSeleccionado" class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Imágenes -->
          <div class="overflow-hidden rounded-lg">
            <swiper :modules="[Navigation, Pagination]" navigation pagination class="rounded-md shadow-lg">
              <swiper-slide v-for="imagen in articuloSeleccionado.imagenes" :key="imagen.id">
                <img
                  :src="imagen.link ? `/storage/${imagen.link}` : 'https://via.placeholder.com/500'"
                  :alt="articuloSeleccionado.nombre"
                  class="w-full h-96 object-cover"
                />
              </swiper-slide>
            </swiper>
          </div>

          <!-- Info -->
          <div class="space-y-6">
            <p class="text-2xl font-semibold text-gray-900">${{ articuloSeleccionado.precio }}</p>
            <p class="text-gray-700">{{ articuloSeleccionado.descripcion }}</p>
            <div class="border-t border-gray-200 pt-4 space-y-2">
              <h3 class="text-lg font-medium text-gray-900">Detalles del producto</h3>
              <p class="text-black font-medium">Estado: {{ articuloSeleccionado.estado }}</p>
              <p class="text-black font-medium">Publicado por: {{ articuloSeleccionado.usuario?.name || 'Desconocido' }}</p>
              <p class="text-black font-medium">Fecha de publicación: {{ new Date(articuloSeleccionado.created_at).toLocaleString() }}</p>
            </div>

            <!-- Contactar -->
            <button class="w-full bg-indigo-600 py-3 px-8 rounded-md font-medium text-white hover:bg-indigo-700">Contactar vendedor</button>

            <!-- Favoritos -->
            <button
              @click.stop.prevent="agregarAFavoritos"
              class="w-full bg-yellow-400 py-3 px-8 rounded-md font-medium text-black hover:bg-yellow-500 mb-4"
            >
              Agregar a favoritos ❤️
            </button>

            <!-- Puntuación -->
            <div>
              <label class="block text-sm font-medium text-gray-700">Tu puntuación:</label>
              <div class="flex items-center space-x-1 mt-2">
                <template v-for="n in 5" :key="n">
                  <svg
                    @click="puntuacion = n"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 cursor-pointer transition duration-200"
                    :class="puntuacion >= n ? 'text-yellow-400' : 'text-gray-300'"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.96a1 1 0 00.95.69h4.17c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.96c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.197-1.539-1.118l1.287-3.96a1 1 0 00-.364-1.118L2.055 9.387c-.783-.57-.38-1.81.588-1.81h4.17a1 1 0 00.951-.69l1.285-3.96z" />
                  </svg>
                </template>
              </div>
            </div>

            <!-- Comentario -->
            <div>
              <label for="comentario" class="block text-sm font-medium text-gray-700">Comentario (opcional):</label>
              <textarea  v-model="comentario" id="comentario" rows="4" class="w-full px-3 py-2 border rounded-md text-sm mt-2 text-gray-700 "></textarea>
            </div>

            <button
              @click.stop.prevent="agregarPuntuacion"
              class="mt-4 w-full bg-indigo-600 py-3 px-8 rounded-md font-medium text-white hover:bg-indigo-700"
            >
              Agregar Puntuación
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <Footer />
</template>

<style scoped>
.z-50 {
  z-index: 50;
}
:global(body.modal-open) {
  overflow: hidden;
}
.detalles-producto {
  color: #000000;
}
</style>
