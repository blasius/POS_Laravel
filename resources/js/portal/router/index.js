import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../store/authStore";
import DashboardLayout from "../layouts/DashboardLayout.vue";

const router = createRouter({
    history: createWebHistory("/portal/"),
    routes: [
        {
            path: "/login",
            name: "Login",
            component: () => import("../pages/Auth/Login.vue"),
            meta: { guestOnly: true }
        },
        {
            path: "/",
            component: DashboardLayout,
            meta: { requiresAuth: true },
            children: [
                { path: "", redirect: "dashboard" },
                { path: "dashboard", name: "Dashboard", component: () => import("../pages/Dashboard.vue") },
                { path: "pos", name: "POS", component: () => import("../pages/POS.vue") },
                {
                    path: "inventory",
                    children: [
                        { path: "products", name: "Products", component: () => import("../pages/Inventory/Products.vue") },
                        { path: "categories", name: "Categories", component: () => import("../pages/Inventory/Categories.vue") },
                    ]
                },
                { path: "customers", name: "Customers", component: () => import("../pages/Customers/Index.vue") },
                { path: "settings", name: "Settings", component: () => import("../pages/Settings.vue") },
                { path: "support", name: "Support", component: () => import("../pages/Support/Index.vue") },

                // Legacy / To be refactored
                { path: "reports", component: () => import("../pages/Reports/Index.vue") },
            ],
        },
        { path: "/:pathMatch(.*)*", redirect: "/login" },
    ],
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    if (!authStore.isInitialized) {
        await authStore.checkAuth();
    }
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
    if (requiresAuth && !authStore.user) {
        next({ name: 'Login' });
    } else if (to.name === 'Login' && authStore.user) {
        next({ name: 'Dashboard' });
    } else {
        next();
    }
});

export default router;
