<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InvoiceStatusChart from '@/Components/InvoiceStatusChart.vue';
import InvoiceTrendRow from '@/Components/InvoiceTrendRow.vue';
import ChartActivityLine from '@/Components/ChartActivityLine.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    totalInvoiced: {
        type: [Number, String],
        default: 0
    },
    totalPaid: {
        type: [Number, String],
        default: 0
    },
    totalUnpaid: {
        type: [Number, String],
        default: 0
    },
    totalOverdue: {
        type: [Number, String],
        default: 0
    },
    paidCount: {
        type: Number,
        default: 0,
    },
    unpaidCount: {
        type: Number,
        default: 0,
    },
    overdueCount: {
        type: Number,
        default: 0,
    }
});

const formatCurrency = (val) => {
    return 'Rs. ' + parseFloat(val || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-2xl text-zinc-900 tracking-tight">Dashboard</h2>
            <p class="text-xs text-zinc-500 mt-1">Summary of the CRM logistical operations and billing milestones.</p>
        </template>

        <div class="py-8 bg-white min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                <!-- Invoicing Statistics Panels -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- panel 1: Total Invoiced -->
                    <div class="bg-white p-6 rounded-xl border border-zinc-200 flex items-center space-x-4">
                        <div class="p-3 bg-zinc-50 rounded-lg text-zinc-900 border border-zinc-100 shrink-0">
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
                        <div class="p-3 bg-zinc-50 rounded-lg text-zinc-900 border border-zinc-100 shrink-0">
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
                        <div class="p-3 bg-zinc-50 rounded-lg text-zinc-900 border border-zinc-100 shrink-0">
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
                        <div class="p-3 bg-zinc-50 rounded-lg text-zinc-900 border border-zinc-100 shrink-0">
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

                <!-- Chart + Info Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Invoice Status Doughnut Chart -->
                    <InvoiceStatusChart
                        :paidCount="paidCount"
                        :unpaidCount="unpaidCount"
                        :overdueCount="overdueCount"
                    />

                    <!-- Main Greeting Block -->
                    <div class="lg:col-span-2 border border-zinc-200 rounded-xl bg-white p-8 flex flex-col justify-center">
                        <h3 class="text-zinc-900 font-medium text-base tracking-tight">System Operational</h3>
                        <p class="text-zinc-500 text-xs mt-1.5 leading-relaxed">
                            Welcome to Central Distributors CRM. All data channels and automated pipelines are fully operational.
                            Navigate to the Customers, Proposals, or Invoices modules from the navigation bar to manage your logistical pipeline.
                        </p>
                        <div class="flex flex-wrap gap-3 mt-5">
                            <!-- Create Customer Button -->
                            <Link :href="route('customers.create')" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-zinc-200 hover:border-zinc-300 rounded-lg text-xs font-semibold text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50 transition duration-150 ease-in-out focus:outline-none">
                                <svg class="h-4 w-4 mr-2 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                New Customer
                            </Link>

                            <!-- Create Proposal Button -->
                            <Link :href="route('proposals.create')" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-zinc-200 hover:border-zinc-300 rounded-lg text-xs font-semibold text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50 transition duration-150 ease-in-out focus:outline-none">
                                <svg class="h-4 w-4 mr-2 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                New Proposal
                            </Link>

                            <!-- Create Invoice Button -->
                            <Link :href="route('invoices.create')" class="inline-flex items-center justify-center px-4 py-2.5 bg-zinc-900 border border-transparent hover:bg-zinc-850 rounded-lg text-xs font-semibold text-white transition duration-150 ease-in-out focus:outline-none">
                                <svg class="h-4 w-4 mr-2 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                New Invoice
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Line Chart Activity -->
                <ChartActivityLine />

                <!-- Core Financial Trends -->
                <div class="space-y-4">
                    <h3 class="text-base font-semibold text-zinc-900 tracking-tight">Core Financial Trends</h3>
                    <p class="text-xs text-zinc-400 mt-0.5">Sparkline visual trends for invoice segments</p>
                    <div class="flex flex-col gap-4">
                        <InvoiceTrendRow
                            invoiceId="TOTAL"
                            clientName="Total Invoiced"
                            :amountDue="parseFloat(totalInvoiced)"
                            status="unpaid"
                            :showActions="false"
                            :minimalist="true"
                            :dateCreated="new Date().toISOString()"
                        />
                        <InvoiceTrendRow
                            invoiceId="PAID"
                            clientName="Total Amount Paid"
                            :amountDue="parseFloat(totalPaid)"
                            status="paid"
                            :showActions="false"
                            :minimalist="true"
                            :dateCreated="new Date().toISOString()"
                        />
                        <InvoiceTrendRow
                            invoiceId="UNPAID"
                            clientName="Total Amount Unpaid"
                            :amountDue="parseFloat(totalUnpaid)"
                            status="overdue"
                            :showActions="false"
                            :minimalist="true"
                            :dateCreated="new Date().toISOString()"
                        />
                    </div>
                </div>
                
            </div>
        </div>
    </AuthenticatedLayout>
</template>
