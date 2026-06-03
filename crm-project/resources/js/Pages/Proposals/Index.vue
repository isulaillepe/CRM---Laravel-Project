<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    proposals: {
        type: Array,
        required: true,
    }
});

// Search and Filter state
const searchQuery = ref('');
const statusFilter = ref('all');

// Stats calculations
const totalPipelineValue = computed(() => {
    return props.proposals.reduce((sum, p) => sum + parseFloat(p.value || 0), 0);
});

const activeDealsCount = computed(() => {
    return props.proposals.filter(p => p.status === 'sent').length;
});

const wonDealsCount = computed(() => {
    return props.proposals.filter(p => p.status === 'accepted').length;
});

const conversionRate = computed(() => {
    if (props.proposals.length === 0) return 0;
    const won = props.proposals.filter(p => p.status === 'accepted').length;
    return Math.round((won / props.proposals.length) * 100);
});

// Filtering logic
const filteredProposals = computed(() => {
    return props.proposals.filter(p => {
        const matchesSearch = 
            p.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            p.description.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (p.customer && p.customer.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
        
        const matchesStatus = 
            statusFilter.value === 'all' || 
            p.status === statusFilter.value;
        
        return matchesSearch && matchesStatus;
    });
});

// Delete Proposal handler
const deleteProposal = (id) => {
    if (confirm('Are you sure you want to delete this proposal?')) {
        useForm({}).delete(route('proposals.destroy', id));
    }
};

// Formatter Helpers
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
</script>

<template>
    <Head title="Proposals Pipeline" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div>
                    <h2 class="font-semibold text-xl text-zinc-900 tracking-tight">Proposals Pipeline</h2>
                    <p class="text-sm text-zinc-500 mt-1">Review pipeline value, deal conversions, and dispatch quotes to clients.</p>
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
                            :href="route('proposals.board')" 
                            class="px-3 py-1.5 text-xs font-medium text-zinc-500 hover:text-zinc-900 rounded-md transition"
                        >
                            Board View
                        </Link>
                    </div>

                    <Link 
                        :href="route('proposals.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-xs text-white hover:bg-blue-700 active:bg-blue-800 focus:outline-none transition duration-150 ease-in-out shadow-none"
                    >
                        <svg class="h-4 w-4 mr-1.5 -ml-0.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Proposal
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Pipeline Stats Dashboard Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Stat 1: Total Valuation -->
                    <div class="bg-white p-6 rounded-lg border border-zinc-200 flex items-center space-x-4 shadow-none">
                        <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider">Pipeline Valuation</div>
                            <div class="text-lg font-semibold text-zinc-900 mt-0.5">{{ formatCurrency(totalPipelineValue) }}</div>
                        </div>
                    </div>

                    <!-- Stat 2: Sent/Active -->
                    <div class="bg-white p-6 rounded-lg border border-zinc-200 flex items-center space-x-4 shadow-none">
                        <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.333a2 2 0 012.22 0l8 5.333A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-5.625-3.75" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider">Active Sent Deals</div>
                            <div class="text-lg font-semibold text-zinc-900 mt-0.5">{{ activeDealsCount }}</div>
                        </div>
                    </div>

                    <!-- Stat 3: Won -->
                    <div class="bg-white p-6 rounded-lg border border-zinc-200 flex items-center space-x-4 shadow-none">
                        <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider">Accepted Deals</div>
                            <div class="text-lg font-semibold text-zinc-900 mt-0.5">{{ wonDealsCount }}</div>
                        </div>
                    </div>

                    <!-- Stat 4: Conversion Rate -->
                    <div class="bg-white p-6 rounded-lg border border-zinc-200 flex items-center space-x-4 shadow-none">
                        <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider">Win Ratio</div>
                            <div class="text-lg font-semibold text-zinc-900 mt-0.5">{{ conversionRate }}%</div>
                        </div>
                    </div>
                </div>

                <!-- Filters & Search Bar -->
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
                            placeholder="Search proposals, details or clients..." 
                            class="pl-9 pr-4 py-2 w-full bg-white hover:bg-zinc-50/50 focus:bg-white text-sm text-zinc-900 border border-zinc-200 rounded-lg focus:ring-1 focus:ring-blue-600 focus:border-blue-600 transition duration-150"
                        />
                    </div>
                    
                    <div class="flex items-center space-x-2 w-full md:w-auto">
                        <span class="text-xs text-zinc-400 font-medium shrink-0">Filter Status:</span>
                        <select 
                            v-model="statusFilter"
                            class="py-2 pl-3 pr-8 w-full md:w-40 bg-white border border-zinc-200 rounded-lg text-xs font-medium focus:bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600 text-zinc-700 transition"
                        >
                            <option value="all">All Proposals</option>
                            <option value="draft">Draft</option>
                            <option value="sent">Sent</option>
                            <option value="accepted">Accepted</option>
                            <option value="declined">Declined</option>
                        </select>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden shadow-none">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-zinc-50 border-b border-zinc-200 text-zinc-500 text-xs uppercase font-medium tracking-wider">
                                    <th class="p-4 pl-6 font-semibold">Deal Details</th>
                                    <th class="p-4 font-semibold">Associated Client</th>
                                    <th class="p-4 font-semibold">Created Date</th>
                                    <th class="p-4 font-semibold">Deal Value</th>
                                    <th class="p-4 font-semibold">Status</th>
                                    <th class="p-4 pr-6 text-right font-semibold">Management</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 text-sm text-zinc-650">
                                <tr 
                                    v-for="proposal in filteredProposals" 
                                    :key="proposal.id" 
                                    class="hover:bg-zinc-50/30 transition duration-150"
                                >
                                    <!-- Proposal Title & Desc -->
                                    <td class="p-4 pl-6 max-w-sm">
                                        <div>
                                            <div class="font-medium text-zinc-950 leading-snug truncate" :title="proposal.title">
                                                {{ proposal.title }}
                                            </div>
                                            <div class="text-xs text-zinc-400 mt-1 line-clamp-1">
                                                {{ proposal.description }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Customer Profile Link -->
                                    <td class="p-4">
                                        <div v-if="proposal.customer" class="flex items-center space-x-2">
                                            <div class="h-6 w-6 rounded bg-zinc-100 border border-zinc-200 flex items-center justify-center font-semibold text-[10px] text-zinc-700">
                                                {{ proposal.customer.name.substring(0,2).toUpperCase() }}
                                            </div>
                                            <span class="font-medium text-zinc-700">{{ proposal.customer.name }}</span>
                                        </div>
                                        <span v-else class="text-zinc-400 italic text-xs">Customer deleted</span>
                                    </td>

                                    <!-- Created Date -->
                                    <td class="p-4 text-xs font-medium text-zinc-500">
                                        {{ formatDate(proposal.created_at) }}
                                    </td>

                                    <!-- Deal Value -->
                                    <td class="p-4 font-medium text-zinc-900 text-sm">
                                        {{ formatCurrency(proposal.value) }}
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="p-4">
                                        <span 
                                            v-if="proposal.status === 'accepted'"
                                            class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-emerald-50/60 text-emerald-700 border border-emerald-200/50 shadow-none"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Accepted
                                        </span>
                                        <span 
                                            v-else-if="proposal.status === 'sent'"
                                            class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-blue-50/60 text-blue-700 border border-blue-200/50 shadow-none"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                                            Sent
                                        </span>
                                        <span 
                                            v-else-if="proposal.status === 'declined'"
                                            class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-rose-50/60 text-rose-700 border border-rose-200/50 shadow-none"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                            Declined
                                        </span>
                                        <span 
                                            v-else
                                            class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-zinc-50 text-zinc-650 border border-zinc-200 shadow-none"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-zinc-400 mr-1.5"></span>
                                            Draft
                                        </span>
                                    </td>

                                    <!-- Action (Edit / Delete) -->
                                    <td class="p-4 pr-6 text-right">
                                        <div class="flex items-center justify-end space-x-1.5">
                                            <Link 
                                                :href="route('proposals.edit', proposal.id)"
                                                class="inline-flex items-center p-1.5 text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 rounded-lg transition duration-150 focus:outline-none"
                                                title="Edit Proposal"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </Link>
                                            <button 
                                                @click="deleteProposal(proposal.id)" 
                                                class="p-1.5 text-zinc-400 hover:text-red-650 hover:bg-red-50/60 rounded-lg transition duration-150 focus:outline-none"
                                                title="Delete Proposal"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="filteredProposals.length === 0">
                                    <td colspan="6" class="p-12 text-center text-zinc-400">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <svg class="h-8 w-8 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="font-medium text-zinc-700 text-sm">No proposals found</p>
                                            <p class="text-xs text-zinc-450">Adjust your search filters or click "Create Proposal" to initiate a new quote.</p>
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
