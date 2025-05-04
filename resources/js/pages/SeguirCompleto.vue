<template>
  <Header />

  <div class="bg-gray-100">
    <div class="max-w-4xl mx-auto px-4 py-10">
      <!-- Perfil -->
      <div class="bg-white rounded-3xl shadow-lg p-8 flex flex-col md:flex-row items-center gap-8">
        <div class="flex-shrink-0">
          <img
            v-if="userImage"
            :src="`/storage/${userImage}`"
            alt="Foto de usuario"
            class="w-48 h-48 rounded-full object-cover shadow-md border-4 border-white cursor-pointer"
            @click="abrirImagenModal(`/storage/${userImage}`)"
          />
          <div v-else class="w-48 h-48 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
            Sin Imagen
          </div>
        </div>

        <div class="flex-1">
          <h2 class="text-3xl font-bold text-gray-800">{{ userName }}</h2>
          <p class="text-gray-500 text-sm">{{ userEmail }}</p>
          <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-4 text-center">
            <div>
              <p class="text-lg font-semibold text-indigo-600">{{ articulosCount }}</p>
              <p class="text-sm text-gray-500">Productos publicados</p>
            </div>
            <div @click="abrirModalSeguidores" class="cursor-pointer hover:text-indigo-800">
              <p class="text-lg font-semibold text-indigo-600 underline">{{ seguidoresCount }}</p>
              <p class="text-sm text-gray-500">Seguidores</p>
            </div>
            <div v-if="isPremium">
              <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700 border border-yellow-400">
                Cuenta Premium ⭐
              </span>
            </div>
          </div>
          <div class="mt-6">
            <button @click="isFollowing ? unfollow() : follow()" :class="buttonClass">
              {{ buttonText }}
            </button>
          </div>
        </div>
      </div>

      <!-- Artículos -->
      <div class="mt-12 max-w-6xl mx-auto px-4 py-10 bg-gray-50 rounded-xl shadow-inner">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Artículos publicados</h3>
        <div v-if="articulos.length" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="articulo in articulos"
            :key="articulo.id"
            class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition cursor-pointer"
            @click="verArticulo(articulo.id)"
          >
            <div class="w-full h-40 bg-gray-100">
              <img
                v-if="articulo.imagenes.length"
                :src="`/storage/${articulo.imagenes[0].link}`"
                :alt="articulo.nombre"
                class="w-full h-full object-cover"
              />
              <div v-else class="flex items-center justify-center h-full text-gray-400">Sin imagen</div>
            </div>
            <div class="p-3">
              <h4 class="text-base font-semibold text-gray-800 truncate">{{ articulo.nombre }}</h4>
            </div>
          </div>
        </div>
        <div v-else class="text-gray-500 text-center">Este usuario no ha publicado ningún artículo aún.</div>
      </div>
    </div>
  </div>

  <!-- Modal de imagen -->
  <div v-if="mostrarImagenModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
    <div class="relative">
      <img :src="imagenSeleccionada" alt="Imagen ampliada" class="max-w-full max-h-screen rounded-lg" />
      <button @click="cerrarImagenModal" class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-2 hover:bg-red-700">✕</button>
    </div>
  </div>

  <!-- Modal de seguidores -->
  <div
  v-if="mostrarModalSeguidores"
  class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
>
  <div class="bg-white p-6 rounded-2xl shadow-2xl max-h-[80vh] overflow-y-auto w-96 relative">
    <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">Seguidores</h3>

    <ul v-if="seguidores.length" class="space-y-4">
      <li
        v-for="seguidor in seguidores"
        :key="seguidor.id"
        @click="verPerfil(seguidor.id)"
        class="flex items-center gap-4 hover:bg-gray-100 p-2 rounded-lg cursor-pointer transition"
      >
        <span class="text-gray-800 font-medium">{{ seguidor.name }}</span>
      </li>
    </ul>

    <p v-else class="text-gray-500 text-center">Este usuario no tiene seguidores aún.</p>

    <button @click="cerrarModalSeguidores" class="absolute top-2 right-2 text-gray-400 hover:text-red-600 text-xl">✕</button>
  </div>
</div>


  <Footer />
</template>

<script setup>
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { usePage, router } from '@inertiajs/vue3';

const {
  userId,
  userName,
  userEmail,
  articulosCount,
  seguidoresCount,
  isPremium,
  articulos,
} = defineProps({
  userId: [Number, String],
  userName: String,
  userEmail: String,
  articulosCount: Number,
  seguidoresCount: Number,
  isPremium: Boolean,
  articulos: {
    type: Array,
    default: () => [],
  },
});

const isFollowing = ref(false);
const userImage = ref(null);
const imagenSeleccionada = ref(null);
const mostrarImagenModal = ref(false);
const mostrarModalSeguidores = ref(false);
const seguidores = ref([]);

const verPerfil = (id) => {
  router.visit(`/profile/${id}`);
};

const abrirImagenModal = (img) => {
  imagenSeleccionada.value = img;
  mostrarImagenModal.value = true;
};

const cerrarImagenModal = () => {
  mostrarImagenModal.value = false;
  imagenSeleccionada.value = null;
};

const cerrarModalSeguidores = () => {
  mostrarModalSeguidores.value = false;
};

const buttonText = computed(() => (isFollowing.value ? 'Dejar de seguir' : 'Seguir'));
const buttonClass = computed(() =>
  isFollowing.value
    ? 'bg-red-600 hover:bg-red-700 text-white font-medium px-6 py-2 rounded-xl transition'
    : 'bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-xl transition'
);

const page = usePage();
const loggedInUserId = page.props.auth.user.id;

const follow = async () => {
  if (loggedInUserId === userId) return alert('No puedes seguirte a ti mismo.');
  try {
    const res = await axios.post('/seguir', {
      seguidor_id: loggedInUserId,
      seguido_id: userId,
    });
    if (res.status === 201) isFollowing.value = true;
  } catch (error) {
    if (error.response?.status === 409) console.warn('Ya sigues a este usuario.');
    else console.error('Error al seguir:', error);
  }
};

const unfollow = async () => {
  try {
    const res = await axios.post('/dejar-seguir', {
      seguidor_id: loggedInUserId,
      seguido_id: userId,
    });
    if (res.status === 200) isFollowing.value = false;
  } catch (error) {
    console.error('Error al dejar de seguir:', error);
  }
};

const checkFollowingStatus = async () => {
  try {
    const res = await axios.get(`/api/seguidores/${loggedInUserId}/${userId}`);
    if (res.status === 200) isFollowing.value = res.data.isFollowing;
  } catch (error) {
    console.error('Error al verificar seguimiento:', error);
  }
};

const fetchUserImage = async () => {
  try {
    const res = await axios.get(`/api/users/${userId}/image`);
    userImage.value = res.data.image_url;
  } catch (error) {
    console.error('Error al cargar imagen:', error);
  }
};

const abrirModalSeguidores = async () => {
  console.log('🔍 userId usado en API:', userId); // DEBUG
  try {
    const res = await axios.get(`/api/seguidores/lista/${userId}`);
    seguidores.value = res.data;
    mostrarModalSeguidores.value = true;
  } catch (error) {
    console.error('Error al obtener seguidores:', error);
  }
};

const verArticulo = (id) => {
  router.visit(`/articulos/ver/${id}`);
};

onMounted(() => {
  fetchUserImage();
  checkFollowingStatus();
});
</script>
