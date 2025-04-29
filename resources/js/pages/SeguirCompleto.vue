<template>
  <Header />
  <div class="bg-gray-100">
    <div class="max-w-4xl mx-auto px-4 py-10">
      <div class="bg-white rounded-3xl shadow-lg p-8 flex flex-col md:flex-row items-center gap-8">
        <!-- Foto de perfil -->
        <div class="flex-shrink-0">
          <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Foto de usuario" class="w-48 h-48 rounded-full object-cover shadow-md border-4 border-white">
        </div>

        <!-- Información del usuario -->
        <div class="flex-1">
          <h2 class="text-3xl font-bold text-gray-800">{{ userName }}</h2>
          <p class="text-gray-500 text-sm">{{ userEmail }}</p>
          <p class="mt-2 text-gray-700">Diseñadora UX apasionada por crear experiencias digitales intuitivas. Amante del diseño minimalista, el café y los gatos.</p>

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
            <button @click="follow" :disabled="isFollowing" :class="buttonClass">
              {{ buttonText }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <Footer />
</template>

<script setup>
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import { ref, computed } from 'vue';

// Props
defineProps({
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

// Computed properties
const buttonText = computed(() => (isFollowing.value ? 'Siguiendo' : 'Seguir'));
const buttonClass = computed(() =>
  isFollowing.value
    ? 'bg-gray-400 cursor-not-allowed text-white font-medium px-6 py-2 rounded-xl transition'
    : 'bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-xl transition'
);

// Methods
const follow = () => {
  isFollowing.value = true;
};
</script>

<style scoped>
/* Agrega estilos específicos para esta vista aquí */
</style>
