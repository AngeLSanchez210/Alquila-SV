<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import type { User } from '@/types';

import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';

defineProps<{ user: User }>();

const activeSection = ref('info'); // Estado para controlar la sección activa

const setActiveSection = (section: string) => {
  activeSection.value = section;
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
        </nav>
      </aside>

      <!-- Contenido Principal -->
      <section class="w-3/4 ml-6">
        <!-- Información del Usuario -->
        <section
          v-if="activeSection === 'info'"
          class="bg-white p-6 rounded-lg shadow-md ">
          <h2 class="text-2xl font-bold mb-4 text-gray-700">Información Personal</h2>
          <div class="space-y-4">
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
        </section>

        <!-- Artículos del Usuario -->
        <section
          v-if="activeSection === 'articles'"
          class="bg-white p-6 rounded-lg shadow-md mb-auto">
          <h2 class="text-2xl font-bold mb-4 text-gray-700">Artículos Publicados</h2>
          <ul class="space-y-4">
            <li class="border-b pb-4">
              <h3 class="text-lg font-medium">Artículo 1</h3>
              <p class="text-sm text-gray-600">Descripción breve del artículo.</p>
              <p class="text-sm text-gray-600">Precio: $100</p>
            </li>
            <li class="border-b pb-4">
              <h3 class="text-lg font-medium">Artículo 2</h3>
              <p class="text-sm text-gray-600">Descripción breve del artículo.</p>
              <p class="text-sm text-gray-600">Precio: $200</p>
            </li>
          </ul>
        </section>

        <!-- Favoritos -->
        <section
          v-if="activeSection === 'favorites'"
          class="bg-white p-6 rounded-lg shadow-md mb-auto">
          <h2 class="text-2xl font-bold mb-4 text-gray-700">Favoritos</h2>
          <ul class="space-y-4">
            <li class="border-b pb-4">
              <h3 class="text-lg font-medium">Artículo Favorito 1</h3>
              <p class="text-sm text-gray-600">Descripción breve del artículo.</p>
            </li>
            <li class="border-b pb-4">
              <h3 class="text-lg font-medium">Artículo Favorito 2</h3>
              <p class="text-sm text-gray-600">Descripción breve del artículo.</p>
            </li>
          </ul>
        </section>
      </section>
    </div>
  </body>
  <Footer></Footer>
</template>
