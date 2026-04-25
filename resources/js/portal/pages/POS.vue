<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-6">Point of Sale</h1>

        <div class="grid grid-cols-12 gap-6">
            <!-- Product Grid -->
            <div class="col-span-8">
                <div class="bg-white rounded-lg shadow p-4 mb-4">
                    <input
                        type="text"
                        placeholder="Search products..."
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                    />
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div v-for="i in 6" :key="i" class="bg-white p-4 rounded-lg shadow hover:shadow-md cursor-pointer border border-transparent hover:border-teal-500">
                        <div class="h-32 bg-gray-100 rounded mb-2 flex items-center justify-center text-gray-400">
                            <Package :size="48" />
                        </div>
                        <h3 class="font-semibold">Mock Product {{ i }}</h3>
                        <p class="text-teal-600 font-bold">$ {{ (i * 10.5).toFixed(2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Cart / Checkout -->
            <div class="col-span-4">
                <div class="bg-white rounded-lg shadow flex flex-col h-[calc(100vh-200px)]">
                    <div class="p-4 border-b font-bold text-lg">Current Order</div>

                    <div class="flex-grow p-4 overflow-y-auto">
                        <div class="text-gray-500 text-center mt-10" v-if="cart.length === 0">
                            Cart is empty
                        </div>
                        <!-- Cart items would go here -->
                    </div>

                    <div class="p-4 border-t bg-gray-50">
                        <div class="flex justify-between mb-2">
                            <span>Subtotal</span>
                            <span>$ 0.00</span>
                        </div>
                        <div class="flex justify-between mb-4 font-bold text-xl">
                            <span>Total</span>
                            <span>$ 0.00</span>
                        </div>
                        <button class="w-full bg-teal-600 text-white py-3 rounded-lg font-bold hover:bg-teal-700 transition">
                            Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Package } from 'lucide-vue-next';

const cart = ref([]);
</script>
