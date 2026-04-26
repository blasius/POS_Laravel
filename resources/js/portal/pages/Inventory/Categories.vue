<template>
    <div class="h-screen flex flex-col bg-slate-50 overflow-hidden relative">
        <!-- Transitioning Slide Panel for Add/Edit -->
        <Transition name="slide">
            <div v-if="panel.show" class="fixed inset-y-0 right-0 w-96 bg-white shadow-2xl z-[110] border-l border-slate-200 flex flex-col">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <div>
                        <h2 class="font-black text-slate-800 uppercase tracking-tight">
                            {{ panel.editing ? 'Edit Category' : 'New Category' }}
                        </h2>
                        <p class="text-xs font-bold text-indigo-600">Product Classification</p>
                    </div>
                    <button @click="closePanel" class="p-2 hover:bg-slate-200 rounded-full transition-colors">
                        <X class="w-5 h-5 text-slate-400" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Category Name</label>
                        <input v-model="form.name" type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="e.g. Beverages">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Description</label>
                        <textarea v-model="form.description" rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Details about this category..."></textarea>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <input type="checkbox" v-model="form.is_active" id="active_check" class="w-4 h-4 text-indigo-600 rounded">
                        <label for="active_check" class="text-xs font-black text-slate-600 uppercase">Active for POS</label>
                    </div>
                </div>

                <div class="p-6 bg-slate-50 border-t border-slate-100">
                    <button @click="saveCategory" :disabled="loading" class="w-full bg-indigo-600 text-white py-4 rounded-xl font-black text-xs uppercase flex items-center justify-center gap-2 hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 disabled:opacity-50">
                        <Save v-if="!loading" class="w-4 h-4" />
                        <span v-else class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        {{ panel.editing ? 'Update Category' : 'Create Category' }}
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
                <div class="p-2 bg-indigo-600 rounded-lg"><LayoutGrid class="w-6 h-6 text-white" /></div>
                <div>
                    <h1 class="text-xl font-black text-slate-800 uppercase leading-none">Inventory</h1>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Category Management</span>
                    </div>
                </div>
                <div class="relative ml-4">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                    <input v-model="searchQuery" type="text" placeholder="Filter categories..." class="pl-10 pr-4 py-2 w-64 bg-slate-100 border-none rounded-full text-sm focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button @click="openAddPanel" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white text-xs font-black rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">
                    <Plus class="w-4 h-4" /> ADD NEW
                </button>
            </div>
        </header>

        <!-- Table Header -->
        <div class="px-8 py-3 bg-slate-800 text-white text-[10px] font-black uppercase tracking-widest grid grid-cols-12 gap-4 shadow-lg z-10">
            <div class="col-span-4">Category Details</div>
            <div class="col-span-3">Slug</div>
            <div class="col-span-2 text-center">Status</div>
            <div class="col-span-3 text-right">Actions</div>
        </div>

        <!-- Table Content -->
        <div class="flex-1 overflow-y-auto px-8 py-4 space-y-2 custom-scrollbar">
            <div v-for="cat in filteredCategories" :key="cat.id" class="bg-white border border-slate-200 rounded-xl p-4 grid grid-cols-12 gap-4 items-center hover:border-indigo-300 transition-all group">
                <div class="col-span-4">
                    <p class="font-black text-slate-900 uppercase tracking-tighter">{{ cat.name }}</p>
                    <p class="text-[10px] font-medium text-slate-400 mt-0.5 truncate">{{ cat.description || 'No description provided' }}</p>
                </div>
                <div class="col-span-3">
                    <code class="text-[10px] bg-slate-100 px-2 py-1 rounded text-slate-600 font-bold">{{ cat.slug }}</code>
                </div>
                <div class="col-span-2 text-center">
                    <span :class="cat.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'" class="text-[8px] font-black px-2 py-1 rounded uppercase">
                        {{ cat.is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="col-span-3 flex justify-end gap-2">
                    <button @click="openEditPanel(cat)" class="p-2 hover:bg-indigo-50 text-slate-300 hover:text-indigo-600 rounded-lg transition-colors">
                        <Edit class="w-4 h-4" />
                    </button>
                    <button @click="deleteCategory(cat.id)" class="p-2 hover:bg-rose-50 text-slate-300 hover:text-rose-600 rounded-lg transition-colors">
                        <Trash2 class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <div v-if="filteredCategories.length === 0" class="flex flex-col items-center justify-center py-20 opacity-40">
                <LayoutGrid :size="48" class="text-slate-300 mb-4" />
                <p class="font-black text-slate-400 uppercase text-xs tracking-widest">No categories found</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';
import { api } from "../../../plugins/axios";
import { Plus, Search, LayoutGrid, Edit, Trash2, X, Save, Check } from 'lucide-vue-next';

const categories = ref([]);
const searchQuery = ref('');
const loading = ref(false);
const panel = reactive({ show: false, editing: false, currentId: null });
const notification = reactive({ show: false, message: '' });

const form = reactive({
    name: '',
    description: '',
    is_active: true
});

const loadData = async () => {
    try {
        const { data } = await api.get('portal/categories');
        categories.value = data;
    } catch (e) {
        // Error is handled by global interceptor, but we catch to stop execution
    }
};

const openAddPanel = () => {
    panel.editing = false;
    panel.currentId = null;
    form.name = '';
    form.description = '';
    form.is_active = true;
    panel.show = true;
};

const openEditPanel = (cat) => {
    panel.editing = true;
    panel.currentId = cat.id;
    form.name = cat.name;
    form.description = cat.description;
    form.is_active = !!cat.is_active;
    panel.show = true;
};

const closePanel = () => {
    panel.show = false;
};

const saveCategory = async () => {
    loading.value = true;
    try {
        if (panel.editing) {
            await api.put(`portal/categories/${panel.currentId}`, form);
            triggerNotification('Category Updated');
        } else {
            await api.post('portal/categories', form);
            triggerNotification('Category Created');
        }
        closePanel();
        loadData();
    } catch (e) {
        // Validation errors usually
    } finally {
        loading.value = false;
    }
};

const deleteCategory = async (id) => {
    if (!confirm('Are you sure? This cannot be undone.')) return;
    try {
        await api.delete(`portal/categories/${id}`);
        triggerNotification('Category Removed');
        loadData();
    } catch (e) {
        alert(e.response?.data?.message || 'Delete failed');
    }
};

const triggerNotification = (msg) => {
    notification.message = msg;
    notification.show = true;
    setTimeout(() => notification.show = false, 3000);
};

const filteredCategories = computed(() => {
    const q = searchQuery.value.toLowerCase();
    return categories.value.filter(c => c.name.toLowerCase().includes(q));
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
