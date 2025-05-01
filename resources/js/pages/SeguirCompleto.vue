<template>
  <Header />
  <div class="bg-gray-100">
    <div class="max-w-4xl mx-auto px-4 py-10">
      <div class="bg-white rounded-3xl shadow-lg p-8 flex flex-col md:flex-row items-center gap-8">
        <!-- Foto de perfil -->
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

        <!-- Información del usuario -->
        <div class="flex-1">
          <h2 class="text-3xl font-bold text-gray-800">{{ userName }}</h2>
          <p class="text-gray-500 text-sm">{{ userEmail }}</p>

          <!-- Estadísticas -->
          <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-4 text-center">
            <div>
              <p class="text-lg font-semibold text-indigo-600">{{ articulosCount }}</p>
              <p class="text-sm text-gray-500">Productos publicados</p>
            </div>
            <div>
              <p class="text-lg font-semibold text-indigo-600">{{ seguidoresCount }}</p>
              <p class="text-sm text-gray-500">Seguidores</p>
            </div>
            <div v-if="isPremium">
              <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700 border border-yellow-400">
                Cuenta Premium ⭐
              </span>
            </div>
          </div>

          <!-- Botón seguir -->
          <div class="mt-6">
            <button @click="isFollowing ? unfollow() : follow()" :class="buttonClass">
              {{ buttonText }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para visualizar imagen -->
  <div v-if="mostrarImagenModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
    <div class="relative">
      <img :src="imagenSeleccionada" alt="Imagen ampliada" class="max-w-full max-h-screen rounded-lg">
      <button @click="cerrarImagenModal" class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-2 hover:bg-red-700">✕</button>
    </div>
  </div>

  <Footer />
</template>

<script setup>
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

// Desestructurar props directamente
const {
  userId,
  userName,
  userEmail,
  articulosCount,
  seguidoresCount,
  isPremium,
} = defineProps({
  userId: {
    type: [Number, String],
    required: true,
  },
  userName: {
    type: String,
    default: 'Sofía Ramírez',
  },
  userEmail: {
    type: String,
    default: 'sofia.ramirez@example.com',
  },
  articulosCount: {
    type: Number,
    default: 0,
  },
  seguidoresCount: {
    type: Number,
    default: 0,
  },
  isPremium: {
    type: Boolean,
    default: false,
  },
});

// Reactive state
const isFollowing = ref(false);
const userImage = ref(null);
const imagenSeleccionada = ref(null);
const mostrarImagenModal = ref(false);

const abrirImagenModal = (imagen) => {
  imagenSeleccionada.value = imagen;
  mostrarImagenModal.value = true;
};

const cerrarImagenModal = () => {
  mostrarImagenModal.value = false;
  imagenSeleccionada.value = null;
};

// Computed properties
const buttonText = computed(() => (isFollowing.value ? 'Dejar de seguir' : 'Seguir'));
const buttonClass = computed(() =>
  isFollowing.value
    ? 'bg-red-600 hover:bg-red-700 text-white font-medium px-6 py-2 rounded-xl transition'
    : 'bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-xl transition'
);

// Obtener el usuario logueado desde Inertia
const page = usePage();
const loggedInUserId = page.props.auth.user.id;

// Methods
const follow = async () => {
  if (loggedInUserId === userId) {
    alert('No puedes seguirte a ti mismo.');
    return;
  }

  try {
    const response = await axios.post('/seguir', {
      seguidor_id: loggedInUserId, // ID del usuario logueado
      seguido_id: userId,          // ID del usuario visualizado
    });
    if (response.status === 201) {
      isFollowing.value = true;
    }
  } catch (error) {
    if (error.response && error.response.status === 409) {
      console.warn('El usuario ya sigue a este perfil.');
    } else {
      console.error('Error al seguir al usuario:', error);
    }
  }
};

const unfollow = async () => {
  try {
    const response = await axios.post('/dejar-seguir', {
      seguidor_id: loggedInUserId, // ID del usuario logueado
      seguido_id: userId,          // ID del usuario visualizado
    });
    if (response.status === 200) {
      isFollowing.value = false;
    }
  } catch (error) {
    console.error('Error al dejar de seguir al usuario:', error);
  }
};

// Verificar si el usuario ya sigue al usuario visualizado
const checkFollowingStatus = async () => {
  try {
    const response = await axios.get(`/api/seguidores/${loggedInUserId}/${userId}`);
    if (response.status === 200) {
      isFollowing.value = response.data.isFollowing; // Asegurarse de que el backend devuelva un valor booleano
    } else {
      isFollowing.value = false; // Si no hay respuesta válida, asumir que no sigue
    }
  } catch (error) {
    console.error('Error al verificar el estado de seguimiento:', error);
    isFollowing.value = false; // En caso de error, asumir que no sigue
  }
};

const fetchUserImage = async () => {
  try {
    const response = await axios.get(`/api/users/${userId}/image`);
    userImage.value = response.data.image_url;
  } catch (error) {
    console.error('Error al cargar la imagen del usuario:', error);
  }
};

onMounted(() => {
  fetchUserImage();
  checkFollowingStatus(); // Asegurarse de que esta función se ejecute correctamente al montar el componente
});
</script>

<style scoped>
/* Agrega estilos específicos para esta vista aquí */
</style>
