<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    transactions: {
        type: Array,
        required: true
    }
});

const searchQuery = ref('');

// Dynamic transactional data search filter logic
const filteredTransactions = computed(() => {
    return props.transactions.filter(tx => {
        const matchesInvoice = tx.invoice?.invoice_number.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesCustomer = tx.invoice?.customer?.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesStripeId = tx.stripe_session_id.toLowerCase().includes(searchQuery.value.toLowerCase());
        
        return matchesInvoice || matchesCustomer || matchesStripeId;
    });
});

// Format helpers
const formatCurrency = (val) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'LKR' }).format(val);
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Flash message support
const page = usePage();
const flashMessage = ref(null);

watch(() => page.props.flash?.success, (newSuccess) => {
    if (newSuccess) {
        flashMessage.value = newSuccess;
        setTimeout(() => { flashMessage.value = null; }, 5000);
    }
}, { immediate: true });

// Manual refresh for live data
const refreshLedger = () => {
    router.reload({ only: ['transactions'] });
};
</script>

<template>
    <Head title="Transactions Ledger" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div>
                    <h2 class="font-semibold text-xl text-zinc-900 tracking-tight">Transactions Ledger</h2>
                    <p class="text-sm text-zinc-500 mt-1">Review payments and raw audit log histories.</p>
                </div>
                <button 
                    @click="refreshLedger"
                    class="inline-flex items-center px-4 py-2 border border-zinc-200 rounded-lg text-xs font-medium text-zinc-700 bg-white hover:bg-zinc-50 focus:outline-none focus:ring-1 focus:ring-blue-600 transition duration-150 ease-in-out shadow-none"
                >
                    <svg class="h-4 w-4 mr-1.5 -ml-0.5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh Ledger
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <div v-if="flashMessage" class="bg-emerald-50/60 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg shadow-none text-sm font-medium flex items-center space-x-2">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ flashMessage }}</span>
                </div>

                <div class="bg-white p-5 rounded-lg border border-zinc-200 shadow-none">
                    <div class="relative max-w-md">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input 
                            v-model="searchQuery" 
                            type="text" 
                            placeholder="Search by Invoice, Client name, or Stripe Session ID..." 
                            class="pl-9 pr-4 py-2 w-full bg-white hover:bg-zinc-50/50 focus:bg-white text-sm text-zinc-900 border border-zinc-200 rounded-lg focus:ring-1 focus:ring-blue-600 focus:border-blue-600 transition duration-150"
                        />
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden shadow-none">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-zinc-50 border-b border-zinc-200 text-zinc-500 text-xs uppercase font-medium tracking-wider">
                                    <th class="p-4 pl-6 font-semibold">Transaction Log ID</th>
                                    <th class="p-4 font-semibold">Invoice Reference</th>
                                    <th class="p-4 font-semibold">Client Name</th>
                                    <th class="p-4 font-semibold">Stripe Gateway ID</th>
                                    <th class="p-4 font-semibold">Settle Timestamp</th>
                                    <th class="p-4 pr-6 text-right font-semibold">Captured Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 text-sm text-zinc-650">
                                <tr 
                                    v-for="tx in filteredTransactions" 
                                    :key="tx.id" 
                                    class="hover:bg-zinc-50/30 transition duration-150"
                                >
                                    <td class="p-4 pl-6 font-mono text-xs text-zinc-400">
                                        #TX-LOG-{{ String(tx.id).padStart(4, '0') }}
                                    </td>

                                    <td class="p-4 font-medium text-zinc-900">
                                        {{ tx.invoice ? tx.invoice.invoice_number : 'N/A' }}
                                    </td>

                                    <td class="p-4">
                                        <div v-if="tx.invoice && tx.invoice.customer" class="flex items-center space-x-2">
                                            <div class="h-6 w-6 rounded bg-zinc-100 border border-zinc-200 flex items-center justify-center font-semibold text-[10px] text-zinc-700">
                                                {{ tx.invoice.customer.name.substring(0,2).toUpperCase() }}
                                            </div>
                                            <span class="font-medium text-zinc-700">{{ tx.invoice.customer.name }}</span>
                                        </div>
                                        <span v-else class="text-zinc-400 italic text-xs">Unlinked / Deleted client</span>
                                    </td>

                                    <td class="p-4 font-mono text-xs text-blue-600 tracking-tight select-all animate-none" :title="tx.stripe_session_id">
                                        {{ tx.stripe_session_id.substring(0, 18) }}...
                                    </td>

                                    <td class="p-4 text-xs font-medium text-zinc-500">
                                        {{ formatDate(tx.created_at) }}
                                    </td>

                                    <td class="p-4 pr-6 text-right font-medium text-emerald-700 text-sm font-mono">
                                        +{{ formatCurrency(tx.amount_paid) }}
                                    </td>
                                </tr>

                                <tr v-if="filteredTransactions.length === 0">
                                    <td colspan="6" class="p-12 text-center text-zinc-400">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <svg class="h-8 w-8 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="font-medium text-zinc-700 text-sm">No transactions recorded</p>
                                            <p class="text-xs text-zinc-450">When customers pay statements online via their sent email links, those logs materialize here.</p>
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