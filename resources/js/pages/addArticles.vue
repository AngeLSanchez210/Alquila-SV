<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const form = ref({
    nombre: '',
    descripcion: '',
    precio: '',
    estado: 'disponible',
    imagen: null as File | null,
});


const onFileChange = (event: Event) => {
    const fileInput = event.target as HTMLInputElement;
    if (fileInput.files && fileInput.files.length > 0) {
        form.value.imagen = fileInput.files[0];
    }
};


const submit = () => {
    const formData = new FormData();
    formData.append('nombre', form.value.nombre);
    formData.append('descripcion', form.value.descripcion);
    formData.append('precio', form.value.precio);
    formData.append('estado', form.value.estado);

    
    if (form.value.imagen instanceof File) {
        formData.append('imagen', form.value.imagen);
    }

   
    router.post('/articulos', formData);
};
</script>

<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto bg-white p-6 shadow-md rounded-lg">
            <h2 class="text-xl font-bold mb-4">Agregar Artículo</h2>
            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-gray-700">Nombre</label>
                    <input v-model="form.nombre" type="text" class="w-full border p-2 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Descripción</label>
                    <textarea v-model="form.descripcion" class="w-full border p-2 rounded"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Precio</label>
                    <input v-model="form.precio" type="number" step="0.01" class="w-full border p-2 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Estado</label>
                    <select v-model="form.estado" class="w-full border p-2 rounded">
                        <option value="disponible">Disponible</option>
                        <option value="alquilado">Alquilado</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Imagen</label>
                    <input type="file" @change="onFileChange" class="w-full border p-2 rounded">
                </div>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                    Guardar Artículo
                </button>
            </form>
        </div>
    </AppLayout>
</template>
