<script setup lang="ts">
// Importamos cosas que vamos a usar
import { ref } from 'vue'; 
import { router } from '@inertiajs/vue3'; 
import Header from '@/components/Header.vue'; 
import Footer from '@/components/Footer.vue'; 
import Swal from 'sweetalert2'; 
import axios from 'axios'; 

// Esta es la forma que tienen las categorías
interface Categoria {
    id: number;
    nombre: string;
}

// Recibimos las categorías como prop
const props = defineProps<{ categorias?: Categoria[] }>();
const categorias = ref(props.categorias || []); // si no vienen, usamos un array vacío

// Aquí guardamos todos los datos del formulario
const form = ref({
    nombre: '', 
    descripcion: '', 
    precio: '', 
    estado: 'disponible', 
    idcategoria: '', 
    imagenes: [] as File[], // imágenes que se van a subir
    tipo_precio: 'por día', // por día o por mes
});

// Esto es para poder limpiar el input de archivos después de guardar
const fileInputRef = ref<HTMLInputElement | null>(null);

// funcion al seleccionar archivos
const onFileChange = (event: Event) => {
    const fileInput = event.target as HTMLInputElement;

    //si los hay
    if (fileInput.files && fileInput.files.length > 0) {
        const filesArray = Array.from(fileInput.files); // convertimos a array
        const validFiles = filesArray.filter(file =>
        //FORMATO DE IMÁGENES
            file.type === 'image/jpeg' ||  // JPG
            file.type === 'image/png' ||   // PNG
            file.type === 'image/jfif' ||  // JFIF
            file.type === 'image/webp'     // WEBP
        );

        // Si hay archivos inválidos, mostramos alerta
        if (validFiles.length !== filesArray.length) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo no permitido',
                text: 'Solo puedes subir imágenes JPG, PNG, JFIF o WEBP.',
            });
        }

        // Guardamos solo los archivos válidos en el formulario
        form.value.imagenes = validFiles;
    }
};


const submit = () => {
    const formData = new FormData(); // usamos FormData para mandar imágenes

    // Agregamos los campos normales
    formData.append('nombre', form.value.nombre);
    const descripcionCompleta = `${form.value.descripcion}\n\n**Precio ${form.value.tipo_precio.toUpperCase()}**`;
    formData.append('descripcion', descripcionCompleta);
    formData.append('precio', form.value.precio);
    formData.append('estado', form.value.estado);
    formData.append('idcategoria', form.value.idcategoria);

    // Agregamos cada imagen si son varias
    form.value.imagenes.forEach((imagen, index) => {
        formData.append(`imagenes[${index}]`, imagen);
    });

    // Enviamos el formulario al backend
    axios.post('/articulos', formData)
        .then(() => {
            // Si todo salió bien, mostramos una alerta con opciones
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
                    // Si elige agregar otro, limpiamos el formulario
                    form.value = {
                        nombre: '',
                        descripcion: '',
                        precio: '',
                        estado: 'disponible',
                        idcategoria: '',
                        imagenes: [],
                        tipo_precio: 'por día'
                    };
                    // También limpiamos el input de archivos
                    if (fileInputRef.value) fileInputRef.value.value = '';
                } else {
                    // Si no, lo mandamos al inicio
                    router.visit('/');
                }
            });
        })
        .catch((error) => {
            // Si hay error, lo manejamos según el tipo
            const status = error.response?.status;
            const errorMsg = error.response?.data?.error || 'Hubo un problema al guardar el artículo.';

            if (status === 403) {
                Swal.fire({
                    title: 'Límite alcanzado',
                    text: errorMsg,
                    icon: 'warning',
                    confirmButtonColor: '#f97316'
                });
            } else if (status === 401) {
                Swal.fire({
                    title: 'No autenticado',
                    text: 'Debes iniciar sesión para publicar artículos.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: errorMsg,
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            }
        });
};
</script>


<template>
  <Header />

  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-6">
    <div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl p-10">
      <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Agregar Artículo</h2>

      <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Nombre -->
        <div class="flex flex-col">
          <label class="text-sm font-medium text-gray-700 mb-1">Nombre</label>
          <input v-model="form.nombre" type="text" placeholder="Nombre del artículo" class="p-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-400 outline-none text-gray-800 bg-white">
        </div>

        <!-- Descripción -->
        <div class="flex flex-col">
          <label class="text-sm font-medium text-gray-700 mb-1">Descripción</label>
          <textarea v-model="form.descripcion" placeholder="Breve descripción" rows="2" class="p-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-400 outline-none text-gray-800 bg-white"></textarea>
        </div>

        <!-- Precio -->
        <div class="flex flex-col">
          <label class="text-sm font-medium text-gray-700 mb-1">Precio</label>
          <input v-model="form.precio" type="number" step="0.01" placeholder="Precio en dólares" class="p-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-400 outline-none text-gray-800 bg-white">
        </div>

        <!-- Tipo de precio -->
        <div class="flex flex-col">
          <label class="text-sm font-medium text-gray-700 mb-1">Tipo de precio</label>
          <select v-model="form.tipo_precio" class="p-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-400 outline-none text-gray-800 bg-white">
            <option value="por día">Por día</option>
            <option value="por mes">Por mes</option>
          </select>
        </div>

        <!-- Estado (bloqueado) -->
        <div class="flex flex-col">
          <label class="text-sm font-medium text-gray-700 mb-1">Estado</label>
          <input value="Disponible" disabled class="p-3 rounded-md border border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed">
        </div>

        <!-- Categoría -->
        <div class="flex flex-col">
          <label class="text-sm font-medium text-gray-700 mb-1">Categoría</label>
          <select v-model="form.idcategoria" class="p-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-400 outline-none text-gray-800 bg-white">
            <option value="">Seleccione una categoría</option>
            <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
              {{ categoria.nombre }}
            </option>
          </select>
        </div>

        <!-- Imágenes -->
        <div class="flex flex-col">
          <label class="text-sm font-medium text-gray-700 mb-1">Imágenes (solo JPG o PNG)</label>
          <input
            type="file"
            ref="fileInputRef"
            accept="image/jpeg, image/png, image/jfif, image/webp"
            @change="onFileChange"
            multiple
            class="..."
          >
        </div>

        <!-- Botón Guardar -->
        <div class="col-span-1 md:col-span-2 flex justify-center mt-4">
          <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-8 rounded-full transition-all duration-300 text-lg font-semibold shadow-md">
            Guardar Artículo
          </button>
        </div>
      </form>
    </div>
  </div>

  <Footer />
</template>
