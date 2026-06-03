<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    proposals: {
        type: Array,
        required: true,
    }
});

// Local state for interactive drag-and-drop & status filtering
const searchQuery = ref('');
const localProposals = ref([...props.proposals]);

// Keep local proposals in sync if props update
import { watch } from 'vue';
watch(() => props.proposals, (newVal) => {
    localProposals.value = [...newVal];
}, { deep: true });

// Define our board columns/lanes
const columns = [
    { id: 'draft', title: 'Draft', badgeColor: 'bg-zinc-100 text-zinc-700 border-zinc-200' },
    { id: 'sent', title: 'Sent', badgeColor: 'bg-blue-50 text-blue-700 border-blue-100' },
    { id: 'accepted', title: 'Accepted', badgeColor: 'bg-emerald-50 text-emerald-700 border-emerald-100' },
    { id: 'declined', title: 'Declined', badgeColor: 'bg-rose-50 text-rose-700 border-rose-100' }
];

// Helper to format currency
const formatCurrency = (val) => {
    return 'Rs. ' + parseFloat(val || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

// Filtered proposals based on search query
const filteredProposals = computed(() => {
    if (!searchQuery.value) return localProposals.value;
    const query = searchQuery.value.toLowerCase();
    return localProposals.value.filter(p => 
        p.title.toLowerCase().includes(query) ||
        p.description.toLowerCase().includes(query) ||
        (p.customer && p.customer.name.toLowerCase().includes(query))
    );
});

// Group proposals by status for rendering in columns
const proposalsByStatus = computed(() => {
    const groups = {
        draft: [],
        sent: [],
        accepted: [],
        declined: []
    };
    filteredProposals.value.forEach(p => {
        if (groups[p.status]) {
            groups[p.status].push(p);
        } else {
            // Fallback for unexpected statuses
            groups.draft.push(p);
        }
    });
    return groups;
});

// Calculate statistics per column
const columnStats = computed(() => {
    const stats = {};
    columns.forEach(col => {
        const list = proposalsByStatus.value[col.id] || [];
        const count = list.length;
        const totalValue = list.reduce((sum, p) => sum + parseFloat(p.value || 0), 0);
        stats[col.id] = { count, totalValue };
    });
    return stats;
});

// Overall pipeline metrics
const totalValuation = computed(() => {
    return localProposals.value.reduce((sum, p) => sum + parseFloat(p.value || 0), 0);
});

const activeDealsCount = computed(() => {
    return localProposals.value.length;
});

// Drag & Drop State
const draggedProposalId = ref(null);
const activeDropTarget = ref(null);

const onDragStart = (event, proposal) => {
    draggedProposalId.value = proposal.id;
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', proposal.id);
    
    // Add transparent/dragging styling
    event.currentTarget.classList.add('opacity-40');
};

const onDragEnd = (event) => {
    event.currentTarget.classList.remove('opacity-40');
    draggedProposalId.value = null;
    activeDropTarget.value = null;
};

const onDragOver = (event, columnId) => {
    event.preventDefault();
    activeDropTarget.value = columnId;
};

const onDragLeave = () => {
    // We only unset if leaving the workspace or handling with timer
};

const onDrop = (event, targetStatus) => {
    event.preventDefault();
    activeDropTarget.value = null;
    
    const proposalId = draggedProposalId.value || parseInt(event.dataTransfer.getData('text/plain'), 10);
    if (!proposalId) return;

    const proposal = localProposals.value.find(p => p.id === proposalId);
    if (proposal && proposal.status !== targetStatus) {
        updateProposalStatus(proposal, targetStatus);
    }
};

// Update proposal status via Inertia patch request
const updatingId = ref(null);

const updateProposalStatus = (proposal, newStatus) => {
    updatingId.value = proposal.id;
    const oldStatus = proposal.status;
    
    // Optimistic local state update for instant UI feedback
    proposal.status = newStatus;

    router.patch(route('proposals.update', proposal.id) + '?redirect_to=board', {
        customer_id: proposal.customer_id,
        title: proposal.title,
        description: proposal.description,
        value: proposal.value,
        status: newStatus
    }, {
        preserveScroll: true,
        onSuccess: () => {
            updatingId.value = null;
        },
        onError: (err) => {
            updatingId.value = null;
            // Revert status on failure
            proposal.status = oldStatus;
            console.error('Failed to update proposal status:', err);
        }
    });
};
</script>

<template>
    <Head title="Pipeline Board" />

    <AuthenticatedLayout>
        <!-- Stark white, clean header with thin borders -->
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-2xl font-semibold text-zinc-900 tracking-tight">Pipeline Board</h2>
                    <p class="text-sm text-zinc-500 mt-1">Stark visual overview of active client proposals and deal statuses.</p>
                </div>
                
                <div class="flex items-center space-x-3">
                    <!-- Minimalist Toggle Control -->
                    <div class="inline-flex rounded-lg border border-zinc-200 bg-white p-0.5">
                        <Link 
                            :href="route('proposals.index')" 
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
                        :href="route('proposals.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white hover:bg-blue-700 active:bg-blue-800 focus:outline-none transition duration-150 ease-in-out shadow-sm"
                    >
                        <svg class="h-4 w-4 mr-1.5 -ml-0.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Proposal
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 bg-white min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Overall minimal pipeline summaries -->
                <div class="flex flex-wrap items-center justify-between border border-zinc-200 bg-white p-6 rounded-xl gap-6">
                    <div class="flex items-center space-x-8">
                        <div>
                            <span class="text-xs text-zinc-400 font-medium uppercase tracking-wider block">Total Deals Valuation</span>
                            <span class="text-2xl font-bold text-zinc-900 mt-1 block">{{ formatCurrency(totalValuation) }}</span>
                        </div>
                        <div class="h-8 w-px bg-zinc-200"></div>
                        <div>
                            <span class="text-xs text-zinc-400 font-medium uppercase tracking-wider block">Total Active Proposals</span>
                            <span class="text-2xl font-bold text-zinc-900 mt-1 block">{{ activeDealsCount }}</span>
                        </div>
                    </div>
                    
                    <!-- Search Input conforming to minimalist system -->
                    <div class="relative w-full md:max-w-xs">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="h-4 w-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input 
                            v-model="searchQuery" 
                            type="text" 
                            placeholder="Filter board deals..." 
                            class="pl-9 pr-4 py-2 w-full bg-white hover:bg-zinc-50 focus:bg-white text-xs text-zinc-900 border border-zinc-200 rounded-lg focus:ring-2 focus:ring-zinc-900 focus:border-zinc-900 transition duration-150"
                        />
                    </div>
                </div>

                <!-- Kanban Board Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-start">
                    
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
                            
                            <!-- Proposal Kanban Card -->
                            <div 
                                v-for="proposal in proposalsByStatus[col.id]"
                                :key="proposal.id"
                                draggable="true"
                                @dragstart="onDragStart($event, proposal)"
                                @dragend="onDragEnd"
                                class="bg-white border border-zinc-200 hover:border-zinc-900 rounded-lg p-4 transition-all duration-150 cursor-grab active:cursor-grabbing group relative"
                            >
                                <!-- Title -->
                                <div class="flex justify-between items-start gap-2">
                                    <h4 class="text-xs font-semibold text-zinc-900 tracking-tight group-hover:text-blue-600 transition duration-150">
                                        {{ proposal.title }}
                                    </h4>
                                    <!-- Action Link styled with single accent rule -->
                                    <Link 
                                        :href="route('proposals.edit', proposal.id) + '?from=board'"
                                        class="text-[10px] font-medium text-zinc-400 hover:text-blue-600 opacity-0 group-hover:opacity-100 transition duration-150 flex items-center"
                                    >
                                        Edit
                                    </Link>
                                </div>

                                <!-- Description (truncated) -->
                                <p class="text-[11px] text-zinc-400 mt-1 line-clamp-2 leading-relaxed">
                                    {{ proposal.description }}
                                </p>

                                <!-- Customer Details -->
                                <div v-if="proposal.customer" class="flex items-center mt-3 pt-3 border-t border-zinc-100">
                                    <div class="h-5 w-5 rounded-full bg-zinc-100 border border-zinc-200 flex items-center justify-center font-bold text-[8px] text-zinc-500 shrink-0">
                                        {{ proposal.customer.name.substring(0,2).toUpperCase() }}
                                    </div>
                                    <span class="text-[10px] font-medium text-zinc-600 ml-1.5 truncate">
                                        {{ proposal.customer.name }}
                                    </span>
                                </div>

                                <!-- Card Bottom -->
                                <div class="flex items-center justify-between mt-3 pt-2">
                                    <span class="text-[11px] font-bold text-zinc-950">
                                        {{ formatCurrency(proposal.value) }}
                                    </span>
                                    
                                    <!-- Simple select dropdown to manually change status for a clear fallback mechanism -->
                                    <div class="relative">
                                        <select 
                                            :value="proposal.status" 
                                            @change="updateProposalStatus(proposal, $event.target.value)"
                                            class="appearance-none bg-zinc-50 hover:bg-zinc-100/70 border border-zinc-200 rounded px-1.5 py-0.5 pr-4 text-[9px] font-semibold text-zinc-500 focus:outline-none transition cursor-pointer"
                                        >
                                            <option value="draft">Draft</option>
                                            <option value="sent">Sent</option>
                                            <option value="accepted">Accepted</option>
                                            <option value="declined">Declined</option>
                                        </select>
                                        <span class="absolute inset-y-0 right-1 flex items-center pointer-events-none text-zinc-400 text-[8px]">
                                            ▼
                                        </span>
                                    </div>
                                </div>

                                <!-- Loading overlay during server updates -->
                                <div 
                                    v-if="updatingId === proposal.id" 
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
                                v-if="proposalsByStatus[col.id]?.length === 0" 
                                class="flex-grow flex flex-col items-center justify-center border border-dashed border-zinc-200 rounded-lg p-6 text-center select-none"
                            >
                                <span class="text-[10px] font-medium text-zinc-300">No proposals</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
