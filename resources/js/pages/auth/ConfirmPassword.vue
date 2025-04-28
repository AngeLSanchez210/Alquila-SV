<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();
        },
    });
};
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <Header />

    <!-- Contenido principal -->
    <main class="flex-1 flex flex-col items-center justify-center bg-white px-4 py-12">
      

      <!-- Tarjeta de confirmación -->
      <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl">
        <Head title="Confirmar contraseña" />

        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Confirma tu contraseña</h2>
        <p class="text-center text-sm text-gray-600 mb-6">
          Esta es un área segura de la aplicación. Por favor confirma tu contraseña antes de continuar.
        </p>

        <form @submit.prevent="submit" class="space-y-6">
          <div class="space-y-2">
            <label for="password" class="text-sm font-semibold text-gray-700">Contraseña</label>
            <Input
              id="password"
              type="password"
              v-model="form.password"
              required
              autofocus
              autocomplete="current-password"
              placeholder="********"
              class="p-3 rounded-lg border border-gray-300 focus:ring-indigo-400 focus:border-indigo-500 w-full text-gray-800"
            />
            <InputError :message="form.errors.password" />
          </div>

          <div>
            <Button
              type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg transition duration-300"
              :disabled="form.processing"
            >
              <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
              Confirmar Contraseña
            </Button>
          </div>
        </form>
      </div>
    </main>

    <Footer />
  </div>
</template>
