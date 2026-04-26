<template>
    <div class="h-screen flex flex-col bg-slate-50 overflow-hidden relative">
        <!-- Slide Panel for Add/Edit -->
        <Transition name="slide">
            <div v-if="panel.show" class="fixed inset-y-0 right-0 w-[450px] bg-white shadow-2xl z-[110] border-l border-slate-200 flex flex-col">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <div>
                        <h2 class="font-black text-slate-800 uppercase tracking-tight">
                            {{ panel.editing ? 'Edit Product' : 'New Product' }}
                        </h2>
                        <p class="text-xs font-bold text-indigo-600">Stock & Pricing</p>
                    </div>
                    <button @click="closePanel" class="p-2 hover:bg-slate-200 rounded-full transition-colors">
                        <X class="w-5 h-5 text-slate-400" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-5 custom-scrollbar">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Product Name</label>
                            <input v-model="form.name" type="text" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">SKU / Barcode</label>
                            <input v-model="form.sku" type="text" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Category</label>
                            <select v-model="form.category_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none appearance-none">
                                <option :value="null">Select Category</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Selling Price</label>
                            <input v-model="form.price" type="number" step="0.01" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Cost Price</label>
                            <input v-model="form.cost_price" type="number" step="0.01" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                        <div>
                            <label class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1 block">Current Stock</label>
                            <input v-model="form.stock_quantity" type="number" class="w-full px-4 py-2.5 bg-white border border-indigo-100 rounded-xl text-sm font-black text-indigo-700 focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1 block">Low Stock Alert</label>
                            <input v-model="form.alert_quantity" type="number" class="w-full px-4 py-2.5 bg-white border border-indigo-100 rounded-xl text-sm font-black text-indigo-700 focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Description</label>
                        <textarea v-model="form.description" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                    </div>

                    <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <input type="checkbox" v-model="form.is_active" id="prod_active" class="w-4 h-4 text-indigo-600 rounded">
                        <label for="prod_active" class="text-xs font-black text-slate-600 uppercase">Visible in POS</label>
                    </div>
                </div>

                <div class="p-6 bg-slate-50 border-t border-slate-100">
                    <button @click="saveProduct" :disabled="loading" class="w-full bg-indigo-600 text-white py-4 rounded-xl font-black text-xs uppercase flex items-center justify-center gap-2 hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 disabled:opacity-50">
                        <Save v-if="!loading" class="w-4 h-4" />
                        <span v-else class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        {{ panel.editing ? 'Update Product' : 'Create Product' }}
                    </button>
                </div>
            </div>
        </Transition>

        <!-- Notification Toast -->
        <Transition name="slide-fade">
            <div v-if="notification.show" class="fixed bottom-8 right-8 z-[130] flex items-center gap-3 px-6 py-3 bg-slate-900 text-white rounded-xl shadow-2xl border border-slate-700">
                <Check class="w-4 h-4 text-emerald-400" />
                <span class="text-sm font-bold">{{ notification.message }}</span>
            </div>
        </Transition>

        <!-- Header -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex flex-wrap items-center justify-between gap-4 shadow-sm z-10">
            <div class="flex items-center gap-4">
                <div class="p-2 bg-indigo-600 rounded-lg"><Package class="w-6 h-6 text-white" /></div>
                <div>
                    <h1 class="text-xl font-black text-slate-800 uppercase leading-none">Inventory</h1>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Product Catalog</span>
                    </div>
                </div>
                <div class="relative ml-4">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                    <input v-model="searchQuery" type="text" placeholder="Search by name or SKU..." class="pl-10 pr-4 py-2 w-72 bg-slate-100 border-none rounded-full text-sm focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button @click="openAddPanel" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white text-xs font-black rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">
                    <Plus class="w-4 h-4" /> ADD PRODUCT
                </button>
            </div>
        </header>

        <!-- Table Header -->
        <div class="px-8 py-3 bg-slate-800 text-white text-[10px] font-black uppercase tracking-widest grid grid-cols-12 gap-4 shadow-lg z-10">
            <div class="col-span-4">Product Info</div>
            <div class="col-span-2">Category</div>
            <div class="col-span-2 text-center">Price</div>
            <div class="col-span-2 text-center">Stock</div>
            <div class="col-span-2 text-right">Actions</div>
        </div>

        <!-- Table Content -->
        <div class="flex-1 overflow-y-auto px-8 py-4 space-y-2 custom-scrollbar">
            <div v-for="prod in filteredProducts" :key="prod.id" class="bg-white border border-slate-200 rounded-xl p-4 grid grid-cols-12 gap-4 items-center hover:border-indigo-300 transition-all group">
                <div class="col-span-4">
                    <p class="font-black text-slate-900 uppercase tracking-tighter">{{ prod.name }}</p>
                    <p class="text-[10px] font-black text-indigo-500">{{ prod.sku }}</p>
                </div>
                <div class="col-span-2">
                    <span class="text-[10px] font-black text-slate-500 uppercase">{{ prod.category?.name || 'Uncategorized' }}</span>
                </div>
                <div class="col-span-2 text-center">
                    <p class="font-black text-slate-800">${{ parseFloat(prod.price).toFixed(2) }}</p>
                </div>
                <div class="col-span-2 text-center">
                    <span :class="getStockClass(prod)" class="text-[10px] font-black px-2 py-1 rounded uppercase">
                        {{ prod.stock_quantity }} IN STOCK
                    </span>
                </div>
                <div class="col-span-2 flex justify-end gap-2">
                    <button @click="openEditPanel(prod)" class="p-2 hover:bg-indigo-50 text-slate-300 hover:text-indigo-600 rounded-lg transition-colors">
                        <Edit class="w-4 h-4" />
                    </button>
                    <button @click="deleteProduct(prod.id)" class="p-2 hover:bg-rose-50 text-slate-300 hover:text-rose-600 rounded-lg transition-colors">
                        <Trash2 class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <div v-if="filteredProducts.length === 0" class="flex flex-col items-center justify-center py-20 opacity-40">
                <Package :size="48" class="text-slate-300 mb-4" />
                <p class="font-black text-slate-400 uppercase text-xs tracking-widest">Empty Catalog</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';
import { api } from "../../../plugins/axios";
import { Plus, Search, Package, Edit, Trash2, X, Save, Check } from 'lucide-vue-next';

const products = ref([]);
const categories = ref([]);
const searchQuery = ref('');
const loading = ref(false);
const panel = reactive({ show: false, editing: false, currentId: null });
const notification = reactive({ show: false, message: '' });

const form = reactive({
    category_id: null,
    name: '',
    sku: '',
    description: '',
    price: 0,
    cost_price: 0,
    stock_quantity: 0,
    alert_quantity: 10,
    is_active: true
});

const loadData = async () => {
    try {
        const [prodRes, catRes] = await Promise.all([
            api.get('portal/products'),
            api.get('portal/categories')
        ]);
        products.value = prodRes.data;
        categories.value = catRes.data;
        console.log('Categories loaded:', categories.value);
    } catch (e) {
        console.error('Error loading data:', e);
    }
};

const getStockClass = (prod) => {
    if (prod.stock_quantity <= 0) return 'bg-rose-100 text-rose-700';
    if (prod.stock_quantity <= prod.alert_quantity) return 'bg-amber-100 text-amber-700';
    return 'bg-emerald-100 text-emerald-700';
};

const openAddPanel = () => {
    panel.editing = false;
    panel.currentId = null;
    Object.assign(form, {
        category_id: categories.value[0]?.id || null,
        name: '', sku: '', description: '', price: 0, cost_price: 0, stock_quantity: 0, alert_quantity: 10, is_active: true
    });
    panel.show = true;
};

const openEditPanel = (prod) => {
    panel.editing = true;
    panel.currentId = prod.id;
    Object.assign(form, { ...prod, is_active: !!prod.is_active });
    panel.show = true;
};

const closePanel = () => { panel.show = false; };

const saveProduct = async () => {
    loading.value = true;
    try {
        if (panel.editing) {
            await api.put(`portal/products/${panel.currentId}`, form);
            triggerNotification('Product Updated');
        } else {
            await api.post('portal/products', form);
            triggerNotification('Product Added');
        }
        closePanel();
        loadData();
    } catch (e) {
        console.error('Error saving product:', e);
        alert(e.response?.data?.message || 'Error saving product');
    } finally { loading.value = false; }
};

const deleteProduct = async (id) => {
    if (!confirm('Permanently delete this product?')) return;
    try {
        await api.delete(`portal/products/${id}`);
        triggerNotification('Product Removed');
        loadData();
    } catch (e) {}
};

const triggerNotification = (msg) => {
    notification.message = msg; notification.show = true;
    setTimeout(() => notification.show = false, 3000);
};

const filteredProducts = computed(() => {
    const q = searchQuery.value.toLowerCase();
    return products.value.filter(p => p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q));
});

onMounted(loadData);
</script>

<style scoped>
.slide-enter-active, .slide-leave-active { transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-enter-from, .slide-leave-to { transform: translateX(100%); }
.slide-fade-enter-active, .slide-fade-leave-active { transition: all 0.3s; }
.slide-fade-enter-from, .slide-fade-leave-to { transform: translateY(20px); opacity: 0; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
