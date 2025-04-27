<script setup lang="ts">
import { ref } from 'vue';
import { router, useForm, Head } from '@inertiajs/vue3';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';
import Swal from 'sweetalert2';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    direccion: '',
    telefono: '',
    foto: null, // Foto nueva
});

const fileInputRef = ref<HTMLInputElement | null>(null);

const submit = () => {
    if (!form.name.trim() || !form.email.trim() || !form.direccion.trim() || !form.telefono.trim()) {
        Swal.fire({
            icon: 'error',
            title: 'Campos vacíos',
            text: 'Por favor completa todos los campos obligatorios.',
        });
        return;
    }

    if (form.password.length < 8) {
        Swal.fire({
            icon: 'error',
            title: 'Contraseña débil',
            text: 'La contraseña debe tener al menos 8 caracteres.',
        });
        return;
    }

    if (form.password !== form.password_confirmation) {
        Swal.fire({
            icon: 'error',
            title: 'Error de contraseña',
            text: 'Las contraseñas no coinciden.',
        });
        return;
    }

    const data = new FormData();
    data.append('name', form.name);
    data.append('email', form.email);
    data.append('password', form.password);
    data.append('password_confirmation', form.password_confirmation);
    data.append('direccion', form.direccion);
    data.append('telefono', form.telefono);
    if (form.foto) {
        data.append('foto', form.foto);
    }

    router.post(route('register'), data, {
        forceFormData: true,
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};


</script>

<template>
  <Header />

  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-6">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-8">
      <Head title="Registrarse" />
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Crear una Cuenta</h2>

      <form @submit.prevent="submit" class="flex flex-col gap-5">
        <div class="grid gap-4">
            <div class="flex justify-center mb-6">
                <img src="/img/logofinal.png" alt="Logo de la Empresa" class="h-20 w-auto">
            </div>
          <div class="grid gap-1">
            <Label for="name">Nombre</Label>
            <Input id="name" type="text" required autofocus autocomplete="name" v-model="form.name" placeholder="Nombre completo" />
            <InputError :message="form.errors.name" />
          </div>

          <div class="grid gap-1">
            <Label for="email">Correo</Label>
            <Input id="email" type="email" required autocomplete="email" v-model="form.email" placeholder="email@example.com" />
            <InputError :message="form.errors.email" />
          </div>

          <div class="grid gap-1">
            <Label for="direccion">Dirección</Label>
            <Input id="direccion" type="text" required v-model="form.direccion" placeholder="Dirección completa" />
            <InputError :message="form.errors.direccion" />
          </div>

          <div class="grid gap-1">
            <Label for="telefono">Teléfono</Label>
            <Input id="telefono" type="text" required v-model="form.telefono" placeholder="Número de teléfono" />
            <InputError :message="form.errors.telefono" />
          </div>

          <div class="grid gap-1">
            <Label for="password">Contraseña</Label>
            <Input id="password" type="password" required autocomplete="new-password" v-model="form.password" placeholder=" Contraseña Mínimo 8 caracteres" />
            <InputError :message="form.errors.password" />
          </div>

          <div class="grid gap-1">
            <Label for="password_confirmation">Confirmar Contraseña</Label>
            <Input id="password_confirmation" type="password" required autocomplete="new-password" v-model="form.password_confirmation" placeholder="Confirma tu contraseña" />
            <InputError :message="form.errors.password_confirmation" />
          </div>


          <Button type="submit" class="mt-2 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg transition-all duration-200" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
            Crear Cuenta
          </Button>
        </div>

        <div class="text-center text-sm text-gray-600 mt-4">
          ¿Ya tienes una cuenta?
          <TextLink :href="route('login')" class="text-indigo-600 hover:underline">Inicia sesión</TextLink>
        </div>
      </form>
    </div>
  </div>

  <Footer />
</template>
