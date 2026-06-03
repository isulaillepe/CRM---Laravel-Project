<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router , usePage } from '@inertiajs/vue3';
import { ref, computed , watch} from 'vue';
const props = defineProps({
    invoices: {
        type: Array,
        required: true,
    }
});

// Search and filter state
const searchQuery = ref('');
const statusFilter = ref('all');

// Stats calculations
const totalInvoiced = computed(() => {
    return props.invoices.reduce((sum, inv) => sum + parseFloat(inv.amount || 0), 0);
});

const totalPaid = computed(() => {
    return props.invoices
        .filter(inv => inv.status === 'paid')
        .reduce((sum, inv) => sum + parseFloat(inv.amount || 0), 0);
});

const totalUnpaid = computed(() => {
    return props.invoices
        .filter(inv => inv.status === 'unpaid')
        .reduce((sum, inv) => sum + parseFloat(inv.amount || 0), 0);
});

const totalOverdue = computed(() => {
    return props.invoices
        .filter(inv => inv.status === 'overdue')
        .reduce((sum, inv) => sum + parseFloat(inv.amount || 0), 0);
});

// Filtering logic
const filteredInvoices = computed(() => {
    return props.invoices.filter(inv => {
        const matchesSearch = 
            inv.invoice_number.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (inv.customer && inv.customer.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
        
        const matchesStatus = 
            statusFilter.value === 'all' || 
            inv.status === statusFilter.value;
            
        return matchesSearch && matchesStatus;
    });
});

// Delete handler
const deleteInvoice = (id) => {
    if (confirm('Are you sure you want to permanently delete this invoice?')) {
        useForm({}).delete(route('invoices.destroy', id));
    }
};

// Format helpers
const formatCurrency = (val) => {
    return 'Rs. ' + parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const formatDate = (dateStr) => {
    if (!dateStr) return 'N/A';
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
const emailInvoiceToClient = (invoiceId) => {
    // Fire an Inertia post request to our newly configured backend route
    router.post(`/invoices/${invoiceId}/send`, {}, {
        onStart: () => alert('Contacting Stripe and dispatching email package...'),
        onSuccess: () => alert('Success! Check your Mailtrap dashboard.'),
        onError: (err) => console.error(err)
    });
    };

    const page = usePage();
const flashMessage = ref(null);

// Watch for flash notifications sliding down the Inertia data wire
watch(() => page.props.flash?.success, (newSuccess) => {
    if (newSuccess) {
        flashMessage.value = newSuccess;
        // Automatically dismiss the success alert after 5 seconds
        setTimeout(() => {
            flashMessage.value = null;
        }, 5000);
    }
}, { immediate: true });


</script>

<template>
    <Head title="Billing & Invoices" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 tracking-tight">Billing & Invoices</h2>
                    <p class="text-sm text-gray-500 mt-1">Monitor receivables, log collections, and review client invoices.</p>
                </div>
                <div>
                    <Link 
                        :href="route('invoices.create')"
                        class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out shadow-sm hover:shadow"
                    >
                        <svg class="h-5 w-5 mr-2 -ml-1 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Invoice
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <div v-if="flashMessage" class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm text-sm font-semibold flex items-center space-x-2">
            <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ flashMessage }}</span>
        </div>
    </div>
    
                <!-- Invoicing Statistics Panels -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- panel 1: Total Invoiced -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Total Invoiced</div>
                            <div class="text-xl font-bold text-gray-900 mt-0.5">{{ formatCurrency(totalInvoiced) }}</div>
                        </div>
                    </div>

                    <!-- panel 2: Paid -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Paid Amount</div>
                            <div class="text-xl font-bold text-gray-900 mt-0.5">{{ formatCurrency(totalPaid) }}</div>
                        </div>
                    </div>

                    <!-- panel 3: Unpaid -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Unpaid Amount</div>
                            <div class="text-xl font-bold text-gray-900 mt-0.5">{{ formatCurrency(totalUnpaid) }}</div>
                        </div>
                    </div>

                    <!-- panel 4: Overdue -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="p-3 bg-rose-50 rounded-xl text-rose-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Overdue Balance</div>
                            <div class="text-xl font-bold text-gray-900 mt-0.5">{{ formatCurrency(totalOverdue) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Filters & Search -->
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
                            placeholder="Search by invoice number or client..." 
                            class="pl-10 pr-4 py-2.5 w-full bg-gray-50 hover:bg-gray-100/70 focus:bg-white text-sm text-gray-900 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
                        />
                    </div>
                    
                    <div class="flex items-center space-x-2 w-full md:w-auto">
                        <span class="text-xs text-gray-400 font-semibold shrink-0">Filter Status:</span>
                        <select 
                            v-model="statusFilter"
                            class="py-2 pl-3 pr-8 w-full md:w-40 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:bg-white focus:ring-indigo-500 focus:border-indigo-500 text-gray-600 transition"
                        >
                            <option value="all">All Invoices</option>
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/75 border-b border-gray-100 text-gray-500 text-xs uppercase font-semibold tracking-wider">
                                    <th class="p-4 pl-6">Invoice details</th>
                                    <th class="p-4">Client Name</th>
                                    <th class="p-4">Date Created</th>
                                    <th class="p-4">Amount Due</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 pr-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-600">
                                <tr 
                                    v-for="invoice in filteredInvoices" 
                                    :key="invoice.id" 
                                    class="hover:bg-gray-50/50 transition duration-150"
                                >
                                    <!-- Serial Number -->
                                    <td class="p-4 pl-6">
                                        <div class="flex items-center space-x-2.5">
                                            <div class="p-2 bg-indigo-50/50 rounded-lg text-indigo-600 border border-indigo-100">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <span class="font-bold text-gray-900 tracking-tight">{{ invoice.invoice_number }}</span>
                                        </div>
                                    </td>

                                    <!-- Client name -->
                                    <td class="p-4 font-medium text-gray-700">
                                        <div v-if="invoice.customer" class="flex items-center space-x-2">
                                            <div class="h-6 w-6 rounded bg-indigo-50 border border-indigo-150 flex items-center justify-center font-bold text-[10px] text-indigo-600">
                                                {{ invoice.customer.name.substring(0,2).toUpperCase() }}
                                            </div>
                                            <span>{{ invoice.customer.name }}</span>
                                        </div>
                                        <span v-else class="text-gray-400 italic text-xs">Customer deleted</span>
                                    </td>

                                    <!-- Created At -->
                                    <td class="p-4 text-xs font-medium text-gray-500">
                                        {{ formatDate(invoice.created_at) }}
                                    </td>

                                    <!-- Amount -->
                                    <td class="p-4 font-bold text-gray-900 text-sm">
                                        {{ formatCurrency(invoice.amount) }}
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="p-4">
                                        <span 
                                            v-if="invoice.status === 'paid'"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/50 shadow-sm"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Paid
                                        </span>
                                        <span 
                                            v-else-if="invoice.status === 'unpaid'"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/50 shadow-sm"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                            Unpaid
                                        </span>
                                        <span 
                                            v-else
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/50 shadow-sm"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                            Overdue
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-4 pr-6 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <Link 
                                                v-if="invoice.status !== 'paid'"
                                                :href="route('invoices.edit', invoice.id)"
                                                class="inline-flex items-center p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition duration-150 focus:outline-none"
                                                title="Edit Invoice"
                                            >
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </Link>
                                            <button 
                                                v-if="invoice.status !== 'paid'"
                                                @click="emailInvoiceToClient(invoice.id)" 
                                                class="inline-flex items-center p-2 text-gray-455 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition duration-150 focus:outline-none"
                                                title="Email Invoice to Client"
                                            >
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                            <button 
                                                @click="deleteInvoice(invoice.id)" 
                                                class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50/70 rounded-xl transition duration-150 focus:outline-none"
                                                title="Delete Invoice"
                                            >
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="filteredInvoices.length === 0">
                                    <td colspan="6" class="p-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="font-medium text-gray-500">No invoices found</p>
                                            <p class="text-xs">Adjust your search parameters or log a new invoice to get started.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
