<script setup>
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import { router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const irAFiltradosPorCategoria = (categoria) => {
  router.visit('/articulos', {
    data: { categoria },
    method: 'get',
    preserveScroll: true,
  });
};

const planes = ref([]);

onMounted(async () => {
  try {
    const response = await axios.get('/api/planes');
    planes.value = response.data;
  } catch (error) {
    console.error('Error al cargar los planes:', error);
  }
});
</script>

<template>
  <Header class="relative z-50"></Header>

  <body class="bg-gray-100">
    <!-- Main Content -->
    <main class="mx-auto text-gray-800">
      <!-- Hero Section -->
      <div class="relative overflow-hidden bg-white w-full mb-1 z-10">
        <div class="pt-16 pb-80 sm:pt-24 sm:pb-40 lg:pt-40 lg:pb-48">
          <div class="relative mx-auto max-w-7xl px-4 sm:static sm:px-6 lg:px-8">
            <div class="sm:max-w-lg">
              <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">Todo lo que necesitas a un solo clic</h1>
              <p class="mt-4 text-xl text-gray-500">Alquila equipos, herramientas y más sin la complicación de comprarlos. Soluciones temporales para necesidades reales.</p>
            </div>
            <div>
              <div class="mt-10">
                <!-- Decorative image grid -->
                <div aria-hidden="true" class="pointer-events-none lg:absolute lg:inset-y-0 lg:mx-auto lg:w-full lg:max-w-7xl">
                  <div class="absolute transform sm:top-0 sm:left-1/2 sm:translate-x-8 lg:top-1/2 lg:left-1/2 lg:-translate-y-1/2 lg:translate-x-8">
                    <div class="flex items-center space-x-6 lg:space-x-8">
                      <div class="grid shrink-0 grid-cols-1 gap-y-6 lg:gap-y-8">
                        <div class="h-64 w-44 overflow-hidden rounded-lg sm:opacity-0 lg:opacity-100">
                          <img src="https://images.unsplash.com/photo-1426927308491-6380b6a9936f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1771&q=80" alt="Herramientas de construcción" class="size-full object-cover">
                        </div>
                        <div class="h-64 w-44 overflow-hidden rounded-lg">
                          <img src="https://images.unsplash.com/photo-1542038784456-1ea8e935640e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1770&q=80" alt="Cámara fotográfica profesional" class="size-full object-cover">
                        </div>
                      </div>
                      <div class="grid shrink-0 grid-cols-1 gap-y-6 lg:gap-y-8">
                        <div class="h-64 w-44 overflow-hidden rounded-lg">
                          <img src="https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1770&q=80" alt="Equipos de camping" class="size-full object-cover">
                        </div>
                        <div class="h-64 w-44 overflow-hidden rounded-lg">
                          <img src="https://images.unsplash.com/photo-1485965120184-e220f721d03e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1770&q=80" alt="Bicicletas de montaña" class="size-full object-cover">
                        </div>
                        <div class="h-64 w-44 overflow-hidden rounded-lg">
                          <img src="https://images.unsplash.com/photo-1593305841991-05c297ba4575?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1757&q=80" alt="Consolas de videojuegos" class="size-full object-cover">
                        </div>
                      </div>
                      <div class="grid shrink-0 grid-cols-1 gap-y-6 lg:gap-y-8">
                        <div class="h-64 w-44 overflow-hidden rounded-lg">
                          <img src="https://images.unsplash.com/photo-1545454675-3531b543be5d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1770&q=80" alt="Equipo de sonido" class="size-full object-cover">
                        </div>
                        <div class="h-64 w-44 overflow-hidden rounded-lg">
                          <img src="https://images.unsplash.com/photo-1429962714451-bb934ecdc4ec?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1770&q=80" alt="Muebles para eventos" class="size-full object-cover">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <a href="/articulos" class="inline-block rounded-md border border-transparent bg-indigo-600 px-8 py-3 text-center font-medium text-white hover:bg-indigo-700">Alquilar ahora</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Featured Categories Section -->
      <div class="bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div class="mx-auto max-w-2xl py-16 sm:py-24 lg:max-w-none lg:py-25">
            <h2 class="text-2xl font-bold text-gray-900">Alquila por categorías</h2>

            <div class="mt-6 space-y-12 lg:grid lg:grid-cols-3 lg:gap-x-6 lg:space-y-0">
              <div class="group relative">
                <a :href="`/articulos?categoria=${encodeURIComponent('Herramientas')}`">
                  <img src="https://images.unsplash.com/photo-1581244277943-fe4a9c777189?ixlib=rb-4.0.3&auto=format&fit=crop&w=1771&q=80" alt="Herramientas" class="w-full rounded-lg object-cover group-hover:opacity-75 max-sm:h-80 sm:aspect-2/1 lg:aspect-square">
                  <h3 class="mt-6 text-sm text-gray-500">Herramientas</h3>
                  <p class="text-base font-semibold text-gray-900">Todo para construcción y reparaciones</p>
                </a>
              </div>

              <div class="group relative">
                <a :href="`/articulos?categoria=${encodeURIComponent('Equipos para eventos')}`">
                  <img src="https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80" alt="Eventos" class="w-full rounded-lg object-cover group-hover:opacity-75 max-sm:h-80 sm:aspect-2/1 lg:aspect-square">
                  <h3 class="mt-6 text-sm text-gray-500">Equipos para eventos</h3>
                  <p class="text-base font-semibold text-gray-900">Sonido, iluminación y más</p>
                </a>
              </div>

              <div class="group relative">
                <a :href="`/articulos?categoria=${encodeURIComponent('Muebles')}`">
                  <img 
                    src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80" 
                    alt="Muebles para el hogar y eventos" 
                    class="w-full rounded-lg object-cover group-hover:opacity-75 max-sm:h-80 sm:aspect-2/1 lg:aspect-square"
                  >
                  <h3 class="mt-6 text-sm text-gray-500">Muebles</h3>
                  <p class="text-base font-semibold text-gray-900">Para el hogar, oficina o eventos</p>
                </a>
              </div>

            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Benefits Section -->
    <section class="text-gray-600 body-font bg-white mt-1">
      <div class="container mx-auto flex px-5 py-24 md:flex-row flex-col items-center">
        <div class="lg:flex-grow md:w-1/2 lg:pr-24 md:pr-16 flex flex-col md:items-start md:text-left mb-16 md:mb-0 items-center text-center">
          <h1 class="mt-2 text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-6xl">Regístrate ahora
            <br class="hidden lg:inline-block">y disfruta de todos los beneficios
          </h1>
          <p class="mb-8 mt-3 leading-relaxed">Accede a nuestro catálogo completo de productos para alquilar, recibe recomendaciones personalizadas según tus necesidades y obtén descuentos exclusivos en tus primeros alquileres. Ahorra dinero alquilando en lugar de comprar equipo que usarás pocas veces.</p>
          <div class="flex justify-center gap-4">
            <button
              @click="$inertia.visit(route('register'))"
              class="inline-flex items-center justify-center text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg transition"
            >
              Registrarse
            </button>

            <a
              href="/articulos"
              class="inline-flex items-center justify-center text-white bg-indigo-600 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-700 rounded text-lg transition"
            >
              Ver catálogo
            </a>
          </div>
        </div>
        <div class="lg:max-w-lg lg:w-full md:w-1/2 w-5/6">
          <img class="object-cover object-center rounded" alt="Persona feliz alquilando equipo" src="https://images.unsplash.com/photo-1560438718-eb61ede255eb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1770&q=80">
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <div class="relative isolate bg-gradient-to-r from-blue-100 to-blue-500 px-6 py-24 sm:py-32 lg:px-8">
      <div class="absolute inset-x-0 -top-3 -z-10 transform-gpu overflow-hidden px-36 blur-3xl" aria-hidden="true">
        <div class="mx-auto aspect-1155/678 w-[72.1875rem] bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
      </div>
      <div class="mx-auto max-w-4xl text-center">
        <h2 class="text-base/7 font-semibold text-indigo-600">Planes de membresía</h2>
        <p class="mt-2 text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-6xl">Elige el plan perfecto para ti</p>
      </div>
      <p class="mx-auto mt-6 max-w-2xl text-center text-lg font-medium text-pretty text-gray-700 sm:text-xl/8">Nuestros planes de membresía ofrecen beneficios exclusivos, descuentos y prioridad en reservas. Encuentra la opción ideal para tus necesidades de alquiler.</p>
      
      <div class="mx-auto mt-16 grid max-w-lg grid-cols-1 items-center gap-y-6 sm:mt-20 sm:gap-y-8 lg:max-w-5xl lg:grid-cols-3">
        <div
          v-for="(plan, index) in planes"
          :key="plan.id"
          :class="[
            'relative rounded-3xl p-8 ring-1',
            plan.precio === Math.max(...planes.map(p => p.precio))
              ? 'bg-gray-900 text-gray-300 shadow-2xl ring-gray-900/10'
              : 'bg-white text-gray-600 ring-gray-900/10',
          ]"
        >
          <h3
            :id="`tier-${plan.nombre.toLowerCase()}`"
            :class="plan.precio === Math.max(...planes.map(p => p.precio)) ? 'text-indigo-400' : 'text-indigo-600'"
          >
            {{ plan.nombre }}
          </h3>
          <p class="mt-4 flex items-baseline gap-x-2">
            <span
              :class="plan.precio === Math.max(...planes.map(p => p.precio)) ? 'text-white' : 'text-gray-900'"
              class="text-5xl font-semibold tracking-tight"
            >
              ${{ plan.precio }}
            </span>
            <span
              :class="plan.precio === Math.max(...planes.map(p => p.precio)) ? 'text-gray-400' : 'text-gray-500'"
              class="text-base"
            >
              /mes
            </span>
          </p>
          <!-- Mostrar solo el primer texto antes de la coma -->
          <p
            class="mt-6 text-base/7"
            :class="plan.precio === Math.max(...planes.map(p => p.precio)) ? 'text-gray-300' : 'text-gray-600'"
          >
            {{ plan.descripcion.split(',')[0] }}
          </p>
          <!-- Listar los demás elementos después de la coma -->
          <ul
            role="list"
            class="mt-8 space-y-3 text-sm/6"
            :class="plan.precio === Math.max(...planes.map(p => p.precio)) ? 'text-gray-300' : 'text-gray-600'"
          >
            <li
              v-for="(item, i) in plan.descripcion.split(',').slice(1)"
              :key="i"
              class="flex gap-x-3"
            >
              <svg
                :class="plan.precio === Math.max(...planes.map(p => p.precio)) ? 'text-indigo-400' : 'text-indigo-600'"
                class="h-6 w-5 flex-none"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                  clip-rule="evenodd"
                />
              </svg>
              {{ item }}
            </li>
          </ul>
          <a
            v-if="plan.precio > 0"
            :href="`/pay?plan_id=${plan.id}`"
            :aria-describedby="`tier-${plan.nombre.toLowerCase()}`"
            class="mt-8 block rounded-md px-3.5 py-2.5 text-center text-sm font-semibold"
            :class="plan.precio === Math.max(...planes.map(p => p.precio))
              ? 'bg-indigo-500 text-white hover:bg-indigo-400 focus-visible:outline-indigo-500'
              : 'text-indigo-600 ring-1 ring-indigo-200 ring-inset hover:ring-indigo-300 focus-visible:outline-indigo-600'"
          >
            {{ plan.precio === Math.max(...planes.map(p => p.precio)) ? 'Contactar ventas' : 'Comenzar ahora' }}
          </a>
        </div>
      </div>
    </div>  
  </body>

  <Footer></Footer>
</template>