<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function summary()
    {
        $today = Carbon::today();

        // Stats
        $todayRevenue = Sale::whereDate('created_at', $today)->sum('total_amount');
        $totalRevenue = Sale::sum('total_amount');
        $totalCustomers = Customer::count();
        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'alert_quantity')->count();

        // Recent Sales
        $recentSales = Sale::with('customer')
            ->latest()
            ->take(5)
            ->get();

        // Low Stock Products
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'alert_quantity')
            ->take(5)
            ->get();

        return response()->json([
            'stats' => [
                'today_revenue' => (float)$todayRevenue,
                'total_revenue' => (float)$totalRevenue,
                'total_customers' => $totalCustomers,
                'low_stock_count' => $lowStockCount,
            ],
            'recent_sales' => $recentSales,
            'low_stock_products' => $lowStockProducts,
        ]);
    }
}
