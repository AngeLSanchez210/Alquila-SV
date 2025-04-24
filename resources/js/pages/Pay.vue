<template>
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6 space-y-6">
      <h2 class="text-2xl font-bold text-center">Confirmar Suscripción</h2>
      <p class="text-center text-gray-600">
        Estás a punto de adquirir el plan <span class="font-semibold text-indigo-600">Premium Mensual</span>.
      </p>

      <div class="bg-indigo-50 p-4 rounded-xl flex items-center justify-between">
        <div>
          <p class="text-sm text-gray-500">Costo</p>
          <p class="text-lg font-semibold">$9.99 / mes</p>
        </div>
        <span class="text-green-600 text-sm font-medium bg-green-100 px-3 py-1 rounded-full">Sin contratos</span>
      </div>

      <div class="space-y-4">
        <button @click="showPaypalEmail" class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-3 rounded-xl transition">
          Pagar con PayPal
        </button>

        <div v-if="paypalVisible" class="mt-4">
          <input type="email" placeholder="Correo de PayPal" class="w-full px-4 py-2 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400" />
          <button class="mt-3 w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 rounded-xl transition">Confirmar pago</button>
        </div>

        <div class="flex items-center justify-center text-sm text-gray-500 gap-2">
          <hr class="w-1/4 border-gray-300" /> o <hr class="w-1/4 border-gray-300" />
        </div>

        <form class="space-y-4">
          <input type="text" placeholder="Nombre del titular" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400" />
          <input type="text" placeholder="Número de tarjeta" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400" />
          <div class="flex gap-4">
            <input type="text" placeholder="MM/AA" class="w-1/2 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400" />
            <input type="text" placeholder="CVV" class="w-1/2 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400" />
          </div>
          <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition">
            Pagar con Tarjeta
          </button>
        </form>
      </div>

      <p class="text-xs text-gray-500 text-center">
        Al continuar, aceptas nuestros <a href="#" class="underline">términos y condiciones</a>.
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const paypalVisible = ref(false);

const showPaypalEmail = () => {
  paypalVisible.value = true;
};
</script>
