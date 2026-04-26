<template>
    <div class="h-screen flex flex-col bg-slate-100 overflow-hidden">
        <!-- Main POS Grid -->
        <div class="flex-1 flex overflow-hidden">
            <!-- Left: Products Selection (70%) -->
            <div class="w-2/3 flex flex-col border-r border-slate-200">
                <!-- Search & Filter Bar -->
                <div class="bg-white p-6 border-b border-slate-200 flex items-center gap-4">
                    <div class="relative flex-1">
                        <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5" />
                        <input v-model="searchQuery" type="text" placeholder="Scan barcode or type name..." class="w-full pl-12 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-500 shadow-inner" />
                    </div>
                    <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
                        <button @click="selectedCategory = null" :class="!selectedCategory ? 'bg-indigo-600 text-white' : 'bg-white text-slate-500 border border-slate-200'" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase transition-all whitespace-nowrap">All Items</button>
                        <button v-for="cat in categories" :key="cat.id" @click="selectedCategory = cat.id" :class="selectedCategory === cat.id ? 'bg-indigo-600 text-white' : 'bg-white text-slate-500 border border-slate-200'" class="px-6 py-2.5 rounded-xl text-xs font-black uppercase transition-all whitespace-nowrap">{{ cat.name }}</button>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="flex-1 overflow-y-auto p-6 grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 custom-scrollbar">
                    <div v-for="prod in filteredProducts" :key="prod.id" @click="addToCart(prod)" class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm hover:border-indigo-500 hover:shadow-xl hover:shadow-indigo-100 transition-all cursor-pointer group flex flex-col relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <PlusCircle class="w-6 h-6 text-indigo-600" />
                        </div>
                        <div class="h-24 bg-slate-50 rounded-xl mb-3 flex items-center justify-center text-slate-300 group-hover:bg-indigo-50 group-hover:text-indigo-300 transition-colors">
                            <Package :size="40" />
                        </div>
                        <h3 class="font-black text-slate-800 text-xs uppercase tracking-tighter line-clamp-2 h-8">{{ prod.name }}</h3>
                        <div class="mt-auto pt-3 flex items-center justify-between border-t border-slate-50">
                            <span class="text-xs font-black text-indigo-600">${{ parseFloat(prod.price).toFixed(2) }}</span>
                            <span :class="prod.stock_quantity <= 5 ? 'text-rose-500' : 'text-slate-400'" class="text-[9px] font-black uppercase">{{ prod.stock_quantity }} Left</span>
                        </div>
                    </div>

                    <div v-if="filteredProducts.length === 0" class="col-span-full flex flex-col items-center justify-center py-20 opacity-30">
                        <PackageSearch :size="64" />
                        <p class="font-black uppercase text-xs mt-4 tracking-widest">No products found</p>
                    </div>
                </div>
            </div>

            <!-- Right: Cart & Checkout (30%) -->
            <div class="w-1/3 flex flex-col bg-white shadow-2xl z-20">
                <!-- Customer Selection -->
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Active Customer</label>
                    <div class="relative">
                        <User class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4" />
                        <select v-model="selectedCustomer" class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-xs font-black text-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none appearance-none">
                            <option :value="null">Walk-in Customer</option>
                            <option v-for="cust in customers" :key="cust.id" :value="cust.id">{{ cust.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
                    <div v-for="item in cart" :key="item.id" class="flex items-center gap-4 bg-slate-50 p-3 rounded-2xl border border-slate-100 group">
                        <div class="flex-1">
                            <p class="text-[10px] font-black text-slate-800 uppercase tracking-tighter">{{ item.name }}</p>
                            <p class="text-[10px] font-black text-indigo-500">${{ item.price.toFixed(2) }}</p>
                        </div>
                        <div class="flex items-center bg-white rounded-lg border border-slate-200 overflow-hidden">
                            <button @click="updateQty(item, -1)" class="px-2 py-1 hover:bg-slate-100 text-slate-400 font-bold">-</button>
                            <span class="px-3 text-[10px] font-black text-slate-700 border-x border-slate-100 min-w-[30px] text-center">{{ item.quantity }}</span>
                            <button @click="updateQty(item, 1)" class="px-2 py-1 hover:bg-slate-100 text-slate-400 font-bold">+</button>
                        </div>
                        <button @click="removeFromCart(item.id)" class="p-1.5 text-slate-300 hover:text-rose-500 transition-colors">
                            <Trash2 class="w-4 h-4" />
                        </button>
                    </div>

                    <div v-if="cart.length === 0" class="h-full flex flex-col items-center justify-center text-slate-300 opacity-50">
                        <ShoppingCart :size="48" class="mb-4" />
                        <p class="text-[10px] font-black uppercase tracking-widest">Cart is Empty</p>
                    </div>
                </div>

                <!-- Checkout Summary -->
                <div class="p-6 border-t border-slate-100 bg-slate-50">
                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between text-xs font-bold text-slate-500">
                            <span>Subtotal</span>
                            <span>${{ subtotal.toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between text-xs font-bold text-slate-500">
                            <span>Tax (0%)</span>
                            <span>$0.00</span>
                        </div>
                        <div class="flex justify-between text-lg font-black text-slate-800 pt-2 border-t border-slate-200">
                            <span>Total</span>
                            <span>${{ subtotal.toFixed(2) }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <button v-for="m in ['cash', 'card', 'mobile']" :key="m" @click="paymentMethod = m" :class="paymentMethod === m ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white text-slate-400 border border-slate-200'" class="py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">{{ m }}</button>
                    </div>

                    <button @click="checkout" :disabled="cart.length === 0 || loading" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3 hover:bg-black transition-all shadow-xl disabled:opacity-50 active:scale-95">
                        <span v-if="!loading">Process Transaction</span>
                        <span v-else class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <ArrowRight v-if="!loading" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <Transition name="fade">
            <div v-if="showSuccess" class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
                <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl">
                    <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <Check :size="48" stroke-width="3" />
                    </div>
                    <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight mb-2">Success!</h2>
                    <p class="text-sm font-medium text-slate-500 mb-8">Transaction completed and stock updated successfully.</p>
                    <button @click="showSuccess = false" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-xs uppercase">Continue Selling</button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { api } from "../../plugins/axios";
import { Search, Package, PackageSearch, User, ShoppingCart, Trash2, ArrowRight, PlusCircle, Check } from 'lucide-vue-next';

// --- STATE ---
const products = ref([]);
const categories = ref([]);
const customers = ref([]);
const cart = ref([]);
const searchQuery = ref('');
const selectedCategory = ref(null);
const selectedCustomer = ref(null);
const paymentMethod = ref('cash');
const loading = ref(false);
const showSuccess = ref(false);

// --- COMPUTED ---
const filteredProducts = computed(() => {
    let list = products.value;
    if (selectedCategory.value) list = list.filter(p => p.category_id === selectedCategory.value);
    const q = searchQuery.value.toLowerCase();
    if (q) list = list.filter(p => p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q));
    return list;
});

const subtotal = computed(() => cart.value.reduce((sum, item) => sum + (item.price * item.quantity), 0));

// --- METHODS ---
const loadData = async () => {
    const [p, cat, cust] = await Promise.all([
        api.get('portal/products'),
        api.get('portal/categories'),
        api.get('portal/customers')
    ]);
    products.value = p.data;
    categories.value = cat.data;
    customers.value = cust.data;
};

const addToCart = (product) => {
    const existing = cart.value.find(i => i.id === product.id);
    if (existing) {
        if (existing.quantity < product.stock_quantity) existing.quantity++;
    } else {
        if (product.stock_quantity > 0) {
            cart.value.push({
                id: product.id,
                name: product.name,
                price: parseFloat(product.price),
                quantity: 1
            });
        }
    }
};

const updateQty = (item, delta) => {
    const prod = products.value.find(p => p.id === item.id);
    const newQty = item.quantity + delta;
    if (newQty > 0 && newQty <= prod.stock_quantity) {
        item.quantity = newQty;
    }
};

const removeFromCart = (id) => {
    cart.value = cart.value.filter(i => i.id !== id);
};

const checkout = async () => {
    loading.value = true;
    try {
        await api.post('portal/sales', {
            customer_id: selectedCustomer.value,
            subtotal: subtotal.value,
            tax_amount: 0,
            discount_amount: 0,
            total_amount: subtotal.value,
            payment_method: paymentMethod.value,
            items: cart.value.map(i => ({
                product_id: i.id,
                quantity: i.quantity,
                unit_price: i.price
            }))
        });

        cart.value = [];
        selectedCustomer.value = null;
        showSuccess.value = true;
        await loadData(); // Reload to update stock numbers
    } catch (e) {
        alert('Transaction failed. Check stock levels.');
    } finally {
        loading.value = false;
    }
};

onMounted(loadData);
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
