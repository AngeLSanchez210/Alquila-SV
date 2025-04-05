<script setup>
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Importaciones para Swiper.js
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

const priceInput = ref(100);
const priceRange = ref(100);


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


onMounted(() => {
  fetchArticulos();
});
</script>

<template>
  <Header></Header>

  <!-- Sección de Productos -->
  <section class="flex flex-col lg:flex-row gap-6 bg-gray-100 p-16 text-gray-900">
    <!-- Filtros -->
    <div class="w-full lg:w-80  space-y-6 bg-white p-6 rounded-lg shadow-sm max-h-fit">
      <h3 class="text-lg font-semibold mb-4 text-gray-900">Filtros</h3>
      
      <!-- Filtro de precio -->
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

      <!-- Filtro de categorias -->
      <div class="border-t pt-6">
           <h4 class="text-sm font-medium mb-3">Categorías</h4>
           <div class="space-y-2">
             <label class="flex items-center gap-2">
               <input type="checkbox" class="h-4 w-4 text-indigo-600 rounded" />
               <span class="text-sm">Camisetas</span>
             </label>
             <label class="flex items-center gap-2">
               <input type="checkbox" class="h-4 w-4 text-indigo-600 rounded" />
               <span class="text-sm">Pantalones</span>
             </label>
             <label class="flex items-center gap-2">
               <input type="checkbox" class="h-4 w-4 text-indigo-600 rounded" />
               <span class="text-sm">Sudaderas</span>
             </label>
             <label class="flex items-center gap-2">
               <input type="checkbox" class="h-4 w-4 text-indigo-600 rounded" />
               <span class="text-sm">Accesorios</span>
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

            <!-- Carrusel de imágenes -->
            <swiper
              :modules="[Navigation, Pagination]"
              navigation
              pagination
              class="rounded-md shadow-lg"
            >
              <swiper-slide v-for="imagen in articulo.imagenes" :key="imagen.id">
                <img
                  :src="imagen.link ? `/storage/${imagen.link}` : 'https://via.placeholder.com/150'"
                  :alt="articulo.nombre"
                  class="w-full h-60 object-cover rounded-md"
                />
              </swiper-slide>
            </swiper>

            <!-- Información del artículo -->
            <div class="mt-4 flex justify-between">
              <div>
                <h3 class="text-sm text-gray-700">
                  <a href="#">
                    <span aria-hidden="true" class="absolute inset-0"></span>
                    {{ articulo.nombre }}
                  </a>
                </h3>
                <p class="mt-1 text-sm text-gray-500">{{ articulo.descripcion }}</p>
              </div>
              <p class="text-sm font-medium text-gray-900">${{ articulo.precio }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <Footer></Footer>
</template>
