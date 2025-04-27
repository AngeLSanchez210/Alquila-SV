<script setup>
import { ref, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import axios from 'axios';

const isMobileMenuOpen = ref(false);
const { props } = usePage();

const searchQuery = ref('');
const searchResults = ref([]);
const showDropdown = ref(false);

watch(searchQuery, async (newVal) => {
  if (newVal.length < 2) {
    searchResults.value = [];
    showDropdown.value = false;
    return;
  }

  try {
    const response = await axios.get(route('api.articulos.search'), {
      params: { q: newVal }
    });
    searchResults.value = response.data;
    showDropdown.value = true;
  } catch (error) {
    console.error(error);
  }
});

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false;
};

const toggleMenu = () => {
  const menu = document.getElementById('profile-menu');
  menu?.classList.toggle('hidden');
};

const closeProfileMenu = () => {
  const menu = document.getElementById('profile-menu');
  menu?.classList.add('hidden');
};

function redirectToUserInfo (section){
  const userId = props.auth.user.id;
  window.location.href = `/user/${userId}?section=${section}`;
};

onMounted(() => {
  document.addEventListener('click', (event) => {
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileToggleButton = document.querySelector('.mobile-menu-toggle');
    const profileMenu = document.getElementById('profile-menu');
    const avatar = document.querySelector('[data-popover-target="profile-menu"]');
    if (
      mobileMenu &&
      mobileToggleButton &&
      !mobileMenu.contains(event.target) &&
      !mobileToggleButton.contains(event.target)
    ) {
      closeMobileMenu();
    }
    if (profileMenu && avatar && !profileMenu.contains(event.target) && !avatar.contains(event.target)) {
      closeProfileMenu();
    }
    if (!event.target.closest('.autocomplete-wrapper')) {
      showDropdown.value = false;
    }
  });
});

const goToArticulo = (id) => {
  router.visit(route('articulos.ver', id));
};



const menuItems = [
  { name: 'Vehículos', href: '#' },
  { name: 'Herramientas', href: '#' },
  { name: 'Tecnología', href: '#' },
];
</script>

<template>
  <header class="relative bg-white">
    <nav aria-label="Top" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="border-b border-gray-200">
        <div class="flex h-16 items-center">
          <!-- Mobile menu toggle -->
          <button
            type="button"
            class="mobile-menu-toggle relative rounded-md bg-white p-2 text-gray-400 hover:text-gray-800 lg:hidden"
            @click="toggleMobileMenu"
          >
            <span class="absolute -inset-0.5"></span>
            <span class="sr-only">Open menu</span>
            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
          </button>

          <!-- Logo -->
          <div class="ml-2 flex lg:ml-0">
            <a href="/">
              <span class="sr-only">Alquila SV</span>
              <img class="h-28 w-auto object-cover" src="/img/logofinal.png" alt="Alquila SV Logo">
            </a>
          </div>


          <!-- Unified menu -->
          <div
            :class="{'hidden lg:block lg:self-stretch': !isMobileMenuOpen, ' mobile-menu absolute top-16 left-0 w-full bg-white shadow-lg lg:hidden': isMobileMenuOpen}"
          >
            <div :class="isMobileMenuOpen ? 'flex flex-col space-y-4 p-4' : 'flex h-full space-x-8'">
              <a
                v-for="item in menuItems"
                :key="item.name"
                :href="item.href"
                class="lg:ml-7 flex items-center text-sm font-medium text-gray-600 hover:text-gray-900"
                @click="isMobileMenuOpen && closeMobileMenu"
              >
                {{ item.name }}
              </a>
            </div>
          </div>

          <div class="lg:ml-6 flex items-center flex-grow">
            <!-- Search -->
            <div class="relative w-full autocomplete-wrapper">
              <div class="flex items-center border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-white focus-within:ring-2 focus-within:ring-indigo-500">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                  <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                  </g>
                </svg>
                <input
                  type="search"
                  v-model="searchQuery"
                  placeholder="Buscar artículos..."
                  class="flex-1 ml-3 outline-none text-gray-800 bg-transparent placeholder-gray-400 text-sm"
                />
              </div>

              <!-- Dropdown de resultados -->
              <div
                v-if="showDropdown && searchResults.length"
                class="absolute z-50 w-full bg-white shadow-lg rounded-md mt-1 border border-gray-200 max-h-96 overflow-auto"
              >
                <div
                  v-for="item in searchResults"
                  :key="item.id"
                  @click="goToArticulo(item.id)"
                  class="flex items-center justify-between p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100"
                >
                  <div>
                    <p class="text-sm font-medium text-gray-800">{{ item.nombre }}</p>
                    <p class="text-blue-600 font-bold text-sm">${{ parseFloat(item.precio).toFixed(2) }}</p>
                  </div>
                  <img
                    v-if="item.imagenes && item.imagenes.length"
                    :src="`/storage/${item.imagenes[0].link}`"
                    class="w-12 h-12 object-cover rounded ml-3"
                    alt="preview"
                  />
                </div>
              </div>
            </div>
          </div>


          <div class="ml-auto flex items-center">
            <!-- Following
            <div class="ml-4 flow-root lg:ml-6">
              <a href="#" class="group -m-2 flex items-center p-2">
                <svg class="size-6 shrink-0 text-gray-400 group-hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                </svg>
              </a>
            </div> -->

            <!-- Likes -->
            <div class="ml-4 flow-root lg:ml-6 cursor-pointer">
              <a @click="redirectToUserInfo('favorites')" class="cursor:pointer group -m-2 flex items-center p-2">
                <svg class="size-6 shrink-0 text-gray-400 group-hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
              </a>
            </div>

            <!-- Perfil info -->
            <div class="ml-4 flow-root lg:ml-6">
              <div class="relative">
                <img
                  alt="tania andrew"
                  src="https://images.unsplash.com/photo-1633332755192-727a05c4013d?ixlib=rb-1.2.1&amp;ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&amp;auto=format&amp;fit=crop&amp;w=1480&amp;q=80"
                  class="relative inline-block h-9 w-9 cursor-pointer rounded-full object-cover object-center"
                  data-popover-target="profile-menu"
                  @click="toggleMenu"
                />
                <ul
                  id="profile-menu"
                  role="menu"
                  data-popover="profile-menu"
                  data-popover-placement="bottom"
                  class="absolute z-10 hidden min-w-[180px] flex-col gap-2 overflow-auto rounded-md border border-gray-200 bg-white p-3 font-sans text-sm font-normal text-gray-700 shadow-lg transition duration-300 ease-in-out transform right-0"
                >
                  <button
                    tabIndex="-1"
                    role="menuitem"
                    class="text-gray-600 hover:text-gray-900 flex w-full cursor-pointer select-none items-center gap-2 rounded-md px-3 pt-[9px] pb-2 text-start leading-tight outline-none transition-all hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900"
                    @click="closeProfileMenu(); redirectToUserInfo('info')"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="2"
                      stroke="currentColor"
                      aria-hidden="true"
                      class="h-4 w-4"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"
                      ></path>
                    </svg>
                    <p class="block font-sans text-sm font-normal leading-normal text-inherit antialiased">
                      My Profile
                    </p>
                  </button>
                  <button
                    tabIndex="-1"
                    role="menuitem"
                    class="text-gray-600 hover:text-gray-900 flex w-full cursor-pointer select-none items-center gap-2 rounded-md px-3 pt-[9px] pb-2 text-start leading-tight outline-none transition-all hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900"
                    @click="closeProfileMenu(); redirectToUserInfo('articles')"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="2"
                      stroke="currentColor"
                      aria-hidden="true"
                      class="h-4 w-4"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122"
                      ></path>
                    </svg>
                    <p class="block font-sans text-sm font-normal leading-normal text-inherit antialiased">
                      Mis articulos
                    </p>
                  </button>

                  <button
                      tabIndex="-1"
                      role="menuitem"
                      class="text-gray-600 hover:text-gray-900 flex w-full cursor-pointer select-none items-center gap-2 rounded-md px-3 pt-[9px] pb-2 text-start leading-tight outline-none transition-all hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900"
                      @click="router.visit(route('articulos.create'))"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        aria-hidden="true"
                        class="h-4 w-4"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122"
                        ></path>
                      </svg>
                      <p class="block font-sans text-sm font-normal leading-normal text-inherit antialiased">
                        Crear Articulo
                      </p>
                    </button>

                  <!-- Other menu items here -->
                  <hr class="my-2 border-blue-gray-50" tabindex="-1" role="menuitem" />
                  <button
                    tabIndex="-1"
                    role="menuitem"
                    class="text-gray-600 hover:text-gray-900 flex w-full cursor-pointer select-none items-center gap-2 rounded-md px-3 pt-[9px] pb-2 text-start leading-tight outline-none transition-all hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900"
                    @click="$inertia.post(route('logout')); closeProfileMenu()"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="2"
                      stroke="currentColor"
                      aria-hidden="true"
                      class="h-4 w-4"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5.636 5.636a9 9 0 1012.728 0M12 3v9"
                      ></path>
                    </svg>
                    <p class="block font-sans text-sm font-normal leading-normal text-inherit antialiased">
                      Sign Out
                    </p>
                  </button>
                </ul>
              </div>
            </div>

          </div>
        </div>
      </div>
    </nav>
  </header>
</template>
