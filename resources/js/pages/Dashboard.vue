<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

const adminName = ref(''); 
onMounted(async () => {
  
  adminName.value = 'Administrador'; 
});
</script>

<template>
  <Head title="Bienvenido" />

  <AppLayout>
    <div class="p-8 space-y-6">
    
      <div class="flex justify-center items-center">
        <h1 class="text-3xl font-semibold text-white">
          ¡Bienvenido, {{ adminName }}!
        </h1>
      </div>

 
      <div class="flex justify-center items-center mt-6">
        <img
          src="/ruta/a/tu/imagen.jpg" 
          alt="Imagen de bienvenida"
          class="rounded-lg shadow-lg"
          width="300"  
        />
      </div>
    </div>
  </AppLayout>
</template>
