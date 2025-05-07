<script setup>
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

const priceInput = ref(1000); // valor del input de precio
const priceRange = ref(1000); // valor del slider de precio
const mostrarModal = ref(false); // controla si se muestra el modal de detalles
const articuloSeleccionado = ref(null); // artículo que se está viendo en detalle
const puntuacion = ref(0); // puntuación seleccionada por el usuario
const comentario = ref(''); // comentario del usuario
const articulos = ref([]); // lista de artículos filtrados
const pagination = ref({}); // datos de paginación recibidos del backend
const isLoading = ref(false); // bandera para mostrar loading
const user = usePage().props.auth.user; // usuario autenticado

// Lista de categorías fijas
const categorias = ref([
  'Herramientas', 'Electrodomésticos', 'Vehículos', 'Ropa y disfraces', 'Deportes', 'Tecnología', 'Muebles',
  'Juguetes', 'Jardinería', 'Cámaras y fotografía', 'Audio y video', 'Camping', 'Fiestas y eventos',
  'Musicales e instrumentos', 'Oficina', 'Bebés y niños', 'Libros y revistas', 'Gaming', 'Accesorios para autos',
  'Maquinaria pesada', 'Artículos de cocina', 'Decoración', 'Fitness y ejercicio', 'Moda y accesorios',
  'Fotocabinas', 'Espacios para reuniones', 'Drones', 'Patinetas y bicis', 'Artículos de limpieza', 'Carpas y toldos'
]);

const categoriasSeleccionadas = ref({}); // objeto para rastrear las categorías seleccionadas
const soloDestacados = ref(false); // para filtrar solo artículos destacados
const paginaActual = ref(1); // página actual para paginación

// Inicializa las categorías como no seleccionadas
categorias.value.forEach(cat => categoriasSeleccionadas.value[cat] = false);

// Lee la categoría desde la URL si viene con ?categoria=nombre
const page = usePage();
const routeParams = page.url.split('?')[1];
const urlParams = new URLSearchParams(routeParams);
const categoriaDesdeURL = urlParams.get('categoria');
if (categoriaDesdeURL && categoriasSeleccionadas.value.hasOwnProperty(categoriaDesdeURL)) {
  categoriasSeleccionadas.value[categoriaDesdeURL] = true;
}

// Actualiza el precio desde input o slider (valida de 0 a 1000)
const updatePrice = (value) => {
  const maxPrice = 1000;
  const numericValue = Math.min(Math.max(value, 0), maxPrice);
  priceInput.value = numericValue;
  priceRange.value = numericValue;
};

// Refetch al cambiar filtros
watch([priceRange, categoriasSeleccionadas, soloDestacados], () => {
  paginaActual.value = 1;
  fetchArticulos();
}, { deep: true });

// Obtener artículos del backend según filtros
const fetchArticulos = async () => {
  isLoading.value = true;
  try {
    const params = { 
      precio_max: priceRange.value,
      page: paginaActual.value
    };

    const categoriasActivas = Object.entries(categoriasSeleccionadas.value)
      .filter(([_, isSelected]) => isSelected)
      .map(([nombre]) => nombre);

    if (categoriasActivas.length > 0) params.categorias = categoriasActivas;
    if (soloDestacados.value) params.destacados = true;

    const response = await axios.get('/api/articulos', { params });
    articulos.value = response.data.data;
    pagination.value = response.data;
  } catch (error) {
    console.error('Error al obtener los artículos:', error);
  } finally {
    isLoading.value = false;
  }
};

// Abre el modal con los detalles de un artículo
const abrirDetalles = async (articulo) => {
  try {
    const response = await axios.get(`/api/articulos/${articulo.id}`);
    articuloSeleccionado.value = response.data.articulo;
    mostrarModal.value = true;
  } catch (error) {
    console.error('Error al cargar el artículo:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'No se pudo cargar el artículo. Inténtalo de nuevo.'
    });
  }
};

// Cierra el modal de detalles
const cerrarModal = () => {
  mostrarModal.value = false;
};

// Ejecuta fetchArticulos al cargar la página
onMounted(() => {
  fetchArticulos();
});

// Incluye cookies en las peticiones (útil para auth)
axios.defaults.withCredentials = true;

// Agrega un artículo a favoritos
const agregarAFavoritos = async () => {
  if (!user || !articuloSeleccionado.value) {
    Swal.fire({ icon: 'warning', title: 'Debes iniciar sesión', text: 'Inicia sesión para agregar el artículo a tus favoritos.' });
    return;
  }
  try {
    const response = await axios.post('/api/favoritos', { articulo_id: articuloSeleccionado.value.id });
    Swal.fire({ icon: 'success', title: '¡Éxito!', text: response.data.message });
  } catch (error) {
    console.error("Error al agregar a favoritos:", error);
    Swal.fire({ icon: 'error', title: '¡Error!', text: 'Hubo un problema al guardar el favorito. Intenta nuevamente.' });
  }
};

// Envía una puntuación y comentario para un artículo
const agregarPuntuacion = async () => {
  if (!user) {
    Swal.fire({ icon: 'warning', title: 'Inicia sesión', text: 'Debes iniciar sesión para puntuar un artículo.' });
    return;
  }

  if (!puntuacion.value) {
    Swal.fire({ icon: 'warning', title: 'Puntuación faltante', text: 'Por favor, selecciona una puntuación antes de enviar.' });
    return;
  }

  const confirmacion = await Swal.fire({
    icon: 'question',
    title: '¿Ya alquilaste este artículo?',
    text: 'Por favor, puntúa solo si ya alquilaste este artículo.',
    showCancelButton: true,
    confirmButtonText: 'Sí, lo alquilé',
    cancelButtonText: 'Cancelar',
  });

  if (!confirmacion.isConfirmed) return;

  try {
    const a = articuloSeleccionado.value;
    await axios.post('/puntuaciones', {
      articulo_id: a.id,
      puntuacion: puntuacion.value,
      comentario: comentario.value || "",
    });

    Swal.fire({ icon: 'success', title: '¡Puntuación agregada!', text: 'Gracias por tu calificación.' });

    if (a.puntuaciones) {
      a.puntuaciones.push({
        id: Date.now(),
        puntuacion: puntuacion.value,
        comentario: comentario.value,
        usuario: { name: user.name },
        created_at: new Date().toISOString()
      });
    }

    puntuacion.value = 0;
    comentario.value = '';

  } catch (error) {
    if (error.response?.status === 409) {
      Swal.fire({ icon: 'info', title: 'Ya puntuaste este artículo', text: 'Solo puedes puntuar una vez por artículo.' });
    } else {
      console.error("Error al agregar puntuación:", error);
      Swal.fire({ icon: 'error', title: '¡Error!', text: 'Hubo un problema al guardar la puntuación. Intenta nuevamente.' });
    }
  }
};

// Limpia todos los filtros seleccionados
const limpiarFiltros = () => {
  priceInput.value = 1000;
  priceRange.value = 1000;
  Object.keys(categoriasSeleccionadas.value).forEach(cat => {
    categoriasSeleccionadas.value[cat] = false;
  });
};

// Mini modal para ampliar imagen
const imagenAmpliada = ref(null);
const mostrarMiniModal = ref(false);

const abrirMiniModal = (imgUrl) => {
  imagenAmpliada.value = imgUrl;
  mostrarMiniModal.value = true;
};

const cerrarMiniModal = () => {
  mostrarMiniModal.value = false;
  imagenAmpliada.value = null;
};

// Verifica sesión y redirige a WhatsApp del dueño del artículo
const validarYRedirigirWhatsapp = () => {
  if (!user) {
    Swal.fire({ icon: 'info', title: 'Inicia sesión', text: 'Debes iniciar sesión para contactar por WhatsApp.' });
    return;
  }

  const telefono = articuloSeleccionado.value?.usuario?.telefono;
  const nombre = articuloSeleccionado.value?.usuario?.name || '';
  const articulo = articuloSeleccionado.value?.nombre || '';

  if (!telefono || telefono.length !== 8 || !/^\d+$/.test(telefono)) {
    Swal.fire({ icon: 'warning', title: 'Número no disponible', text: 'Este usuario no tiene un número de WhatsApp válido.' });
    return;
  }

  const mensaje = encodeURIComponent(`Hola ${nombre}, estoy interesad@ en tu artículo '${articulo}'. ¿Me podrías dar más información?`);
  const link = `https://wa.me/503${telefono}?text=${mensaje}`;
  window.open(link, '_blank');
};

</script>


<template>
  <Header />

  <!-- Sección principal -->
  <section class="flex flex-col lg:flex-row gap-6 bg-gray-100 p-16 text-gray-900">
    <!-- Filtros -->
    <div class="w-full lg:w-80 space-y-6 bg-white p-6 rounded-lg shadow-sm max-h-fit">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Filtros</h3>
        <button 
          @click="limpiarFiltros" 
          class="text-sm text-indigo-600 hover:text-indigo-800"
        >
          Limpiar filtros
        </button>
      </div>

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
      <!-- Checkbox para artículos destacados -->
        <div class="pt-4 border-t">
          <label class="flex items-center gap-2">
            <input 
              type="checkbox"
              v-model="soloDestacados"
              class="h-4 w-4 text-indigo-600 rounded"
            />
            <span class="text-sm">Solo destacados</span>
          </label>
        </div>


      <!-- Categorías -->
      <div class="border-t pt-6">
        <h4 class="text-sm font-medium mb-3">Categorías</h4>
        <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
          <label 
            v-for="categoria in categorias" 
            :key="categoria" 
            class="flex items-center gap-2"
          >
            <input 
              type="checkbox" 
              v-model="categoriasSeleccionadas[categoria]"
              class="h-4 w-4 text-indigo-600 rounded" 
            />
            <span class="text-sm">{{ categoria }}</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Productos -->
    <div class="flex-1 bg-white">
      <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <div class="flex justify-between items-center">
          <h2 class="text-2xl font-bold tracking-tight text-gray-900">
            Mostrando artículos ({{ articulos.length }})
          </h2>
        </div>
        
        <div v-if="isLoading" class="flex justify-center items-center h-64">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>

        <div v-else-if="articulos.length === 0" class="flex justify-center items-center h-64">
          <p class="text-gray-500 text-lg">No se encontraron artículos con los filtros seleccionados</p>
        </div>

        <div v-else class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-8">
          <div
            v-for="articulo in articulos"
            :key="articulo.id"
            class="bg-white rounded-xl shadow-md overflow-hidden flex flex-col transition-transform transform hover:-translate-y-1 hover:shadow-lg"
          >
            <swiper
              :modules="[Navigation, Pagination]"
              navigation
              pagination
              class="w-full h-60"
            >
              <swiper-slide v-for="imagen in articulo.imagenes" :key="imagen.id">
                <img
                    :src="imagen.link ? `/storage/${imagen.link}` : 'https://via.placeholder.com/300x200'"
                    :alt="articulo.nombre"
                    class="w-full h-60 object-cover cursor-zoom-in"
                    @click="abrirMiniModal('/storage/' + imagen.link)"
                  />

              </swiper-slide>
            </swiper>

            <div class="p-4 flex flex-col justify-between flex-grow">
              <div class="mb-3">
                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ articulo.nombre }}</h3>
                <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ articulo.descripcion }}</p>
              </div>
              
              <div class="mt-auto">
                <p class="text-indigo-600 font-bold text-lg mb-3">${{ articulo.precio }}</p>
                <p class="inline-block bg-indigo-100 text-indigo-700 text-xs font-semibold px-3 py-1 rounded-full mb-2">
                  Categoría: {{ articulo.categoria?.nombre || 'Sin categoría' }}
                </p>


                <button
                  @click.stop.prevent="abrirDetalles(articulo)"
                  class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition"
                >
                  Ver más
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Paginación -->
        <div v-if="pagination.last_page > 1" class="mt-12 px-4 pb-16">
              <div class="flex justify-center space-x-2">
                <button
                  @click="paginaActual = Math.max(1, paginaActual - 1); fetchArticulos()"
                  :disabled="paginaActual === 1"
                  class="px-4 py-2 border rounded disabled:opacity-50 disabled:cursor-not-allowed bg-white text-indigo-600 border-indigo-600"
                >
                  Anterior
                </button>

                <button
                  v-for="page in pagination.last_page"
                  :key="page"
                  @click="paginaActual = page; fetchArticulos()"
                  :class="[
                    'px-4 py-2 rounded border',
                    page === pagination.current_page
                      ? 'bg-indigo-600 text-white'
                      : 'bg-white text-indigo-600 border-indigo-600'
                  ]"
                >
                  {{ page }}
                </button>

                <button
                  @click="paginaActual = Math.min(pagination.last_page, paginaActual + 1); fetchArticulos()"
                  :disabled="paginaActual === pagination.last_page"
                  class="px-4 py-2 border rounded disabled:opacity-50 disabled:cursor-not-allowed bg-white text-indigo-600 border-indigo-600"
                >
                  Siguiente
                </button>
              </div>
            </div>


      </div>
    </div>
  </section>



 <!-- Modal Detalles -->
<div v-if="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
    
    <!-- Header del modal -->
    <div class="flex justify-between items-center px-6 py-4 border-b">
      <h2 class="text-3xl font-bold text-gray-800">{{ articuloSeleccionado?.nombre }}</h2>
      <button @click="cerrarModal" class="text-gray-400 hover:text-gray-600 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Contenido del modal -->
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
      
      <!-- Columna izquierda (Swiper + Puntuaciones) -->
      <div class="flex flex-col space-y-6">
        
        <!-- Imágenes -->
        <div class="rounded-2xl overflow-hidden shadow-md">
          <swiper :modules="[Navigation, Pagination]" navigation pagination>
            <swiper-slide v-for="imagen in articuloSeleccionado.imagenes" :key="imagen.id">
              <img
                :src="imagen.link ? `/storage/${imagen.link}` : 'https://via.placeholder.com/600x400'"
                :alt="articuloSeleccionado.nombre"
                class="w-full h-[400px] object-cover cursor-zoom-in"
                @click="abrirMiniModal('/storage/' + imagen.link)"
              />

            </swiper-slide>
          </swiper>
        </div>

        <!-- Puntuaciones -->
        <div class="bg-gray-100 p-4 rounded-2xl shadow-inner">
          <h3 class="text-lg font-semibold text-gray-800 mb-3">Puntuaciones:</h3>

          <div v-if="articuloSeleccionado.puntuaciones && articuloSeleccionado.puntuaciones.length > 0" class="space-y-3">
            <div 
              v-for="punt in articuloSeleccionado.puntuaciones" 
              :key="punt.id" 
              class="bg-white p-3 rounded-lg shadow-sm"
            >
              <div class="flex items-center justify-between mb-1">
                <div class="flex items-center space-x-2">
                  <p class="text-sm font-bold text-gray-700">{{ punt.usuario?.name || 'Anónimo' }}</p>
                  <p class="text-xs text-gray-500">{{ new Date(punt.created_at).toLocaleDateString() }}</p>
                </div>
                <div class="flex space-x-1">
                  <template v-for="n in 5" :key="n">
                    <svg
                      v-if="n <= punt.puntuacion"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 text-yellow-400"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.96a1 1 0 00.95.69h4.17c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.96c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.197-1.539-1.118l1.287-3.96a1 1 0 00-.364-1.118L2.055 9.387c-.783-.57-.38-1.81.588-1.81h4.17a1 1 0 00.951-.69l1.285-3.96z" />
                    </svg>
                  </template>
                </div>
              </div>
              <p class="text-sm text-gray-600">{{ punt.comentario || 'Sin comentario' }}</p>
            </div>
          </div>

          <div v-else>
            <p class="text-sm text-gray-500">Este artículo aún no tiene puntuaciones.</p>
          </div>
        </div>
      </div>

      <!-- Columna derecha (Información del artículo) -->
      <div class="flex flex-col justify-between">
        <div class="space-y-4">
          <p class="text-3xl font-bold text-indigo-600">${{ articuloSeleccionado.precio }}</p>
          <p class="text-gray-700 text-lg leading-relaxed">{{ articuloSeleccionado.descripcion }}</p>

          <div class="mt-4 border-t pt-4 text-sm text-gray-600 space-y-2">
            <p><strong>Categoría:</strong> {{ articuloSeleccionado.categoria?.nombre || 'Sin categoría' }}</p>
            <p><strong>Estado:</strong> {{ articuloSeleccionado.estado }}</p>
            <p><strong>Publicado por:</strong> <a :href="`/profile/${articuloSeleccionado.usuario?.id}`" 
              class="text-indigo-600 hover:text-indigo-800 underline">{{ articuloSeleccionado.usuario?.name || 'Desconocido' }}</a> 
            </p>
            <p><strong>Fecha de publicación:</strong> {{ new Date(articuloSeleccionado.created_at).toLocaleString() }}</p>
          </div>

          <!-- WhatsApp -->
          <button
                  @click="validarYRedirigirWhatsapp"
                  class="block w-full text-center bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 rounded-xl transition mt-6"
                >
                  Contactar por WhatsApp
          </button>



          <!-- Botón de Favoritos -->
          <button
            @click.stop.prevent="agregarAFavoritos"
            class="w-full mt-2 bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-2.5 rounded-xl transition"
          >
            Agregar a favoritos
          </button>
        </div>

        <!-- Puntuación -->
        <div class="mt-8">
          <h3 class="text-lg font-semibold text-gray-700 mb-2">Tu puntuación:</h3>
          <div class="flex space-x-1 mb-4">
            <template v-for="n in 5" :key="n">
              <svg
                @click="puntuacion = n"
                xmlns="http://www.w3.org/2000/svg"
                class="h-7 w-7 cursor-pointer transition duration-200"
                :class="puntuacion >= n ? 'text-yellow-400' : 'text-gray-300'"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.96a1 1 0 00.95.69h4.17c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.96c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.197-1.539-1.118l1.287-3.96a1 1 0 00-.364-1.118L2.055 9.387c-.783-.57-.38-1.81.588-1.81h4.17a1 1 0 00.951-.69l1.285-3.96z" />
              </svg>
            </template>
          </div>

          <textarea
            v-model="comentario"
            rows="3"
            placeholder="Comentario opcional..."
            class="w-full p-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none text-black"
          ></textarea>

          <button
            @click.stop.prevent="agregarPuntuacion"
            class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl transition"
          >
            Agregar Puntuación
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Mini Modal de Imagen -->
<div v-if="mostrarMiniModal" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center">
  <div class="relative max-w-4xl w-full p-4">
    <img
      :src="imagenAmpliada"
      class="rounded-lg shadow-xl max-h-[90vh] w-full object-contain"
    />
    <button
      @click="cerrarMiniModal"
      class="absolute top-3 right-3 bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition"
    >
      ✕
    </button>
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