<template>
    
  <main class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
    <div class="text-center">
      <p class="text-base font-semibold text-indigo-600">404</p>
      <h1 class="mt-4 text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-7xl">Página no encontrada</h1>
      <p class="mt-6 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8">Lo sentimos, no pudimos encontrar la página que buscas.</p>
      <div class="mt-10 flex items-center justify-center gap-x-6">
        <a href="/" class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Volver al inicio</a>
        <a href="/contact" class="text-sm font-semibold text-gray-900">Contactar soporte <span aria-hidden="true">&rarr;</span></a>
      </div>
    </div>
  </main>
</template>

<script>
export default {
  name: "Error404",
};
</script>

<style scoped>
/* Agrega estilos específicos si es necesario */
</style>
