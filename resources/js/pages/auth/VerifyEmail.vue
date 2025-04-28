<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <Header />

    <!-- Contenido principal -->
    <main class="flex-1 flex flex-col items-center justify-center bg-white px-4 py-12">
      
      <!-- Logo empresa (opcional) -->
      <div class="mb-8">
        <img class="h-28 w-auto object-cover" src="/img/logofinal.png" alt="Alquila SV Logo">
      </div>

      <!-- Tarjeta -->
      <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl">
        <Head title="Verificación de correo electrónico" />

        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Verifica tu correo electrónico</h2>
        <p class="text-center text-sm text-gray-600 mb-6">
          Por favor verifica tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar.
        </p>

        <!-- Mensaje de estado -->
        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-600">
            Se ha enviado un nuevo enlace de verificación al correo electrónico que proporcionaste durante el registro.
        </div>

        <!-- Formulario -->
        <form @submit.prevent="submit" class="space-y-6 text-center">
          <Button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg transition duration-300"
          >
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
            Reenviar correo de verificación
          </Button>

          <TextLink
            :href="route('logout')"
            method="post"
            as="button"
            class="text-indigo-600 hover:underline font-semibold mt-4 block"
          >
            Cerrar sesión
          </TextLink>
        </form>
      </div>
    </main>

    <Footer />
  </div>
</template>
