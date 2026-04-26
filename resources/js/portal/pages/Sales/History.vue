<template>
    <div class="h-screen flex flex-col bg-slate-50 overflow-hidden relative">
        <!-- Sale Details Slide Panel -->
        <Transition name="slide">
            <div v-if="panel.show" class="fixed inset-y-0 right-0 w-[450px] bg-white shadow-2xl z-[110] border-l border-slate-200 flex flex-col">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <div>
                        <h2 class="font-black text-slate-800 uppercase tracking-tight">Sale Details</h2>
                        <p class="text-xs font-bold text-indigo-600">Transaction #{{ panel.data?.id }}</p>
                    </div>
                    <button @click="panel.show = false" class="p-2 hover:bg-slate-200 rounded-full transition-colors">
                        <X class="w-5 h-5 text-slate-400" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-8 custom-scrollbar">
                    <!-- Customer & Info -->
                    <section>
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Customer Info</h3>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <p class="text-xs font-black text-slate-800 uppercase">{{ panel.data?.customer?.name || 'Walk-in Customer' }}</p>
                            <p class="text-[10px] font-bold text-slate-500 mt-1">{{ formatDate(panel.data?.created_at) }}</p>
                            <div class="mt-3 inline-block px-2 py-1 rounded bg-indigo-100 text-indigo-700 text-[8px] font-black uppercase">
                                Paid via {{ panel.data?.payment_method }}
                            </div>
                        </div>
                    </section>

                    <!-- Items List -->
                    <section>
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Items Sold</h3>
                        <div class="space-y-3">
                            <div v-for="item in panel.data?.items" :key="item.id" class="flex justify-between items-center bg-white border-b border-slate-50 pb-3">
                                <div>
                                    <p class="text-[10px] font-black text-slate-800 uppercase">{{ item.product?.name }}</p>
                                    <p class="text-[9px] font-bold text-slate-400">{{ item.quantity }} x ${{ parseFloat(item.unit_price).toFixed(2) }}</p>
                                </div>
                                <span class="text-xs font-black text-slate-700">${{ parseFloat(item.total_price).toFixed(2) }}</span>
                            </div>
                        </div>
                    </section>

                    <!-- Financial Summary -->
                    <section class="bg-slate-900 text-white p-6 rounded-3xl shadow-xl shadow-slate-200">
                        <div class="space-y-2 border-b border-slate-700 pb-4 mb-4">
                            <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase">
                                <span>Subtotal</span>
                                <span>${{ parseFloat(panel.data?.subtotal).toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase">
                                <span>Tax</span>
                                <span>$0.00</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-black uppercase tracking-widest">Total Amount</span>
                            <span class="text-2xl font-black text-emerald-400">${{ parseFloat(panel.data?.total_amount).toFixed(2) }}</span>
                        </div>
                    </section>
                </div>

                <div class="p-6 bg-slate-50 border-t border-slate-100">
                    <button @click="printReceipt" class="w-full bg-slate-900 text-white py-4 rounded-xl font-black text-xs uppercase flex items-center justify-center gap-2 hover:bg-black transition-all">
                        <Printer class="w-4 h-4" /> Print Receipt
                    </button>
                </div>
            </div>
        </Transition>

        <!-- Header & Stats -->
        <header class="bg-white border-b border-slate-200 px-8 py-6 shadow-sm z-10">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="p-2 bg-indigo-600 rounded-lg"><History class="w-6 h-6 text-white" /></div>
                    <div>
                        <h1 class="text-xl font-black text-slate-800 uppercase leading-none">Sales History</h1>
                        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mt-1.5">Revenue Tracking</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="bg-slate-50 px-6 py-3 rounded-2xl border border-slate-100 text-right">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Today's Revenue</p>
                        <p class="text-lg font-black text-slate-800">${{ stats.todayRevenue.toFixed(2) }}</p>
                    </div>
                    <div class="bg-slate-900 px-6 py-3 rounded-2xl text-right shadow-lg shadow-slate-200">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Total Sales</p>
                        <p class="text-lg font-black text-emerald-400">${{ stats.totalRevenue.toFixed(2) }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative flex-1 max-w-md">
                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4" />
                    <input v-model="searchQuery" type="text" placeholder="Search by customer or ID..." class="w-full pl-12 pr-4 py-2.5 bg-slate-50 border-none rounded-full text-sm font-bold focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
        </header>

        <!-- Table Body -->
        <div class="flex-1 overflow-hidden flex flex-col">
            <div class="px-8 py-3 bg-slate-800 text-white text-[10px] font-black uppercase tracking-widest grid grid-cols-12 gap-4 shadow-lg z-10">
                <div class="col-span-2">Date / Time</div>
                <div class="col-span-1">ID</div>
                <div class="col-span-3">Customer</div>
                <div class="col-span-2">Payment</div>
                <div class="col-span-2 text-right">Amount</div>
                <div class="col-span-2 text-right">Actions</div>
            </div>

            <div class="flex-1 overflow-y-auto px-8 py-4 space-y-2 custom-scrollbar">
                <div v-for="sale in filteredSales" :key="sale.id" @click="viewSale(sale)" class="bg-white border border-slate-200 rounded-xl p-4 grid grid-cols-12 gap-4 items-center hover:border-indigo-300 transition-all cursor-pointer group">
                    <div class="col-span-2">
                        <p class="text-[10px] font-black text-slate-800 uppercase">{{ formatDateShort(sale.created_at) }}</p>
                        <p class="text-[9px] font-bold text-slate-400">{{ formatTime(sale.created_at) }}</p>
                    </div>
                    <div class="col-span-1">
                        <span class="text-[10px] font-black text-indigo-600">#{{ sale.id }}</span>
                    </div>
                    <div class="col-span-3">
                        <p class="text-[10px] font-black text-slate-700 uppercase truncate">{{ sale.customer?.name || 'Walk-in' }}</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[8px] font-black px-2 py-1 rounded bg-slate-100 text-slate-500 uppercase">{{ sale.payment_method }}</span>
                    </div>
                    <div class="col-span-2 text-right">
                        <p class="font-black text-slate-900">${{ parseFloat(sale.total_amount).toFixed(2) }}</p>
                    </div>
                    <div class="col-span-2 flex justify-end">
                        <button class="p-2 hover:bg-indigo-50 text-slate-300 group-hover:text-indigo-600 rounded-lg transition-colors">
                            <Eye class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <div v-if="filteredSales.length === 0" class="flex flex-col items-center justify-center py-20 opacity-30">
                    <History :size="64" />
                    <p class="font-black uppercase text-xs mt-4 tracking-widest">No sales records found</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';
import { api } from "../../../plugins/axios";
import { History, Search, Eye, X, Printer } from 'lucide-vue-next';
import dayjs from 'dayjs';

const sales = ref([]);
const searchQuery = ref('');
const panel = reactive({ show: false, data: null });

const stats = reactive({
    todayRevenue: 0,
    totalRevenue: 0
});

const loadData = async () => {
    try {
        const { data } = await api.get('portal/sales');
        sales.value = data;
        calculateStats();
    } catch (e) {
        console.error("Failed to load sales", e);
    }
};

const calculateStats = () => {
    const today = dayjs().format('YYYY-MM-DD');
    stats.totalRevenue = sales.value.reduce((sum, s) => sum + parseFloat(s.total_amount), 0);
    stats.todayRevenue = sales.value
        .filter(s => dayjs(s.created_at).format('YYYY-MM-DD') === today)
        .reduce((sum, s) => sum + parseFloat(s.total_amount), 0);
};

const viewSale = (sale) => {
    panel.data = sale;
    panel.show = true;
};

const filteredSales = computed(() => {
    const q = searchQuery.value.toLowerCase();
    return sales.value.filter(s =>
        s.id.toString().includes(q) ||
        (s.customer && s.customer.name.toLowerCase().includes(q))
    );
});

const formatDate = (d) => dayjs(d).format('MMMM D, YYYY - HH:mm');
const formatDateShort = (d) => dayjs(d).format('MMM D, YYYY');
const formatTime = (d) => dayjs(d).format('HH:mm A');

const printReceipt = () => {
    window.print(); // Simple for now, can be specialized later
};

onMounted(loadData);
</script>

<style scoped>
.slide-enter-active, .slide-leave-active { transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-enter-from, .slide-leave-to { transform: translateX(100%); }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

@media print {
    .no-print { display: none; }
}
</style>
