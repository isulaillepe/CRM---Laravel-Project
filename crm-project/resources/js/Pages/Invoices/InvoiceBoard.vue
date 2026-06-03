<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    invoices: {
        type: Array,
        required: true,
    }
});

// Local state for search and optimistic rendering
const searchQuery = ref('');
const localInvoices = ref([...props.invoices]);

watch(() => props.invoices, (newVal) => {
    localInvoices.value = [...newVal];
}, { deep: true });

// Define lanes for Invoices
const columns = [
    { id: 'unpaid', title: 'Unpaid', badgeColor: 'bg-amber-50 text-amber-700 border-amber-100' },
    { id: 'paid', title: 'Paid', badgeColor: 'bg-emerald-50 text-emerald-700 border-emerald-100' },
    { id: 'overdue', title: 'Overdue', badgeColor: 'bg-rose-50 text-rose-700 border-rose-100' }
];

// Helper to format currency
const formatCurrency = (val) => {
    return 'Rs. ' + parseFloat(val || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

// Formatting date helper
const formatDate = (dateStr) => {
    if (!dateStr) return 'N/A';
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

// Filtered invoices based on search
const filteredInvoices = computed(() => {
    if (!searchQuery.value) return localInvoices.value;
    const query = searchQuery.value.toLowerCase();
    return localInvoices.value.filter(inv => 
        inv.invoice_number.toLowerCase().includes(query) ||
        (inv.customer && inv.customer.name.toLowerCase().includes(query))
    );
});

// Stats calculations (identical to Invoices/Index.vue)
const totalInvoiced = computed(() => {
    return localInvoices.value.reduce((sum, inv) => sum + parseFloat(inv.amount || 0), 0);
});

const totalPaid = computed(() => {
    return localInvoices.value
        .filter(inv => inv.status === 'paid')
        .reduce((sum, inv) => sum + parseFloat(inv.amount || 0), 0);
});

const totalUnpaid = computed(() => {
    return localInvoices.value
        .filter(inv => inv.status === 'unpaid')
        .reduce((sum, inv) => sum + parseFloat(inv.amount || 0), 0);
});

const totalOverdue = computed(() => {
    return localInvoices.value
        .filter(inv => inv.status === 'overdue')
        .reduce((sum, inv) => sum + parseFloat(inv.amount || 0), 0);
});

// Group invoices by status
const invoicesByStatus = computed(() => {
    const groups = {
        unpaid: [],
        paid: [],
        overdue: []
    };
    filteredInvoices.value.forEach(inv => {
        if (groups[inv.status]) {
            groups[inv.status].push(inv);
        } else {
            groups.unpaid.push(inv);
        }
    });
    return groups;
});

// Calculate statistics per column (for header sums)
const columnStats = computed(() => {
    const stats = {};
    columns.forEach(col => {
        const list = invoicesByStatus.value[col.id] || [];
        const count = list.length;
        const totalValue = list.reduce((sum, inv) => sum + parseFloat(inv.amount || 0), 0);
        stats[col.id] = { count, totalValue };
    });
    return stats;
});

// Drag & Drop State
const draggedInvoiceId = ref(null);
const activeDropTarget = ref(null);

const onDragStart = (event, invoice) => {
    if (invoice.status === 'paid') {
        event.preventDefault();
        return;
    }
    draggedInvoiceId.value = invoice.id;
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', invoice.id);
    event.currentTarget.classList.add('opacity-40');
};

const onDragEnd = (event) => {
    event.currentTarget.classList.remove('opacity-40');
    draggedInvoiceId.value = null;
    activeDropTarget.value = null;
};

const onDragOver = (event, columnId) => {
    event.preventDefault();
    activeDropTarget.value = columnId;
};

const onDragLeave = () => {
    // Left target
};

const onDrop = (event, targetStatus) => {
    event.preventDefault();
    activeDropTarget.value = null;
    
    const invoiceId = draggedInvoiceId.value || parseInt(event.dataTransfer.getData('text/plain'), 10);
    if (!invoiceId) return;

    const invoice = localInvoices.value.find(inv => inv.id === invoiceId);
    if (invoice && invoice.status !== 'paid' && invoice.status !== targetStatus) {
        updateInvoiceStatus(invoice, targetStatus);
    }
};

// Update invoice status via Inertia request
const updatingId = ref(null);

const updateInvoiceStatus = (invoice, newStatus) => {
    updatingId.value = invoice.id;
    const oldStatus = invoice.status;
    
    // Optimistic local state update
    invoice.status = newStatus;

    router.patch(route('invoices.update', invoice.id) + '?redirect_to=board', {
        customer_id: invoice.customer_id,
        invoice_number: invoice.invoice_number,
        amount: invoice.amount,
        status: newStatus,
        due_date: invoice.due_date
    }, {
        preserveScroll: true,
        onSuccess: () => {
            updatingId.value = null;
        },
        onError: (err) => {
            updatingId.value = null;
            // Revert state
            invoice.status = oldStatus;
            console.error('Failed to update invoice status:', err);
        }
    });
};

// Mail helper
const emailInvoiceToClient = (invoiceId) => {
    router.post(`/invoices/${invoiceId}/send`, {}, {
        onStart: () => alert('Contacting Stripe and dispatching email package...'),
        onSuccess: () => alert('Success! Check your Mailtrap dashboard.'),
        onError: (err) => console.error(err)
    });
};

const page = usePage();
const flashMessage = ref(null);
watch(() => page.props.flash?.success, (newSuccess) => {
    if (newSuccess) {
        flashMessage.value = newSuccess;
        setTimeout(() => {
            flashMessage.value = null;
        }, 5000);
    }
}, { immediate: true });
</script>

<template>
    <Head title="Billing Board" />

    <AuthenticatedLayout>
        <!-- Stark white, clean header with thin borders -->
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-2xl font-semibold text-zinc-900 tracking-tight">Billing & Invoices Board</h2>
                    <p class="text-sm text-zinc-500 mt-1">Stark visual overview of billing statuses, outstanding balances, and collections.</p>
                </div>
                
                <div class="flex items-center space-x-3">
                    <!-- Minimalist Toggle Control -->
                    <div class="inline-flex rounded-lg border border-zinc-200 bg-white p-0.5">
                        <Link 
                            :href="route('invoices.index')" 
                            class="px-3 py-1.5 text-xs font-medium text-zinc-500 hover:text-zinc-900 rounded-md transition"
                        >
                            List View
                        </Link>
                        <span 
                            class="px-3 py-1.5 text-xs font-medium bg-zinc-900 text-white rounded-md transition select-none cursor-default"
                        >
                            Board View
                        </span>
                    </div>

                    <!-- Single Action Accent Button -->
                    <Link 
                        :href="route('invoices.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white hover:bg-blue-700 active:bg-blue-800 focus:outline-none transition duration-150 ease-in-out shadow-sm"
                    >
                        <svg class="h-4 w-4 mr-1.5 -ml-0.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Invoice
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 bg-white min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Success notification -->
                <div v-if="flashMessage" class="mt-4">
                    <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 px-4 py-3 rounded-lg shadow-sm text-xs font-semibold flex items-center space-x-2">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ flashMessage }}</span>
                    </div>
                </div>

                <!-- Invoicing Statistics Panels (identical to Index.vue, styled cleanly) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- panel 1: Total Invoiced -->
                    <div class="bg-white p-6 rounded-xl border border-zinc-200 flex items-center space-x-4">
                        <div class="p-3 bg-zinc-50 rounded-lg text-zinc-900 border border-zinc-100">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider">Total Invoiced</div>
                            <div class="text-lg font-bold text-zinc-900 mt-0.5">{{ formatCurrency(totalInvoiced) }}</div>
                        </div>
                    </div>

                    <!-- panel 2: Paid -->
                    <div class="bg-white p-6 rounded-xl border border-zinc-200 flex items-center space-x-4">
                        <div class="p-3 bg-zinc-50 rounded-lg text-zinc-900 border border-zinc-100">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider">Paid Amount</div>
                            <div class="text-lg font-bold text-zinc-900 mt-0.5">{{ formatCurrency(totalPaid) }}</div>
                        </div>
                    </div>

                    <!-- panel 3: Unpaid -->
                    <div class="bg-white p-6 rounded-xl border border-zinc-200 flex items-center space-x-4">
                        <div class="p-3 bg-zinc-50 rounded-lg text-zinc-900 border border-zinc-100">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider">Unpaid Amount</div>
                            <div class="text-lg font-bold text-zinc-900 mt-0.5">{{ formatCurrency(totalUnpaid) }}</div>
                        </div>
                    </div>

                    <!-- panel 4: Overdue -->
                    <div class="bg-white p-6 rounded-xl border border-zinc-200 flex items-center space-x-4">
                        <div class="p-3 bg-zinc-50 rounded-lg text-zinc-900 border border-zinc-100">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider">Overdue Balance</div>
                            <div class="text-lg font-bold text-zinc-900 mt-0.5">{{ formatCurrency(totalOverdue) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Filters & Search Bar -->
                <div class="flex flex-wrap items-center justify-between border border-zinc-200 bg-white p-5 rounded-xl gap-4">
                    <div class="text-sm font-semibold text-zinc-900">Workspace Pipeline</div>
                    
                    <div class="relative w-full md:max-w-xs">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input 
                            v-model="searchQuery" 
                            type="text" 
                            placeholder="Filter board invoices..." 
                            class="pl-9 pr-4 py-2 w-full bg-white hover:bg-zinc-50 focus:bg-white text-xs text-zinc-900 border border-zinc-200 rounded-lg focus:ring-2 focus:ring-zinc-900 focus:border-zinc-900 transition duration-150"
                        />
                    </div>
                </div>

                <!-- Kanban Board Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                    
                    <!-- Status Lane -->
                    <div 
                        v-for="col in columns" 
                        :key="col.id"
                        class="flex flex-col rounded-xl border transition-all duration-200"
                        :class="[
                            activeDropTarget === col.id 
                                ? 'border-blue-600 bg-zinc-50/50' 
                                : 'border-zinc-200 bg-white'
                        ]"
                        @dragover="onDragOver($event, col.id)"
                        @dragleave="onDragLeave"
                        @drop="onDrop($event, col.id)"
                    >
                        <!-- Column Header -->
                        <div class="p-4 border-b border-zinc-100 flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-semibold text-zinc-900 tracking-tight">{{ col.title }}</span>
                                <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full border border-zinc-200 text-zinc-500 bg-zinc-50">
                                    {{ columnStats[col.id]?.count || 0 }}
                                </span>
                            </div>
                            <span class="text-xs font-semibold text-zinc-500">
                                {{ formatCurrency(columnStats[col.id]?.totalValue || 0) }}
                            </span>
                        </div>

                        <!-- Dropzone & Card List -->
                        <div class="p-3 space-y-3 min-h-[500px] flex flex-col">
                            
                            <!-- Invoice Card -->
                            <div 
                                v-for="invoice in invoicesByStatus[col.id]"
                                :key="invoice.id"
                                :draggable="invoice.status !== 'paid'"
                                @dragstart="onDragStart($event, invoice)"
                                @dragend="onDragEnd"
                                class="bg-white border border-zinc-200 rounded-lg p-4 transition-all duration-150 relative group"
                                :class="[
                                    invoice.status === 'paid' 
                                        ? 'cursor-default' 
                                        : 'hover:border-zinc-900 cursor-grab active:cursor-grabbing'
                                ]"
                            >
                                <!-- Header: Invoice Number & Edit Link -->
                                <div class="flex justify-between items-start gap-2">
                                    <div class="flex items-center space-x-1.5">
                                        <div class="p-1 bg-zinc-50 rounded text-zinc-600 border border-zinc-200">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <span class="text-xs font-bold text-zinc-900 tracking-tight">{{ invoice.invoice_number }}</span>
                                    </div>
                                    
                                    <!-- Action Link -->
                                    <div class="flex items-center space-x-1">
                                        <button 
                                            v-if="invoice.status !== 'paid'"
                                            @click="emailInvoiceToClient(invoice.id)" 
                                            class="p-1 text-zinc-400 hover:text-blue-600 hover:bg-zinc-50 rounded transition duration-150"
                                            title="Email Invoice"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                        <Link 
                                            v-if="invoice.status !== 'paid'"
                                            :href="route('invoices.edit', invoice.id) + '?from=board'"
                                            class="text-[10px] font-medium text-zinc-450 hover:text-blue-600 px-1 transition duration-150"
                                        >
                                            Edit
                                        </Link>
                                    </div>
                                </div>

                                <!-- Invoice due date -->
                                <div class="mt-2 text-[10px] text-zinc-400">
                                    Due: {{ formatDate(invoice.due_date) }}
                                </div>

                                <!-- Customer Details -->
                                <div v-if="invoice.customer" class="flex items-center mt-3 pt-3 border-t border-zinc-100">
                                    <div class="h-5 w-5 rounded-full bg-zinc-100 border border-zinc-200 flex items-center justify-center font-bold text-[8px] text-zinc-500 shrink-0">
                                        {{ invoice.customer.name.substring(0,2).toUpperCase() }}
                                    </div>
                                    <span class="text-[10px] font-medium text-zinc-600 ml-1.5 truncate">
                                        {{ invoice.customer.name }}
                                    </span>
                                </div>

                                <!-- Card Bottom -->
                                <div class="flex items-center justify-between mt-3 pt-2">
                                    <span class="text-xs font-bold text-zinc-950">
                                        {{ formatCurrency(invoice.amount) }}
                                    </span>
                                    
                                    <!-- Select dropdown (disabled for paid status) -->
                                    <div class="relative">
                                        <select 
                                            :value="invoice.status" 
                                            :disabled="invoice.status === 'paid'"
                                            @change="updateInvoiceStatus(invoice, $event.target.value)"
                                            class="appearance-none bg-zinc-50 hover:bg-zinc-100/70 border border-zinc-200 rounded px-1.5 py-0.5 pr-4 text-[9px] font-semibold text-zinc-505 focus:outline-none transition"
                                            :class="[
                                                invoice.status === 'paid' 
                                                    ? 'cursor-not-allowed opacity-75 text-zinc-400 bg-zinc-100' 
                                                    : 'cursor-pointer text-zinc-500'
                                            ]"
                                        >
                                            <option value="unpaid">Unpaid</option>
                                            <option value="paid">Paid</option>
                                            <option value="overdue">Overdue</option>
                                        </select>
                                        <span 
                                            v-if="invoice.status !== 'paid'"
                                            class="absolute inset-y-0 right-1 flex items-center pointer-events-none text-zinc-400 text-[7px]"
                                        >
                                            ▼
                                        </span>
                                    </div>
                                </div>

                                <!-- Loading overlay -->
                                <div 
                                    v-if="updatingId === invoice.id" 
                                    class="absolute inset-0 bg-white/70 flex items-center justify-center rounded-lg"
                                >
                                    <svg class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Empty Column State placeholder -->
                            <div 
                                v-if="invoicesByStatus[col.id]?.length === 0" 
                                class="flex-grow flex flex-col items-center justify-center border border-dashed border-zinc-200 rounded-lg p-6 text-center select-none"
                            >
                                <span class="text-[10px] font-medium text-zinc-300">No invoices</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
