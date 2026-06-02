<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({
    customers: {
        type: Array,
        required: true,
    }
});

// Auto-generate a unique serial number
const generateInvoiceNumber = () => {
    const date = new Date();
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const random = Math.floor(1000 + Math.random() * 9000);
    return `INV-${year}${month}${day}-${random}`;
};

// Form state
const form = useForm({
    customer_id: '',
    invoice_number: '',
    amount: '',
    status: 'unpaid',
    due_date: ''
});

onMounted(() => {
    form.invoice_number = generateInvoiceNumber();
    const future = new Date();
    future.setDate(future.getDate() + 30);
    form.due_date = future.toISOString().slice(0, 10);
});

const submit = () => {
    form.post(route('invoices.store'));
};
</script>

<template>
    <Head title="Create New Invoice" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 tracking-tight">Create Invoice</h2>
                    <p class="text-sm text-gray-500 mt-1">Configure and issue a billing invoice to an existing client account.</p>
                </div>
                <Link 
                    :href="route('invoices.index')" 
                    class="inline-flex items-center px-4 py-2 border border-gray-250 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:shadow-sm transition"
                >
                    <svg class="h-5 w-5 mr-1.5 -ml-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to List
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                    
                    <form @submit.prevent="submit" class="space-y-5">
                        
                        <!-- Customer Selection -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Billing Client</label>
                            <select 
                                v-model="form.customer_id" 
                                required
                                class="mt-2 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                            >
                                <option value="" disabled>-- Select Customer Account --</option>
                                <option 
                                    v-for="customer in customers" 
                                    :key="customer.id" 
                                    :value="customer.id"
                                >
                                    {{ customer.name }} ({{ customer.email }})
                                </option>
                            </select>
                            <div v-if="form.errors.customer_id" class="text-rose-500 text-xs mt-1 font-medium">{{ form.errors.customer_id }}</div>
                            <p v-if="customers.length === 0" class="text-amber-600 text-xs mt-1.5 font-medium">
                                No clients exist in database yet. 
                                <Link :href="route('customers.index')" class="underline text-indigo-600 font-semibold">Create one first</Link>.
                            </p>
                        </div>

                        <!-- Invoice Number (with auto generation button) -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Invoice Number</label>
                            <div class="mt-2 flex space-x-2">
                                <input 
                                    v-model="form.invoice_number" 
                                    type="text" 
                                    required 
                                    placeholder="INV-YYYYMMDD-XXXX"
                                    class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                                />
                                <button 
                                    type="button"
                                    @click="form.invoice_number = generateInvoiceNumber()"
                                    class="px-3 py-2 border border-gray-200 text-gray-650 hover:bg-gray-50 rounded-xl text-xs font-semibold tracking-tight transition shrink-0"
                                    title="Regenerate Invoice ID"
                                >
                                    Regenerate
                                </button>
                            </div>
                            <div v-if="form.errors.invoice_number" class="text-rose-500 text-xs mt-1 font-medium">{{ form.errors.invoice_number }}</div>
                        </div>

                        <!-- Invoice Amount & Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Amount -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Billing Amount (LKR)</label>
                                <div class="mt-2 relative rounded-xl shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <span class="text-gray-400 text-sm">$</span>
                                    </div>
                                    <input 
                                        v-model="form.amount" 
                                        type="number" 
                                        step="0.01"
                                        min="0"
                                        required 
                                        placeholder="0.00"
                                        class="pl-8 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                                    />
                                </div>
                                <div v-if="form.errors.amount" class="text-rose-500 text-xs mt-1 font-medium">{{ form.errors.amount }}</div>
                            </div>

                            <!-- Invoice Status -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Invoice Status</label>
                                <select 
                                    v-model="form.status" 
                                    required
                                    class="mt-2 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                                >
                                    <option value="unpaid">Unpaid</option>
                                    <option value="paid">Paid (Cleared)</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                                <div v-if="form.errors.status" class="text-rose-500 text-xs mt-1 font-medium">{{ form.errors.status }}</div>
                            </div>
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Due Date</label>
                            <input 
                                v-model="form.due_date" 
                                type="date" 
                                required
                                class="mt-2 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                            />
                            <div v-if="form.errors.due_date" class="text-rose-500 text-xs mt-1 font-medium">{{ form.errors.due_date }}</div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-5 border-t border-gray-100 flex justify-end space-x-3 mt-6">
                            <Link 
                                :href="route('invoices.index')" 
                                class="px-4 py-2.5 border border-gray-250 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition"
                            >
                                Cancel
                            </Link>
                            <button 
                                type="submit" 
                                :disabled="form.processing" 
                                class="px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50 transition shadow-sm hover:shadow"
                            >
                                {{ form.processing ? 'Saving...' : 'Issue Invoice' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
