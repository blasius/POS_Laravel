import {
    Home,
    ShoppingCart,
    Package,
    Users,
    Receipt,
    BarChart3,
    Settings,
    HelpCircle,
    LayoutGrid,
    Wallet,
    History
} from "lucide-vue-next";

export const menu = [
    {
        label: "Dashboard",
        icon: Home,
        to: "/dashboard",
    },
    {
        label: "POS",
        icon: ShoppingCart,
        to: "/pos",
    },
    {
        label: "Inventory",
        icon: Package,
        children: [
            { label: "Products", to: "/inventory/products" },
            { label: "Categories", to: "/inventory/categories" },
            { label: "Stock Adjustments", to: "/inventory/adjustments" },
        ],
    },
    {
        label: "Sales",
        icon: Receipt,
        children: [
            { label: "Sales History", to: "/sales/history" },
            { label: "Pending Orders", to: "/sales/pending" },
        ],
    },
    {
        label: "Customers",
        icon: Users,
        to: "/customers",
    },
    {
        label: "Expenses",
        icon: Wallet,
        to: "/expenses",
    },
    {
        label: "Reports",
        icon: BarChart3,
        children: [
            { label: "Revenue", to: "/reports/revenue" },
            { label: "Inventory Report", to: "/reports/inventory" },
            { label: "Profit & Loss", to: "/reports/profit-loss" },
        ],
    },
    {
        label: "Settings",
        icon: Settings,
        to: "/settings",
    },
    {
        label: "Support",
        icon: HelpCircle,
        to: "/support",
    },
];
