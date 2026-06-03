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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        

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


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">You're logged in!</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
