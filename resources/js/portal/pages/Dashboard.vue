<template>
    <div class="p-8 space-y-8 bg-slate-50 min-h-screen custom-scrollbar overflow-y-auto">
        <!-- Welcome Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Business Overview</h1>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mt-1">Real-time Analytics</p>
            </div>
            <div class="flex gap-3">
                <router-link to="/pos" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center gap-2">
                    <Plus class="w-4 h-4" /> New Sale
                </router-link>
            </div>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-100 text-emerald-600 rounded-2xl"><DollarSign class="w-6 h-6" /></div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Today's Revenue</p>
                    <p class="text-xl font-black text-slate-800 tracking-tighter">${{ stats.today_revenue.toFixed(2) }}</p>
                </div>
            </div>
            <div class="bg-slate-900 p-6 rounded-3xl shadow-xl shadow-slate-200 flex items-center gap-4 text-white">
                <div class="p-3 bg-slate-800 text-emerald-400 rounded-2xl"><TrendingUp class="w-6 h-6" /></div>
                <div>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Total Sales</p>
                    <p class="text-xl font-black tracking-tighter">${{ stats.total_revenue.toFixed(2) }}</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-indigo-100 text-indigo-600 rounded-2xl"><Users class="w-6 h-6" /></div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Customers</p>
                    <p class="text-xl font-black text-slate-800 tracking-tighter">{{ stats.total_customers }}</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-rose-100 text-rose-600 rounded-2xl"><AlertTriangle class="w-6 h-6" /></div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Low Stock</p>
                    <p class="text-xl font-black text-slate-800 tracking-tighter">{{ stats.low_stock_count }} Items</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Recent Sales Table -->
            <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Recent Transactions</h3>
                    <router-link to="/sales/history" class="text-[10px] font-black text-indigo-600 uppercase hover:underline">View All</router-link>
                </div>
                <div class="p-6 space-y-4">
                    <div v-for="sale in recentSales" :key="sale.id" class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-indigo-200 transition-all cursor-default group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-white rounded-xl border border-slate-200 flex items-center justify-center text-slate-400 group-hover:text-indigo-500 transition-colors">
                                <Receipt class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-xs font-black text-slate-800 uppercase tracking-tighter">{{ sale.customer?.name || 'Walk-in Customer' }}</p>
                                <p class="text-[10px] font-bold text-slate-400">{{ formatDate(sale.created_at) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-slate-800 tracking-tighter">${{ parseFloat(sale.total_amount).toFixed(2) }}</p>
                            <span class="text-[8px] font-black uppercase text-indigo-600 px-2 py-0.5 bg-indigo-50 rounded">{{ sale.payment_method }}</span>
                        </div>
                    </div>

                    <div v-if="recentSales.length === 0" class="py-10 text-center text-slate-300">
                        <p class="text-xs font-black uppercase tracking-widest opacity-40">No sales yet</p>
                    </div>
                </div>
            </div>

            <!-- Low Stock Side List -->
            <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-sm flex flex-col">
                <div class="p-6 border-b border-slate-50">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Stock Alerts</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div v-for="prod in lowStockProducts" :key="prod.id" class="flex items-center justify-between p-4 bg-rose-50/50 rounded-2xl border border-rose-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-rose-500 shadow-sm">
                                <Package class="w-4 h-4" />
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-800 uppercase tracking-tighter truncate w-32">{{ prod.name }}</p>
                                <p class="text-[10px] font-bold text-rose-600">{{ prod.stock_quantity }} Remaining</p>
                            </div>
                        </div>
                        <router-link to="/inventory/products" class="p-2 text-slate-300 hover:text-indigo-600 transition-colors">
                            <ArrowRight class="w-4 h-4" />
                        </router-link>
                    </div>

                    <div v-if="lowStockProducts.length === 0" class="py-10 text-center text-emerald-500 bg-emerald-50 rounded-2xl">
                        <CheckCircle class="w-8 h-8 mx-auto mb-2 opacity-40" />
                        <p class="text-[10px] font-black uppercase tracking-widest">Inventory Healthy</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { api } from "../../plugins/axios";
import {
    DollarSign, TrendingUp, Users, AlertTriangle,
    Plus, Receipt, Package, ArrowRight, CheckCircle
} from 'lucide-vue-next';
import dayjs from 'dayjs';

const stats = reactive({
    today_revenue: 0,
    total_revenue: 0,
    total_customers: 0,
    low_stock_count: 0
});

const recentSales = ref([]);
const lowStockProducts = ref([]);

const loadData = async () => {
    try {
        const { data } = await api.get('portal/dashboard/summary');
        Object.assign(stats, data.stats);
        recentSales.value = data.recent_sales;
        lowStockProducts.value = data.low_stock_products;
    } catch (e) {
        console.error("Dashboard loading failed", e);
    }
};

const formatDate = (date) => dayjs(date).format('MMM D, HH:mm A');

onMounted(loadData);
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
