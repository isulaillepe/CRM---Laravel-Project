<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    customers: {
        type: Array,
        required: true,
    }
});

// Search functionality
const searchQuery = ref('');
const filteredCustomers = computed(() => {
    if (!searchQuery.value) return props.customers;
    const q = searchQuery.value.toLowerCase();
    return props.customers.filter(c => 
        c.name.toLowerCase().includes(q) || 
        c.email.toLowerCase().includes(q) || 
        (c.phone && c.phone.includes(q))
    );
});

// Modal State Variables
const isCreateOpen = ref(false);
const isEditOpen = ref(false);
const editingCustomer = ref(null);

// Forms
const createForm = useForm({
    name: '',
    email: '',
    phone: '',
    status: 'active'
});

const editForm = useForm({
    name: '',
    email: '',
    phone: '',
    status: 'active'
});

// Handlers
const openCreateModal = () => {
    createForm.reset();
    createForm.clearErrors();
    isCreateOpen.value = true;
};

const closeCreateModal = () => {
    isCreateOpen.value = false;
};

const openEditModal = (customer) => {
    editingCustomer.value = customer;
    editForm.name = customer.name;
    editForm.email = customer.email;
    editForm.phone = customer.phone || '';
    editForm.status = customer.status;
    editForm.clearErrors();
    isEditOpen.value = true;
};

const closeEditModal = () => {
    isEditOpen.value = false;
    editingCustomer.value = null;
};

const submitCreate = () => {
    createForm.post(route('customers.store'), {
        onSuccess: () => {
            closeCreateModal();
        }
    });
};

const submitEdit = () => {
    editForm.patch(route('customers.update', editingCustomer.value.id), {
        onSuccess: () => {
            closeEditModal();
        }
    });
};

const deleteCustomer = (id) => {
    if (confirm('Are you sure you want to permanently delete this customer? All associated proposals will be deleted.')) {
        useForm({}).delete(route('customers.destroy', id));
    }
};

// Aesthetics Helpers
const getAvatarColor = (name) => {
    const colors = [
        'bg-indigo-50 text-indigo-600 border-indigo-100',
        'bg-emerald-50 text-emerald-600 border-emerald-100',
        'bg-amber-50 text-amber-600 border-amber-100',
        'bg-rose-50 text-rose-600 border-rose-100',
        'bg-violet-50 text-violet-600 border-violet-100',
        'bg-sky-50 text-sky-600 border-sky-100',
    ];
    let sum = 0;
    for (let i = 0; i < name.length; i++) {
        sum += name.charCodeAt(i);
    }
    return colors[sum % colors.length];
};

const getInitials = (name) => {
    if (!name) return 'C';
    return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
};
</script>

<template>
    <Head title="Customer Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 tracking-tight">Customer Database</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage accounts, status details, and review associated pipeline deals.</p>
                </div>
                <div>
                    <button 
                        @click="openCreateModal"
                        class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out shadow-sm hover:shadow"
                    >
                        <svg class="h-5 w-5 mr-2 -ml-1 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Customer
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Search & Filters -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-center">
                    <div class="relative w-full md:max-w-md">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input 
                            v-model="searchQuery" 
                            type="text" 
                            placeholder="Search by name, email or phone..." 
                            class="pl-10 pr-4 py-2.5 w-full bg-gray-50 hover:bg-gray-100/70 focus:bg-white text-sm text-gray-900 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
                        />
                    </div>
                    <div class="text-xs text-gray-400 font-medium">
                        Showing {{ filteredCustomers.length }} of {{ customers.length }} clients
                    </div>
                </div>

                <!-- Customer Table Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/75 border-b border-gray-100 text-gray-500 text-xs uppercase font-semibold tracking-wider">
                                    <th class="p-4 pl-6">Client Profile</th>
                                    <th class="p-4">Contact Info</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 pr-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-600">
                                <tr 
                                    v-for="customer in filteredCustomers" 
                                    :key="customer.id" 
                                    class="hover:bg-gray-50/50 transition duration-150"
                                >
                                    <!-- Avatar & Name -->
                                    <td class="p-4 pl-6">
                                        <div class="flex items-center space-x-3.5">
                                            <div 
                                                :class="getAvatarColor(customer.name)"
                                                class="h-10 w-10 rounded-xl border flex items-center justify-center font-bold text-sm tracking-wider shadow-inner shrink-0"
                                            >
                                                {{ getInitials(customer.name) }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900 leading-tight">{{ customer.name }}</div>
                                                <div class="text-xs text-gray-400 mt-0.5">ID: #{{ customer.id }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Email & Phone -->
                                    <td class="p-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center text-gray-700">
                                                <svg class="h-4 w-4 text-gray-400 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                <span class="font-medium text-xs">{{ customer.email }}</span>
                                            </div>
                                            <div class="flex items-center text-gray-400">
                                                <svg class="h-4 w-4 text-gray-400 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 00.96.725h1.64a1 1 0 00.96-.725l.548-2.2A1 1 0 0117 3H20a2 2 0 012 2v1c0 9.389-7.611 17-17 17H4a2 2 0 01-2-2V5z" />
                                                </svg>
                                                <span class="text-xs">{{ customer.phone || 'No phone recorded' }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="p-4">
                                        <span 
                                            v-if="customer.status === 'active'"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-55 text-emerald-700 bg-emerald-50 border border-emerald-200/50 shadow-sm"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                            Active
                                        </span>
                                        <span 
                                            v-else
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-500 border border-gray-200/50 shadow-sm"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400 mr-1.5"></span>
                                            Inactive
                                        </span>
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="p-4 pr-6 text-right">
                                        <div class="inline-flex items-center space-x-1.5">
                                            <button 
                                                @click="openEditModal(customer)" 
                                                class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50/70 rounded-xl transition duration-150 focus:outline-none"
                                                title="Edit Client"
                                            >
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button 
                                                @click="deleteCustomer(customer.id)" 
                                                class="p-2 text-gray-500 hover:text-rose-600 hover:bg-rose-50/70 rounded-xl transition duration-150 focus:outline-none"
                                                title="Delete Client"
                                            >
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="filteredCustomers.length === 0">
                                    <td colspan="4" class="p-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <p class="font-medium text-gray-500">No customers found</p>
                                            <p class="text-xs">Adjust your search parameters or add a new client to database.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- CREATE CUSTOMER MODAL -->
        <div 
            v-if="isCreateOpen" 
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" 
            role="dialog" 
            aria-modal="true"
        >
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop blur-sm bg-black/50 -->
                <div 
                    @click="closeCreateModal"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                    aria-hidden="true"
                ></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal card -->
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-6">
                        <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-5">
                            <h3 class="text-lg font-bold text-gray-900" id="modal-title">
                                Add New Customer
                            </h3>
                            <button @click="closeCreateModal" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-50 transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form @submit.prevent="submitCreate" class="space-y-4">
                            <!-- Full Name -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Full Name</label>
                                <input 
                                    v-model="createForm.name" 
                                    type="text" 
                                    required 
                                    class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                                    placeholder="Isula Illeperuma"
                                />
                                <div v-if="createForm.errors.name" class="text-rose-500 text-xs mt-1 font-medium">{{ createForm.errors.name }}</div>
                            </div>

                            <!-- Email Address -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Email Address</label>
                                <input 
                                    v-model="createForm.email" 
                                    type="email" 
                                    required 
                                    class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                                    placeholder="hello@gmail.com"
                                />
                                <div v-if="createForm.errors.email" class="text-rose-500 text-xs mt-1 font-medium">{{ createForm.errors.email }}</div>
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Phone Number</label>
                                <input 
                                    v-model="createForm.phone" 
                                    type="text" 
                                    class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                                    placeholder="+94774710311"
                                />
                                <div v-if="createForm.errors.phone" class="text-rose-500 text-xs mt-1 font-medium">{{ createForm.errors.phone }}</div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Profile Status</label>
                                <select 
                                    v-model="createForm.status" 
                                    class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="pt-5 border-t border-gray-100 flex justify-end space-x-3 mt-6">
                                <button 
                                    type="button" 
                                    @click="closeCreateModal" 
                                    class="px-4 py-2 border border-gray-250 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit" 
                                    :disabled="createForm.processing" 
                                    class="px-4 py-2 bg-indigo-600 border border-transparent rounded-xl text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50 transition shadow-sm"
                                >
                                    {{ createForm.processing ? 'Saving...' : 'Create Client' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- EDIT CUSTOMER MODAL -->
        <div 
            v-if="isEditOpen" 
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" 
            role="dialog" 
            aria-modal="true"
        >
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div 
                    @click="closeEditModal"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                    aria-hidden="true"
                ></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal card -->
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-6">
                        <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-5">
                            <h3 class="text-lg font-bold text-gray-900" id="modal-title">
                                Update Customer Details
                            </h3>
                            <button @click="closeEditModal" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-50 transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form @submit.prevent="submitEdit" class="space-y-4">
                            <!-- Full Name -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Full Name</label>
                                <input 
                                    v-model="editForm.name" 
                                    type="text" 
                                    required 
                                    class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                                />
                                <div v-if="editForm.errors.name" class="text-rose-500 text-xs mt-1 font-medium">{{ editForm.errors.name }}</div>
                            </div>

                            <!-- Email Address -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Email Address</label>
                                <input 
                                    v-model="editForm.email" 
                                    type="email" 
                                    required 
                                    class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                                />
                                <div v-if="editForm.errors.email" class="text-rose-500 text-xs mt-1 font-medium">{{ editForm.errors.email }}</div>
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Phone Number</label>
                                <input 
                                    v-model="editForm.phone" 
                                    type="text" 
                                    class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                                />
                                <div v-if="editForm.errors.phone" class="text-rose-500 text-xs mt-1 font-medium">{{ editForm.errors.phone }}</div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Profile Status</label>
                                <select 
                                    v-model="editForm.status" 
                                    class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="pt-5 border-t border-gray-100 flex justify-end space-x-3 mt-6">
                                <button 
                                    type="button" 
                                    @click="closeEditModal" 
                                    class="px-4 py-2 border border-gray-250 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit" 
                                    :disabled="editForm.processing" 
                                    class="px-4 py-2 bg-indigo-600 border border-transparent rounded-xl text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50 transition shadow-sm"
                                >
                                    {{ editForm.processing ? 'Updating...' : 'Save Changes' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>