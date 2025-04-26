<template>
  <div class="min-h-screen flex items-center justify-center px-4 text-gray-700">
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
        <button @click="seleccionarMetodo('paypal')" class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-3 rounded-xl transition">
          Pagar con PayPal
        </button>

        <button @click="seleccionarMetodo('tarjeta')" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition">
          Pagar con Tarjeta
        </button>

        <div v-if="metodoPago === 'paypal'" class="mt-4">
          <input
            v-model="correoPaypal"
            type="email"
            placeholder="Correo de PayPal"
            class="w-full px-4 py-2 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
          />
          <button
            @click="pagarConPaypal"
            class="mt-3 w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 rounded-xl transition"
          >
            Confirmar pago PayPal
          </button>
        </div>

        <div v-if="metodoPago === 'tarjeta'" class="mt-4 space-y-4">
          <input
            v-model="tarjetaDatos.titular"
            type="text"
            placeholder="Nombre del titular"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400"
          />
          <input
            v-model="tarjetaDatos.numero"
            type="text"
            placeholder="Número de tarjeta"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400"
          />
          <div class="flex gap-4">
            <input
              v-model="tarjetaDatos.expiracion"
              type="text"
              placeholder="MM/AA"
              class="w-1/2 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400"
            />
            <input
              v-model="tarjetaDatos.cvv"
              type="text"
              placeholder="CVV"
              class="w-1/2 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400"
            />
          </div>
          <button
            @click="pagarConTarjeta"
            class="w-full bg-indigo-700 hover:bg-indigo-800 text-white font-semibold py-2 rounded-xl transition"
          >
            Confirmar pago Tarjeta
          </button>
        </div>
      </div>

      <p class="text-xs text-gray-500 text-center mt-4">
        Al continuar, aceptas nuestros <a href="#" class="underline">términos y condiciones</a>.
      </p>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { ref, onMounted } from 'vue';
import Swal from 'sweetalert2';

const metodoPago = ref('');
const correoPaypal = ref('');
const tarjetaDatos = ref({
  titular: '',
  numero: '',
  expiracion: '',
  cvv: '',
});
const planId = ref(1);

onMounted(() => {
  const params = new URLSearchParams(window.location.search);
  const plan_id = params.get('plan_id');
  if (plan_id) {
    planId.value = parseInt(plan_id, 10);
  }
});

const seleccionarMetodo = (metodo) => {
  metodoPago.value = metodo;
};

const generarTransaccionId = () => {
  return Math.random().toString(36).substr(2, 10); // Genera una cadena aleatoria de 10 caracteres
};

const validarCorreo = (correo) => {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(correo);
};

const pagarConPaypal = async () => {
  if (!correoPaypal.value) {
    Swal.fire('Error', 'Por favor ingresa tu correo de PayPal.', 'error');
    return;
  }
  if (!validarCorreo(correoPaypal.value)) {
    Swal.fire('Error', 'Por favor ingresa un correo válido.', 'error');
    return;
  }
  try {
    await axios.post('/pagos', {
      metodo: 'paypal',
      plan_id: planId.value,
      transaccion_id: generarTransaccionId(),
    });
    Swal.fire('Pago Exitoso', 'Tu pago se ha procesado correctamente.', 'success');
  } catch (error) {
    if (error.response?.status === 400) {
      Swal.fire('Error', error.response.data.error, 'error'); // Mostrar mensaje del backend
    } else {
      Swal.fire('Error', 'Error al procesar el pago.', 'error');
    }
  }
};

const pagarConTarjeta = async () => {
  const { titular, numero, expiracion, cvv } = tarjetaDatos.value;
  if (!titular || !numero || !expiracion || !cvv) {
    Swal.fire('Error', 'Por favor completa todos los campos de la tarjeta.', 'error');
    return;
  }
  try {
    await axios.post('/pagos', {
      metodo: 'tarjeta',
      plan_id: planId.value,
      transaccion_id: generarTransaccionId(),
    });
    Swal.fire('Pago Exitoso', 'Tu pago se ha procesado correctamente.', 'success');
  } catch (error) {
    if (error.response?.status === 400) {
      Swal.fire('Error', error.response.data.error, 'error'); // Mostrar mensaje del backend
    } else {
      Swal.fire('Error', 'Error al procesar el pago.', 'error');
    }
  }
};
</script>
