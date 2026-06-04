<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router , usePage } from '@inertiajs/vue3';
import { ref, computed , watch} from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
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

// WhatsApp message handler
const sendWhatsApp = (invoice) => {
    if (!invoice.customer || !invoice.customer.phone) {
        alert('This customer does not have a phone number on file.');
        return;
    }
    // Strip ALL non-digit characters (including +) — wa.me expects digits only
    const phone = invoice.customer.phone.replace(/\D/g, '');
    const defaultMessage = `The invoice place of (${invoice.invoice_number}) is (${formatCurrency(invoice.amount)}) for the date (${formatDate(invoice.due_date)})`;
    // Let the user customise the message before sending
    const customMessage = prompt('Edit your WhatsApp message:', defaultMessage);
    if (customMessage === null) return; // user cancelled
    const url = `https://wa.me/${phone}?text=${encodeURIComponent(customMessage)}`;
    window.open(url, '_blank');
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

// Change status handler
const setStatus = (invoice, status) => {
    if (invoice.status === status) return;
    router.patch(route('invoices.update', invoice.id), {
        customer_id: invoice.customer_id,
        invoice_number: invoice.invoice_number,
        amount: invoice.amount,
        status: status,
        due_date: invoice.due_date
    }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Billing & Invoices" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div>
                    <h2 class="font-semibold text-xl text-zinc-900 tracking-tight">Billing & Invoices</h2>
                    <p class="text-sm text-zinc-500 mt-1">Monitor receivables, log collections, and review client invoices.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Minimalist Toggle Control -->
                    <div class="inline-flex rounded-lg border border-zinc-200 bg-white p-0.5">
                        <span 
                            class="px-3 py-1.5 text-xs font-medium bg-zinc-900 text-white rounded-md transition select-none cursor-default"
                        >
                            List View
                        </span>
                        <Link 
                            :href="route('invoices.board')" 
                            class="px-3 py-1.5 text-xs font-medium text-zinc-500 hover:text-zinc-900 rounded-md transition"
                        >
                            Board View
                        </Link>
                    </div>

                    <Link 
                        :href="route('invoices.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-xs text-white hover:bg-blue-700 active:bg-blue-800 focus:outline-none transition duration-150 ease-in-out shadow-none"
                    >
                        <svg class="h-4 w-4 mr-1.5 -ml-0.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                    <div class="bg-emerald-50/60 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg shadow-none text-sm font-medium flex items-center space-x-2">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ flashMessage }}</span>
                    </div>
                </div>
    
                <!-- Invoicing Statistics Panels -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- panel 1: Total Invoiced -->
                    <div class="bg-white p-6 rounded-lg border border-zinc-200 flex items-center space-x-4 shadow-none">
                        <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-650">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider">Total Invoiced</div>
                            <div class="text-lg font-semibold text-zinc-900 mt-0.5">{{ formatCurrency(totalInvoiced) }}</div>
                        </div>
                    </div>

                    <!-- panel 2: Paid -->
                    <div class="bg-white p-6 rounded-lg border border-zinc-200 flex items-center space-x-4 shadow-none">
                        <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-650">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider">Paid Amount</div>
                            <div class="text-lg font-semibold text-zinc-900 mt-0.5">{{ formatCurrency(totalPaid) }}</div>
                        </div>
                    </div>

                    <!-- panel 3: Unpaid -->
                    <div class="bg-white p-6 rounded-lg border border-zinc-200 flex items-center space-x-4 shadow-none">
                        <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-650">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider">Unpaid Amount</div>
                            <div class="text-lg font-semibold text-zinc-900 mt-0.5">{{ formatCurrency(totalUnpaid) }}</div>
                        </div>
                    </div>

                    <!-- panel 4: Overdue -->
                    <div class="bg-white p-6 rounded-lg border border-zinc-200 flex items-center space-x-4 shadow-none">
                        <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-650">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider">Overdue Balance</div>
                            <div class="text-lg font-semibold text-zinc-900 mt-0.5">{{ formatCurrency(totalOverdue) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Filters & Search -->
                <div class="bg-white p-5 rounded-lg border border-zinc-200 flex flex-col md:flex-row gap-4 justify-between items-center shadow-none">
                    <div class="relative w-full md:max-w-md">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input 
                            v-model="searchQuery" 
                            type="text" 
                            placeholder="Search by invoice number or client..." 
                            class="pl-9 pr-4 py-2 w-full bg-white hover:bg-zinc-50/50 focus:bg-white text-sm text-zinc-900 border border-zinc-200 rounded-lg focus:ring-1 focus:ring-blue-600 focus:border-blue-600 transition duration-150"
                        />
                    </div>
                    
                    <div class="flex items-center space-x-2 w-full md:w-auto">
                        <span class="text-xs text-zinc-400 font-medium shrink-0">Filter Status:</span>
                        <select 
                            v-model="statusFilter"
                            class="py-2 pl-3 pr-8 w-full md:w-40 bg-white border border-zinc-200 rounded-lg text-xs font-medium focus:bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600 text-zinc-700 transition"
                        >
                            <option value="all">All Invoices</option>
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden shadow-none">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-zinc-50 border-b border-zinc-200 text-zinc-500 text-xs uppercase font-medium tracking-wider">
                                    <th class="p-4 pl-6 font-semibold">Invoice details</th>
                                    <th class="p-4 font-semibold">Client Name</th>
                                    <th class="p-4 font-semibold">Date Created</th>
                                    <th class="p-4 font-semibold">Amount Due</th>
                                    <th class="p-4 font-semibold">Status</th>
                                    <th class="p-4 pr-6 text-right font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 text-sm text-zinc-650">
                                <tr 
                                    v-for="invoice in filteredInvoices" 
                                    :key="invoice.id" 
                                    class="hover:bg-zinc-50/30 transition duration-150"
                                >
                                    <!-- Serial Number -->
                                    <td class="p-4 pl-6">
                                        <div class="flex items-center space-x-2.5">
                                            <div class="p-2 bg-zinc-50 rounded-lg text-zinc-600 border border-zinc-200">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <span class="font-medium text-zinc-900 tracking-tight">{{ invoice.invoice_number }}</span>
                                        </div>
                                    </td>

                                    <!-- Client name -->
                                    <td class="p-4">
                                        <div v-if="invoice.customer" class="flex items-center space-x-2">
                                            <div class="h-6 w-6 rounded bg-zinc-100 border border-zinc-200 flex items-center justify-center font-semibold text-[10px] text-zinc-700">
                                                {{ invoice.customer.name.substring(0,2).toUpperCase() }}
                                            </div>
                                            <span class="font-medium text-zinc-700">{{ invoice.customer.name }}</span>
                                        </div>
                                        <span v-else class="text-zinc-400 italic text-xs">Customer deleted</span>
                                    </td>

                                    <!-- Created At -->
                                    <td class="p-4 text-xs font-medium text-zinc-500">
                                        {{ formatDate(invoice.created_at) }}
                                    </td>

                                    <!-- Amount -->
                                    <td class="p-4 font-medium text-zinc-900 text-sm">
                                        {{ formatCurrency(invoice.amount) }}
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="p-4">
                                        <span 
                                            v-if="invoice.status === 'paid'"
                                            class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-emerald-50/60 text-emerald-700 border border-emerald-200/50 shadow-none"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Paid
                                        </span>
                                        <span 
                                            v-else-if="invoice.status === 'unpaid'"
                                            class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-amber-50/60 text-amber-700 border border-amber-200/50 shadow-none"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                            Unpaid
                                        </span>
                                        <span 
                                            v-else
                                            class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-rose-50/60 text-rose-700 border border-rose-200/50 shadow-none"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                            Overdue
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-4 pr-6 text-right">

                                        <div class="inline-flex items-center space-x-1.5">
                                            <!-- Status Dropdown -->
                                            <Dropdown v-if="invoice.status !== 'paid'" align="right" width="48">
                                                <template #trigger>
                                                    <button class="p-1.5 hover:bg-zinc-100 rounded-lg transition duration-150 focus:outline-none flex items-center space-x-1" title="Change Status">
                                                        <span :class="['h-2 w-2 rounded-full', invoice.status === 'paid' ? 'bg-emerald-500' : (invoice.status === 'unpaid' ? 'bg-amber-500' : 'bg-rose-500')]"></span>
                                                        <svg class="h-3 w-3 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>
                                                </template>
                                                <template #content>
                                                    <div class="px-3 py-1.5 text-[9px] font-semibold text-zinc-400 uppercase tracking-wider">Change Status</div>
                                                    <button 
                                                        @click="setStatus(invoice, 'paid')"
                                                        class="flex items-center w-full px-4 py-2 text-left text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition duration-150"
                                                        :class="{ 'bg-zinc-50/50 text-emerald-600': invoice.status === 'paid' }"
                                                    >
                                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-2"></span>
                                                        Paid
                                                    </button>
                                                    <button 
                                                        @click="setStatus(invoice, 'unpaid')"
                                                        class="flex items-center w-full px-4 py-2 text-left text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition duration-150"
                                                        :class="{ 'bg-zinc-50/50 text-amber-600': invoice.status === 'unpaid' }"
                                                    >
                                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500 mr-2"></span>
                                                        Unpaid
                                                    </button>
                                                    <button 
                                                        @click="setStatus(invoice, 'overdue')"
                                                        class="flex items-center w-full px-4 py-2 text-left text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition duration-150"
                                                        :class="{ 'bg-zinc-50/50 text-rose-600': invoice.status === 'overdue' }"
                                                    >
                                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500 mr-2"></span>
                                                        Overdue
                                                    </button>
                                                </template>
                                            </Dropdown>

                                            <Link 
                                                v-if="invoice.status !== 'paid'"
                                                :href="route('invoices.edit', invoice.id)"
                                                class="inline-flex items-center p-1.5 text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 rounded-lg transition duration-150 focus:outline-none"
                                                title="Edit Invoice"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </Link>
                                            <button 
                                                v-if="invoice.status !== 'paid'"
                                                @click="emailInvoiceToClient(invoice.id)" 
                                                class="inline-flex items-center p-1.5 text-zinc-400 hover:text-blue-600 hover:bg-blue-50/50 rounded-lg transition duration-150 focus:outline-none"
                                                title="Email Invoice to Client"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                            <!-- WhatsApp Button -->
                                            <button 
                                                v-if="invoice.status !== 'paid' && invoice.customer && invoice.customer.phone"
                                                @click="sendWhatsApp(invoice)" 
                                                class="inline-flex items-center p-1.5 text-zinc-400 hover:text-green-600 hover:bg-green-50/50 rounded-lg transition duration-150 focus:outline-none"
                                                title="Send Invoice via WhatsApp"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                                </svg>
                                            </button>
                                            <button 
                                                @click="deleteInvoice(invoice.id)" 
                                                class="p-1.5 text-zinc-400 hover:text-red-650 hover:bg-red-50/60 rounded-lg transition duration-150 focus:outline-none"
                                                title="Delete Invoice"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="filteredInvoices.length === 0">
                                    <td colspan="6" class="p-12 text-center text-zinc-400">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <svg class="h-8 w-8 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="font-medium text-zinc-700 text-sm">No invoices found</p>
                                            <p class="text-xs text-zinc-450">Adjust your search parameters or log a new invoice to get started.</p>
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
