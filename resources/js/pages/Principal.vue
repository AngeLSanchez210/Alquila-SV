<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import axios from 'axios';

interface Product {
    id: number;
    name: string;
    description: string;
    price: string;
    image: string;
    category_id: number;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Principal', href: '/principal' },
];



const products = ref<Product[]>([]);
const categories = ref<{ id: number, name: string }[]>([]);

const searchQuery = ref('');
const selectedCategory = ref<number | null>(null);
const filteredProducts = ref<Product[]>([]);

onMounted(async () => {
    const productsResponse = await axios.get('/products');
    products.value = productsResponse.data;

    const categoriesResponse = await axios.get('/categories');
    categories.value = categoriesResponse.data;

    filteredProducts.value = products.value;
});

const filterByCategory = () => {
    if (selectedCategory.value) {
        filteredProducts.value = products.value.filter(product => product.category_id === selectedCategory.value);
    } else {
        filteredProducts.value = products.value;
    }
};

const searchProducts = () => {
    filteredProducts.value = products.value.filter(product =>
        product.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
};

const viewProductDetails = (id: number) => {
    window.location.href = `/product/${id}`;
};

const navigateToAddArticles = () => {
    window.location.href = '/addArticles';  // Cambia la ruta si es necesario
};
</script>

<template>
    <Head title="Principal" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">
                <!-- Barra de búsqueda -->
                <div class="search-bar mb-4">
                    <input 
                        v-model="searchQuery" 
                        type="text" 
                        placeholder="Buscar productos..." 
                        @input="searchProducts" 
                        class="p-2 border rounded w-full"
                    />
                </div>
                

                <!-- Filtros -->
                <div class="filters mb-4">
                    <select 
                        v-model="selectedCategory" 
                        @change="filterByCategory" 
                        class="p-2 border rounded"
                    >
                        <option value="">Todas las categorías</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>

                <!-- Listado de productos -->
                <div class="product-list grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div 
                        v-for="product in filteredProducts" 
                        :key="product.id" 
                        class="product-item p-4 border rounded-lg shadow-md"
                    >
                        <img :src="product.image" alt="Product Image" class="w-full h-48 object-cover rounded-md mb-4"/>
                        <h3 class="text-lg font-semibold">{{ product.name }}</h3>
                        <p class="text-sm text-gray-600">{{ product.description }}</p>
                        <p class="text-lg font-semibold mt-2">{{ product.price }}</p>
                        <button 
                            @click="viewProductDetails(product.id)" 
                            class="mt-4 bg-blue-500 text-white py-2 px-4 rounded-lg"
                        >
                            Ver detalles
                        </button>
                    </div>
                </div>

                
                <div class="mt-6">
                    <button 
                        @click="navigateToAddArticles" 
                        class="bg-green-600 text-white px-4 py-2 rounded"
                    >
                        Agregar Artículo
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.search-bar input {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 0.5rem;
    width: 100%;
}

.filters select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 0.5rem;
    width: 100%;
}

.product-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
}

.product-item {
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 0.5rem;
    background-color: #fff;
    text-align: center;
}

.product-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 0.5rem;
}

.product-item h3 {
    font-size: 1.25rem;
    font-weight: 600;
}

.product-item p {
    font-size: 1rem;
    color: #4a4a4a;
}

.product-item button {
    background-color: #007bff;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s;
}

.product-item button:hover {
    background-color: #0056b3;
}
</style>
