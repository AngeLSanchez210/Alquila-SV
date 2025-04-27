<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';

defineProps<{ status?: string; canResetPassword: boolean }>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen flex flex-col">
      <Header />
  
      <!-- Contenido principal -->
      <main class="flex-1 flex flex-col items-center justify-center bg-white px-4 py-12">
        
        <!-- Logo empresa -->
        <div class="mb-8">
            <img class="h-28 w-auto object-cover" src="/img/logofinal.png" alt="Alquila SV Logo">
        </div>
  
        <!-- Formulario -->
        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl">
          <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Inicia Sesión</h2>
  
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Email -->
            <div class="space-y-2">
              <label for="email" class="text-sm font-semibold text-gray-700">Correo Electrónico</label>
              <Input
                id="email"
                type="email"
                required
                autofocus
                autocomplete="email"
                v-model="form.email"
                placeholder="email@example.com"
                class="p-3 rounded-lg border border-gray-300 focus:ring-indigo-400 focus:border-indigo-500 w-full text-gray-800"
              />
              <InputError :message="form.errors.email" />
            </div>
  
            <!-- Contraseña -->
            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <label for="password" class="text-sm font-semibold text-gray-700">Contraseña</label>
                <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-indigo-600 hover:underline text-sm">
                  ¿Olvidaste tu contraseña?
                </TextLink>
              </div>
              <Input
                id="password"
                type="password"
                required
                v-model="form.password"
                placeholder="********"
                autocomplete="current-password"
                class="p-3 rounded-lg border border-gray-300 focus:ring-indigo-400 focus:border-indigo-500 w-full text-gray-800"
              />
              <InputError :message="form.errors.password" />
            </div>
  
            <!-- Recuérdame -->
            <div class="flex items-center gap-2">
              <Checkbox id="remember" v-model:checked="form.remember" />
              <label for="remember" class="text-gray-700 text-sm">Recuérdame</label>
            </div>
  
            <!-- Botón de iniciar sesión -->
            <Button
              type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg transition duration-300"
              :disabled="form.processing"
            >
              <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
              Iniciar Sesión
            </Button>
          </form>
  
          <!-- Registrarse -->
          <p class="text-center text-sm text-gray-600 mt-6">
            ¿No tienes una cuenta?
            <TextLink :href="route('register')" class="text-indigo-600 hover:underline font-semibold">
              Regístrate
            </TextLink>
          </p>
        </div>
      </main>
  
      <Footer />
    </div>
  </template>
  