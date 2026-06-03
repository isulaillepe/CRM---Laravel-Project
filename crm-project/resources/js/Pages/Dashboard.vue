<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

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
            <p class="text-xs text-zinc-500 mt-1">Stark summary of the CRM logistical operations and billing milestones.</p>
        </template>

        <div class="py-8 bg-white min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

                <!-- Main Greeting Block -->
                <div class="border border-zinc-200 rounded-xl bg-white p-8">
                    <h3 class="text-zinc-900 font-medium text-base tracking-tight">System Operational</h3>
                    <p class="text-zinc-500 text-xs mt-1.5 leading-relaxed">
                        Welcome to Central Distributors CRM. All data channels and automated pipelines are fully operational.
                        Navigate to the Customers, Proposals, or Invoices modules from the navigation bar to manage your logistical pipeline.
                    </p>
                </div>
                
            </div>
        </div>
    </AuthenticatedLayout>
</template>
