<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-6">Products</h1>

        <div class="bg-white rounded-lg shadow p-4 mb-4 flex justify-between items-center">
            <input
                type="text"
                placeholder="Search products..."
                class="w-1/3 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
            />
            <button class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">
                Add New Product
            </button>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="product in products" :key="product.id">
                        <td class="px-6 py-4 whitespace-nowrap">{{ product.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ product.sku }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ product.category }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${{ product.price.toFixed(2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ product.stock }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-teal-600 hover:text-teal-900 mr-3">Edit</a>
                            <a href="#" class="text-red-600 hover:text-red-900">Delete</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const products = ref([
    { id: 1, name: 'Product A', sku: 'PA001', category: 'Electronics', price: 29.99, stock: 150 },
    { id: 2, name: 'Product B', sku: 'PB002', category: 'Books', price: 12.50, stock: 300 },
    { id: 3, name: 'Product C', sku: 'PC003', category: 'Home Goods', price: 5.75, stock: 80 },
    { id: 4, name: 'Product D', sku: 'PD004', category: 'Electronics', price: 199.99, stock: 50 },
    { id: 5, name: 'Product E', sku: 'PE005', category: 'Books', price: 25.00, stock: 120 },
]);
</script>
