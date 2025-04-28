<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
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
        <Head title="Olvidaste tu contraseña" />

        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">¿Olvidaste tu contraseña?</h2>
        <p class="text-center text-sm text-gray-600 mb-6">
          Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
        </p>

        <!-- Mensaje de estado -->
        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <!-- Formulario -->
        <form @submit.prevent="submit" class="space-y-6">
          <div class="space-y-2">
            <label for="email" class="text-sm font-semibold text-gray-700">Correo Electrónico</label>
            <Input
              id="email"
              type="email"
              v-model="form.email"
              required
              autofocus
              placeholder="email@example.com"
              autocomplete="off"
              class="p-3 rounded-lg border border-gray-300 focus:ring-indigo-400 focus:border-indigo-500 w-full text-gray-800"
            />
            <InputError :message="form.errors.email" />
          </div>

          <div>
            <Button
              type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg transition duration-300"
              :disabled="form.processing"
            >
              <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
              Enviar enlace de restablecimiento
            </Button>
          </div>
        </form>

        <!-- Link de regresar -->
        <div class="text-center text-sm text-gray-600 mt-6">
          <span>¿Recordaste tu contraseña?</span>
          <TextLink :href="route('login')" class="text-indigo-600 hover:underline font-semibold">
            Inicia sesión
          </TextLink>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>
