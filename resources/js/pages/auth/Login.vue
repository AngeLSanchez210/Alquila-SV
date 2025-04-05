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
    <AuthBase title="Inicia Sesión" description="Accede a tu cuenta para continuar">
        <Head title="Iniciar Sesión" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6 p-8 bg-white shadow-xl rounded-xl">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email" class="text-gray-700 font-semibold">Correo Electrónico</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                        class="border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 rounded-lg"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password" class="text-gray-700 font-semibold">Contraseña</Label>
                        <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-indigo-600 hover:underline text-sm" :tabindex="5">
                            ¿Olvidaste tu contraseña?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="********"
                        class="border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 rounded-lg"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center gap-2">
                    <Checkbox id="remember" v-model:checked="form.remember" :tabindex="4" class="border-gray-300 focus:ring-indigo-500" />
                    <Label for="remember" class="text-gray-700">Recuérdame</Label>
                </div>

                <Button type="submit" class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-lg transition-all duration-200 flex items-center justify-center gap-2" :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Iniciar Sesión
                </Button>
            </div>

            <div class="text-center text-sm text-gray-600">
                ¿No tienes una cuenta?
                <TextLink :href="route('register')" class="text-indigo-600 hover:underline" :tabindex="5">Regístrate</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
