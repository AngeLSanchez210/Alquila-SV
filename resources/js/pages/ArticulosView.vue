<script setup>
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

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
</script>

<template>
  <Header />

  <!-- Sección principal -->
  <section class="flex flex-col lg:flex-row gap-6 bg-gray-100 p-16 text-gray-900">
    <!-- Filtros -->
    <div class="w-full lg:w-80 space-y-6 bg-white p-6 rounded-lg shadow-sm max-h-fit">
      <!-- Aquí puedes añadir tus filtros -->
    </div>

    <!-- Productos -->
    <div class="flex-1 bg-white">
      <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <h2 class="text-2xl font-bold tracking-tight text-gray-900">Mostrando artículos</h2>

        <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-8">
          <div v-for="articulo in articulos" :key="articulo.id" class="group relative">
            <!-- Carrusel -->
            <swiper :modules="[Navigation, Pagination]" navigation pagination class="rounded-md shadow-lg">
              <swiper-slide v-for="imagen in articulo.imagenes" :key="imagen.id">
                <img
                  :src="imagen.link ? `/storage/${imagen.link}` : 'https://via.placeholder.com/150'"
                  :alt="articulo.nombre"
                  class="w-full h-60 object-cover rounded-md"
                />
              </swiper-slide>
            </swiper>

            <!-- Info -->
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

          <!-- Info completa -->
          <div class="space-y-6">
            <p class="text-2xl font-semibold text-gray-900">${{ articuloSeleccionado.precio }}</p>
            <p class="text-gray-700">{{ articuloSeleccionado.descripcion }}</p>

            <div class="border-t border-gray-200 pt-4 space-y-2">
              <h3 class="text-lg font-medium text-gray-900">Detalles del producto</h3>
              <p class=" text-black font-medium"><span class=" text-black font-medium">Estado:</span> {{ articuloSeleccionado.estado }}</p>
              <p class="text-black font-medium">
              <span class="text-black font-medium">Publicado por:</span> {{ articuloSeleccionado.usuario?.name || 'Desconocido' }}</p>
              <p class=" text-black font-medium"><span class="text-black font-medium">Fecha de publicación:</span> {{ new Date(articuloSeleccionado.created_at).toLocaleString() }}</p>
            </div>

            <div class="mt-6">
              <button class="w-full bg-indigo-600 py-3 px-8 rounded-md font-medium text-white hover:bg-indigo-700">
                Contactar vendedor
              </button>
            </div>
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
