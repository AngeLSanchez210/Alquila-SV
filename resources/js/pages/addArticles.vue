<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import Swal from 'sweetalert2';
import axios from 'axios';

// Definir las propiedades esperadas
interface Categoria {
    id: number;
    nombre: string;
}



// Definir props con valor predeterminado para evitar el error de prop requerido
const props = defineProps<{
    categorias?: Categoria[];
}>();

// Usar las categorías del prop si están disponibles, de lo contrario usar un array vacío
const categorias = ref(props.categorias || []);

console.log('Categorías recibidas:', props.categorias);


const form = ref({
    nombre: '',
    descripcion: '',
    precio: '',
    estado: 'disponible',
    idcategoria: '', // Campo para la categoría
    imagenes: [] as File[],
});


const onFileChange = (event: Event) => {
    const fileInput = event.target as HTMLInputElement;
    if (fileInput.files && fileInput.files.length > 0) {
        form.value.imagenes = Array.from(fileInput.files);
    }
};

const fileInputRef = ref<HTMLInputElement | null>(null); // referencia al input de archivos

const submit = () => {
  const formData = new FormData();
  formData.append('nombre', form.value.nombre);
  formData.append('descripcion', form.value.descripcion);
  formData.append('precio', form.value.precio);
  formData.append('estado', form.value.estado);
  formData.append('idcategoria', form.value.idcategoria);

  form.value.imagenes.forEach((imagen, index) => {
    formData.append(`imagenes[${index}]`, imagen);
  });

  axios.post('/articulos', formData)
  .then(() => {
    Swal.fire({
      title: '¡Artículo guardado!',
      text: '¿Deseas agregar otro artículo o volver al inicio?',
      icon: 'success',
      showCancelButton: true,
      confirmButtonText: 'Agregar otro',
      cancelButtonText: 'Volver al inicio',
      confirmButtonColor: '#22c55e',
      cancelButtonColor: '#3b82f6',
    }).then((result) => {
      if (result.isConfirmed) {
        // Resetear campos
        form.value.nombre = '';
        form.value.descripcion = '';
        form.value.precio = '';
        form.value.estado = 'disponible';
        form.value.idcategoria = '';
        form.value.imagenes = [];

        if (fileInputRef.value) {
          fileInputRef.value.value = '';
        }
      } else {
        router.visit('/');
      }
    });
  })
  .catch(() => {
    Swal.fire({
      title: 'Error',
      text: 'Hubo un problema al guardar el artículo.',
      icon: 'error',
      confirmButtonColor: '#ef4444'
    });
  });
};


</script>

<template>
    <!-- Agregar Header y Footer en la misma estructura -->
    <Header />
    
    <!-- Contenedor principal con fondo blanco -->
    <div class="min-h-screen bg-white flex flex-col items-center">
        <!-- Formulario con fondo oscuro y texto claro -->
        <div class="max-w-4xl mx-auto bg-gray-900 text-white p-6 shadow-lg rounded-lg mt-10">
            <h2 class="text-2xl font-bold mb-6 text-gray-200 text-center">Agregar Artículo</h2>
            <form @submit.prevent="submit" class="grid grid-cols-2 gap-6">
                <!-- Nombre del artículo -->
                <div class="mb-4 flex flex-col">
                    <label class="block text-gray-200">Nombre</label>
                    <input v-model="form.nombre" type="text" class="w-full border p-3 rounded-lg bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <!-- Descripción del artículo -->
                <div class="mb-4 flex flex-col">
                    <label class="block text-gray-200">Descripción</label>
                    <textarea v-model="form.descripcion" class="w-full border p-3 rounded-lg bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                <!-- Precio del artículo -->
                <div class="mb-4 flex flex-col">
                    <label class="block text-gray-200">Precio</label>
                    <input v-model="form.precio" type="number" step="0.01" class="w-full border p-3 rounded-lg bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <!-- Estado del artículo -->
                <div class="mb-4 flex flex-col">
                    <label class="block text-gray-200">Estado</label>
                    <select v-model="form.estado" class="w-full border p-3 rounded-lg bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="disponible">Disponible</option>
                        <option value="alquilado">Alquilado</option>
                    </select>
                </div>
                <!-- Selección de categoría -->
                <div class="mb-4 flex flex-col">
                    <label class="block text-gray-200">Categoría</label>
                    <select v-model="form.idcategoria" class="w-full border p-3 rounded-lg bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Seleccione una categoría</option>
                        <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
                            {{ categoria.nombre }}
                        </option>
                    </select>
                </div>
                <!-- Selección de imágenes -->
                <div class="mb-4 flex flex-col">
                    <label class="block text-gray-200">Imágenes</label>
                    <input type="file" @change="onFileChange" class="w-full border p-3 rounded-lg bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-green-500" multiple>
                </div>
                <!-- Botón de enviar -->
                <div class="col-span-2 flex justify-center">
                    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-300">
                        Guardar Artículo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <Footer />
</template>