<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const adminName = ref('');

onMounted(() => {
  adminName.value = 'Administrador';
});
</script>

<template>
  <Head title="Dashboard Administrador" />

  <AppLayout>
    <div class="p-8 bg-gray-100 min-h-screen space-y-8">

      <!-- Bienvenida -->
      <div class="bg-white p-6 rounded-2xl shadow-md flex flex-col md:flex-row justify-between items-center">
        <div>
          <h1 class="text-4xl font-bold text-gray-800 mb-2">¡Bienvenido, {{ adminName }}!</h1>
          <p class="text-gray-600 text-lg">Gestiona tu plataforma desde aquí.</p>
        </div>
        <img
          src="/img/admin-dashboard.png"
          alt="Dashboard Admin"
          class="w-40 h-40 object-cover mt-6 md:mt-0 rounded-full shadow-md"
        />
      </div>

      <!-- Accesos rápidos -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Usuarios -->
        <div class="bg-indigo-600 p-6 rounded-2xl shadow-lg flex flex-col justify-between hover:bg-indigo-700 transition duration-300">
          <h2 class="text-white text-xl font-bold mb-4">Usuarios</h2>
          <p class="text-indigo-100 text-sm mb-6">Gestiona los usuarios registrados en el sistema.</p>
          <Link
            href="/admin/users"
            class="bg-white text-indigo-600 font-semibold hover:bg-gray-100 transition rounded-lg py-2 text-center"
          >
            Administrar Usuarios
          </Link>
        </div>

        <!-- Artículos -->
        <div class="bg-green-600 p-6 rounded-2xl shadow-lg flex flex-col justify-between hover:bg-green-700 transition duration-300">
          <h2 class="text-white text-xl font-bold mb-4">Artículos</h2>
          <p class="text-green-100 text-sm mb-6">Gestiona los artículos disponibles.</p>
          <Link
            href="/admin/items"
            class="bg-white text-green-600 font-semibold hover:bg-gray-100 transition rounded-lg py-2 text-center"
          >
            Administrar Artículos
          </Link>
        </div>

        <!-- Planes -->
        <div class="bg-yellow-500 p-6 rounded-2xl shadow-lg flex flex-col justify-between hover:bg-yellow-600 transition duration-300">
          <h2 class="text-white text-xl font-bold mb-4">Planes</h2>
          <p class="text-yellow-100 text-sm mb-6">Ajusta los planes y sus configuraciones.</p>
          <Link
            href="/planes"
            class="bg-white text-yellow-600 font-semibold hover:bg-gray-100 transition rounded-lg py-2 text-center"
          >
            Administrar Planes
          </Link>
        </div>

      </div>

    </div>
  </AppLayout>
</template>
