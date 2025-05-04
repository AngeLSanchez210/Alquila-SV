<template>
    <div class="flex flex-col items-center justify-center h-screen bg-gray-100 text-center px-4">
      <h1 class="text-5xl font-bold text-red-600 mb-4">403 - Acceso Denegado</h1>
      <p class="text-gray-700 text-lg mb-6">
        Lo sentimos, no tienes permiso para ver esta página.
      </p>
      <Link href="/" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition">
        Ir al inicio
      </Link>
    </div>
  </template>
  
  <script setup>
  import { Link } from '@inertiajs/vue3';
  </script>
  