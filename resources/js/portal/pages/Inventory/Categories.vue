<template>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Categories</h1>
            <button
                @click="openModal()"
                class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition flex items-center gap-2"
            >
                <Plus :size="20" />
                Add Category
            </button>
        </div>

        <!-- Categories Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="category in categories" :key="category.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ category.name }}</div>
                            <div class="text-sm text-gray-500">{{ category.description }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ category.slug }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`">
                                {{ category.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="openModal(category)" class="text-teal-600 hover:text-teal-900 mr-3">Edit</button>
                            <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="categories.length === 0">
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                            No categories found. Start by adding one.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg w-full max-w-md p-6">
                <h2 class="text-xl font-bold mb-4">{{ editingCategory ? 'Edit Category' : 'Add Category' }}</h2>
                <form @submit.prevent="saveCategory">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-teal-500 focus:border-teal-500"
                            required
                        />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea
                            v-model="form.description"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-teal-500 focus:border-teal-500"
                            rows="3"
                        ></textarea>
                    </div>
                    <div class="mb-6 flex items-center">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
                            id="is_active"
                        />
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="isModalOpen = false"
                            class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700"
                        >
                            {{ editingCategory ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Plus } from 'lucide-vue-next';
import axios from 'axios';

const categories = ref([]);
const isModalOpen = ref(false);
const editingCategory = ref(null);
const form = ref({
    name: '',
    description: '',
    is_active: true
});

const fetchCategories = async () => {
    try {
        const response = await axios.get('/api/portal/categories');
        categories.value = response.data;
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
};

const openModal = (category = null) => {
    if (category) {
        editingCategory.value = category;
        form.value = { ...category };
    } else {
        editingCategory.value = null;
        form.value = { name: '', description: '', is_active: true };
    }
    isModalOpen.value = true;
};

const saveCategory = async () => {
    try {
        if (editingCategory.value) {
            await axios.put(`/api/portal/categories/${editingCategory.value.id}`, form.value);
        } else {
            await axios.post('/api/portal/categories', form.value);
        }
        isModalOpen.value = false;
        fetchCategories();
    } catch (error) {
        console.error('Error saving category:', error);
        alert(error.response?.data?.message || 'Error saving category');
    }
};

const deleteCategory = async (id) => {
    if (!confirm('Are you sure you want to delete this category?')) return;
    try {
        await axios.delete(`/api/portal/categories/${id}`);
        fetchCategories();
    } catch (error) {
        console.error('Error deleting category:', error);
        alert(error.response?.data?.message || 'Error deleting category');
    }
};

onMounted(fetchCategories);
</script>
