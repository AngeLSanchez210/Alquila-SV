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

  <section class="bg-gray-100 min-h-screen px-4 py-12">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-lg grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Imágenes -->
      <div>
        <swiper :modules="[Navigation, Pagination]" navigation pagination class="rounded-lg shadow">
          <swiper-slide v-for="img in articulo.imagenes" :key="img.id">
            <img
              :src="img.link ? `/storage/${img.link}` : 'https://via.placeholder.com/500'"
              :alt="articulo.nombre"
              class="w-full h-96 object-cover rounded-md"
            />
          </swiper-slide>
        </swiper>
      </div>

      <!-- Información -->
      <div class="flex flex-col justify-between">
        <div>
          <h1 class="text-3xl font-bold mb-2 text-black">{{ articulo.nombre }}</h1>
          <p class="text-gray-600 mb-4 text-black" >{{ articulo.descripcion }}</p>
          <p class="text-indigo-600 text-black font-bold text-2xl mb-4">${{ articulo.precio }}</p>

          <p class="text-sm font-bold text-black"><strong>Categoría:</strong> {{ articulo.categoria?.nombre || 'Sin categoría' }}</p>
          <p class="text-sm font-bold text-black"><strong>Publicado por:</strong> {{ articulo.usuario?.name || 'Desconocido' }}</p>
          <p class="text-sm font-bold  text-black"><strong>Publicado el:</strong> {{ new Date(articulo.created_at).toLocaleString() }}</p>
        </div>

        <!-- Contactar por WhatsApp -->
              <a
                v-if="articulo.usuario?.telefono && articulo.usuario.telefono.length === 8"
                :href="`https://wa.me/503${articulo.usuario.telefono}?text=Hola ${articulo.usuario.name}, estoy interesad@ en tu artículo '${articulo.nombre}'. ¿Me podrías dar más información?`"
                target="_blank"
                class="w-full inline-flex justify-center items-center gap-2 bg-green-500 py-2 px-4 rounded font-medium text-white hover:bg-green-600 transition"
              >
                Contactar por WhatsApp
              </a>


        <div class="space-y-3 mt-6">
          <button @click="agregarAFavoritos" class="w-full bg-yellow-400 text-black py-2 rounded hover:bg-yellow-500">
            Agregar a favoritos 
          </button>
         
        </div>

        <!-- Puntuación -->
        <div class="mt-6">
          <label class="text-sm font-semibold">Tu puntuación:</label>
          <div class="flex space-x-1 mt-2">
            <template v-for="n in 5" :key="n">
              <svg
                @click="puntuacion = n"
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 cursor-pointer"
                :class="puntuacion >= n ? 'text-yellow-400' : 'text-gray-300'"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.96a1 1 0 00.95.69h4.17c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.96c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.197-1.539-1.118l1.287-3.96a1 1 0 00-.364-1.118L2.055 9.387c-.783-.57-.38-1.81.588-1.81h4.17a1 1 0 00.951-.69l1.285-3.96z"/>
              </svg>
            </template>
          </div>

          <!-- Comentario -->
          <textarea
            v-model="comentario"
            rows="3"
            class="w-full mt-4 p-2 border border-gray-300 rounded text-sm text-black"
            placeholder="Comentario opcional..."
          ></textarea>

          <button
            @click="agregarPuntuacion"
            class="mt-3 w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700"
          >
            Agregar Puntuación
          </button>
        </div>
      </div>
    </div>
  </section>

  <Footer />
</template>
