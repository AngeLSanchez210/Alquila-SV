<script setup>
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import Swal from 'sweetalert2';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios'; 
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

const props = defineProps({
  articulo: Object
});

const articulo = ref(props.articulo);
const comentario = ref('');
const puntuacion = ref(0);
const user = usePage().props.auth.user;

// --- Mini Modal Imagen ---
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
// --------------------------

const agregarAFavoritos = async () => {
  if (!user) {
    Swal.fire({
      icon: 'warning',
      title: 'Debes iniciar sesión',
      text: 'Inicia sesión para agregar el artículo a tus favoritos.',
    });
    return;
  }

  try {
    const response = await axios.post('/api/favoritos', {
      articulo_id: articulo.value.id
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
  if (!user || !puntuacion.value) {
    Swal.fire({
      icon: 'warning',
      title: 'Debes iniciar sesión y proporcionar una puntuación',
      text: 'Inicia sesión para agregar una puntuación al artículo.',
    });
    return;
  }

  try {
    await axios.post('/puntuaciones', {
      articulo_id: articulo.value.id,
      puntuacion: puntuacion.value,
      comentario: comentario.value || "",
    });

    Swal.fire({
      icon: 'success',
      title: '¡Puntuación agregada!',
      text: 'Tu puntuación se ha registrado correctamente.',
    });
  } catch (error) {
    if (error.response && error.response.status === 409) {
      Swal.fire({
        icon: 'info',
        title: 'Ya puntuaste este artículo',
        text: 'Solo puedes puntuar una vez por artículo.',
      });
    } else {
      console.error("Error al agregar puntuación:", error);
      Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: 'Hubo un problema al guardar la puntuación. Intenta nuevamente.',
      });
    }
  }
};
</script>

<template>
  <Header />

  <section class="bg-gray-50 min-h-screen px-6 py-12">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-10 items-center bg-white p-8 rounded-2xl shadow-xl">
      
      <!-- Imágenes -->
      <div>
        <swiper :modules="[Navigation, Pagination]" navigation pagination class="rounded-2xl overflow-hidden shadow-md">
          <swiper-slide v-for="img in articulo.imagenes" :key="img.id">
            <img
              :src="img.link ? `/storage/${img.link}` : 'https://via.placeholder.com/600'"
              :alt="articulo.nombre"
              class="w-full h-[400px] object-cover cursor-pointer hover:opacity-90 transition"
              @click="abrirMiniModal(`/storage/${img.link}`)"
            />
          </swiper-slide>
        </swiper>
      </div>

      <!-- Información -->
      <div class="flex flex-col justify-between h-full">
        <div class="space-y-4">
          <h1 class="text-4xl font-bold text-gray-900">{{ articulo.nombre }}</h1>
          <p class="text-gray-600 leading-relaxed">{{ articulo.descripcion }}</p>
          <p class="text-3xl font-semibold text-indigo-600">${{ articulo.precio }}</p>

          <div class="mt-4 space-y-2 text-gray-700 text-sm">
            <p><strong>Categoría:</strong> {{ articulo.categoria?.nombre || 'Sin categoría' }}</p>
            <p>
                  <strong>Publicado por:</strong> 
                  <a 
                    :href="`/profile/${articulo.usuario?.id}`" 
                    class="text-indigo-600 hover:text-indigo-800 underline"
                  >
                    {{ articulo.usuario?.name || 'Desconocido' }}
                  </a>
                </p>
            <p><strong>Fecha de Publicacion:</strong> {{ new Date(articulo.created_at).toLocaleString() }}</p>
          </div>

          <!-- WhatsApp Contact -->
          <a
            v-if="articulo.usuario?.telefono && articulo.usuario.telefono.length === 8"
            :href="`https://wa.me/503${articulo.usuario.telefono}?text=Hola ${articulo.usuario.name}, estoy interesad@ en tu artículo '${articulo.nombre}'. ¿Me podrías dar más información?`"
            target="_blank"
            class="inline-block w-full text-center bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-xl transition duration-300"
          >
            Contactar por WhatsApp
          </a>

          <!-- Favoritos -->
          <button
            @click="agregarAFavoritos"
            class="w-full mt-2 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold py-2 rounded-xl transition duration-300"
          >
            Agregar a favoritos
          </button>
        </div>

        <!-- Puntuación -->
        <div class="mt-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-2">Tu puntuación:</h2>
          <div class="flex space-x-1 mb-4">
            <template v-for="n in 5" :key="n">
              <svg
                @click="puntuacion = n"
                xmlns="http://www.w3.org/2000/svg"
                class="h-7 w-7 cursor-pointer"
                :class="puntuacion >= n ? 'text-yellow-400' : 'text-gray-300'"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.96a1 1 0 00.95.69h4.17c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.96c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.197-1.539-1.118l1.287-3.96a1 1 0 00-.364-1.118L2.055 9.387c-.783-.57-.38-1.81.588-1.81h4.17a1 1 0 00.951-.69l1.285-3.96z"/>
              </svg>
            </template>
          </div>

          <textarea
            v-model="comentario"
            rows="3"
            placeholder="Comentario opcional..."
            class="w-full p-3 border text-black border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none"
          ></textarea>

          <button
            @click="agregarPuntuacion"
            class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-xl transition duration-300"
          >
            Agregar Puntuación
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- MINI MODAL IMAGEN -->
  <div v-if="mostrarMiniModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl overflow-hidden shadow-lg p-4 max-w-lg w-full relative">
      <img
        :src="imagenAmpliada"
        alt="Vista ampliada"
        class="w-full object-contain rounded"
      />
      <button
        @click="cerrarMiniModal"
        class="absolute top-2 right-2 bg-red-600 text-white rounded-full px-3 py-1 hover:bg-red-700"
      >
        ✕
      </button>
    </div>
  </div>

  <Footer />
</template>
