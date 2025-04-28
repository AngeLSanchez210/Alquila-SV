<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

interface Props {
    token: string;
    email: string;
}

const props = defineProps<Props>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
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

      <!-- Tarjeta de reset -->
      <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl">
        <Head title="Restablecer contraseña" />

        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Restablece tu contraseña</h2>
        <p class="text-center text-sm text-gray-600 mb-6">
          Ingresa tu nueva contraseña a continuación.
        </p>

        <form @submit.prevent="submit" class="space-y-6">
          <div class="space-y-2">
            <label for="email" class="text-sm font-semibold text-gray-700">Correo Electrónico</label>
            <Input
              id="email"
              type="email"
              v-model="form.email"
              readonly
              autocomplete="email"
              class="p-3 rounded-lg border border-gray-300 focus:ring-indigo-400 focus:border-indigo-500 w-full text-gray-800 bg-gray-100"
            />
            <InputError :message="form.errors.email" />
          </div>

          <div class="space-y-2">
            <label for="password" class="text-sm font-semibold text-gray-700">Nueva Contraseña</label>
            <Input
              id="password"
              type="password"
              v-model="form.password"
              required
              autocomplete="new-password"
              autofocus
              placeholder="Nueva contraseña"
              class="p-3 rounded-lg border border-gray-300 focus:ring-indigo-400 focus:border-indigo-500 w-full text-gray-800"
            />
            <InputError :message="form.errors.password" />
          </div>

          <div class="space-y-2">
            <label for="password_confirmation" class="text-sm font-semibold text-gray-700">Confirmar Contraseña</label>
            <Input
              id="password_confirmation"
              type="password"
              v-model="form.password_confirmation"
              required
              autocomplete="new-password"
              placeholder="Confirmar contraseña"
              class="p-3 rounded-lg border border-gray-300 focus:ring-indigo-400 focus:border-indigo-500 w-full text-gray-800"
            />
            <InputError :message="form.errors.password_confirmation" />
          </div>

          <Button
            type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg transition duration-300"
            :disabled="form.processing"
          >
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
            Restablecer Contraseña
          </Button>
        </form>
      </div>
    </main>

    <Footer />
  </div>
</template>
