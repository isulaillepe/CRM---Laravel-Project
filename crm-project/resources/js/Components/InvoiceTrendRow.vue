<script setup>
import { computed } from 'vue';

const props = defineProps({
    invoiceId: {
        type: String,
        required: true,
    },
    clientName: {
        type: String,
        default: 'Unknown Client',
    },
    dateCreated: {
        type: String,
        default: '',
    },
    amountDue: {
        type: Number,
        default: 0,
    },
    status: {
        type: String,
        default: 'unpaid',
        validator: (val) => ['paid', 'unpaid', 'overdue'].includes(val),
    },
    showActions: {
        type: Boolean,
        default: true,
    },
    minimalist: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['delete', 'edit', 'email', 'status-change']);

// ─── Sparkline SVG Paths ────────────────────────────────────────
// Pre-coded wave paths that cycle based on a simple hash of the invoiceId.
// Each path traces a smooth micro trend-line across a 160×48 viewBox.
const sparklinePaths = [
    'M0 36 C12 36, 16 18, 28 20 S44 38, 56 30 S72 10, 84 16 S100 34, 112 24 S128 12, 140 20 S152 32, 160 28',
    'M0 28 C10 28, 18 40, 30 34 S48 14, 60 22 S76 38, 88 26 S104 10, 116 18 S132 36, 144 30 S156 22, 160 24',
    'M0 32 C14 32, 20 12, 34 18 S50 36, 64 28 S80 16, 92 22 S108 40, 120 32 S136 18, 148 26 S156 30, 160 28',
    'M0 24 C8 24, 16 38, 28 32 S42 16, 56 24 S70 40, 84 34 S98 18, 112 26 S126 38, 140 28 S152 20, 160 24',
    'M0 30 C12 30, 22 14, 36 22 S52 42, 66 34 S78 18, 92 26 S106 36, 118 28 S132 14, 146 22 S154 34, 160 30',
];

// Simple hash to deterministically pick a path for each invoice
const pathIndex = computed(() => {
    let hash = 0;
    for (let i = 0; i < props.invoiceId.length; i++) {
        hash = (hash * 31 + props.invoiceId.charCodeAt(i)) % sparklinePaths.length;
    }
    return Math.abs(hash) % sparklinePaths.length;
});

const sparklinePath = computed(() => sparklinePaths[pathIndex.value]);

// ─── Status-Driven Colors ───────────────────────────────────────
const statusConfig = computed(() => {
    if (props.minimalist) {
        switch (props.status) {
            case 'paid':
                return {
                    stroke: '#18181b',       // zinc-900 (black)
                    gradientFrom: '#18181b',
                    gradientTo: '#18181b00',
                    badgeBg: 'bg-zinc-900',
                    badgeText: 'text-zinc-100',
                    badgeBorder: 'border-zinc-800',
                    dotBg: 'bg-zinc-400',
                    label: 'Paid',
                };
            case 'overdue':
                return {
                    stroke: '#71717a',       // zinc-500
                    gradientFrom: '#71717a',
                    gradientTo: '#71717a00',
                    badgeBg: 'bg-zinc-100',
                    badgeText: 'text-zinc-800',
                    badgeBorder: 'border-zinc-300',
                    dotBg: 'bg-zinc-650',
                    label: 'Overdue',
                };
            default: // unpaid
                return {
                    stroke: '#d4d4d8',       // zinc-300
                    gradientFrom: '#d4d4d8',
                    gradientTo: '#d4d4d800',
                    badgeBg: 'bg-zinc-50',
                    badgeText: 'text-zinc-650',
                    badgeBorder: 'border-zinc-200',
                    dotBg: 'bg-zinc-400',
                    label: 'Unpaid',
                };
        }
    }

    switch (props.status) {
        case 'paid':
            return {
                stroke: '#10b981',       // emerald-500
                gradientFrom: '#10b981',
                gradientTo: '#10b98100',
                badgeBg: 'bg-emerald-50/60',
                badgeText: 'text-emerald-700',
                badgeBorder: 'border-emerald-200/50',
                dotBg: 'bg-emerald-500',
                label: 'Paid',
            };
        case 'overdue':
            return {
                stroke: '#f43f5e',       // rose-500
                gradientFrom: '#f43f5e',
                gradientTo: '#f43f5e00',
                badgeBg: 'bg-rose-50/60',
                badgeText: 'text-rose-700',
                badgeBorder: 'border-rose-200/50',
                dotBg: 'bg-rose-500',
                label: 'Overdue',
            };
        default: // unpaid
            return {
                stroke: '#f59e0b',       // amber-500
                gradientFrom: '#f59e0b',
                gradientTo: '#f59e0b00',
                badgeBg: 'bg-amber-50/60',
                badgeText: 'text-amber-700',
                badgeBorder: 'border-amber-200/50',
                dotBg: 'bg-amber-500',
                label: 'Unpaid',
            };
    }
});

// ─── Date Formatting ────────────────────────────────────────────
const formattedDate = computed(() => {
    if (!props.dateCreated) return 'Date unavailable';
    try {
        return new Date(props.dateCreated).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    } catch {
        return props.dateCreated;
    }
});

// ─── Currency Formatting ────────────────────────────────────────
const formattedAmount = computed(() => {
    return 'Rs. ' + parseFloat(props.amountDue || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
});

// ─── Client Initials ────────────────────────────────────────────
const clientInitials = computed(() => {
    if (!props.clientName) return '??';
    return props.clientName
        .split(' ')
        .map((w) => w[0])
        .join('')
        .substring(0, 2)
        .toUpperCase();
});

// Unique gradient ID to avoid SVG conflicts when multiple rows render
const gradientId = computed(() => `sparkGrad-${props.invoiceId.replace(/[^a-zA-Z0-9]/g, '')}`);
</script>

<template>
    <div
        class="bg-white border border-zinc-200 hover:border-zinc-300 p-6 rounded-xl flex flex-col lg:flex-row items-start lg:items-center justify-between w-full transition-all duration-200 group"
    >
        <!-- ═══════════════════════════════════════════ -->
        <!-- LEFT: Invoice Metadata                     -->
        <!-- ═══════════════════════════════════════════ -->
        <div class="flex-1 min-w-0">
            <!-- Invoice ID Pill Badge -->
            <span
                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-zinc-100 text-zinc-600 border border-zinc-200 tracking-wide"
            >
                {{ invoiceId }}
            </span>

            <!-- Client Name Heading -->
            <h3 class="text-xl font-bold text-zinc-900 mt-2.5 truncate tracking-tight">
                {{ clientName }}
            </h3>

            <!-- Date Created Subtitle -->
            <div class="flex items-center gap-1.5 mt-1.5">
                <!-- Calendar Check Icon -->
                <svg
                    class="w-3.5 h-3.5 text-zinc-400 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                </svg>
                <span class="text-sm text-zinc-500">
                    Created on {{ formattedDate }}
                </span>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════ -->
        <!-- CENTER: SVG Sparkline                      -->
        <!-- ═══════════════════════════════════════════ -->
        <div class="w-40 h-12 mx-6 shrink-0 hidden md:block">
            <svg
                viewBox="0 0 160 48"
                class="w-full h-full overflow-visible"
                preserveAspectRatio="none"
            >
                <defs>
                    <linearGradient
                        :id="gradientId"
                        x1="0%"
                        y1="0%"
                        x2="0%"
                        y2="100%"
                    >
                        <stop
                            offset="0%"
                            :stop-color="statusConfig.gradientFrom"
                            stop-opacity="0.15"
                        />
                        <stop
                            offset="100%"
                            :stop-color="statusConfig.gradientTo"
                            stop-opacity="0"
                        />
                    </linearGradient>
                </defs>

                <!-- Area fill under the line -->
                <path
                    :d="sparklinePath + ' L160 48 L0 48 Z'"
                    :fill="`url(#${gradientId})`"
                />

                <!-- Main trend stroke -->
                <path
                    :d="sparklinePath"
                    fill="none"
                    :stroke="statusConfig.stroke"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </div>

        <!-- ═══════════════════════════════════════════ -->
        <!-- RIGHT: Financial Value + Status + Actions  -->
        <!-- ═══════════════════════════════════════════ -->
        <div class="flex items-center gap-5 mt-4 lg:mt-0 shrink-0">
            <!-- Amount + Badge Stack -->
            <div class="text-right">
                <div class="text-2xl font-black text-zinc-900 tracking-tight leading-tight">
                    {{ formattedAmount }}
                </div>
                <span
                    class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium border mt-1.5"
                    :class="[statusConfig.badgeBg, statusConfig.badgeText, statusConfig.badgeBorder]"
                >
                    <span
                        class="h-1.5 w-1.5 rounded-full mr-1.5"
                        :class="statusConfig.dotBg"
                    ></span>
                    {{ statusConfig.label }}
                </span>
            </div>

            <!-- Action Buttons -->
            <div v-if="showActions" class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                <!-- Edit -->
                <button
                    v-if="status !== 'paid'"
                    @click="emit('edit')"
                    class="p-2 text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 rounded-lg transition duration-150 focus:outline-none"
                    title="Edit Invoice"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                        />
                    </svg>
                </button>

                <!-- Email -->
                <button
                    v-if="status !== 'paid'"
                    @click="emit('email')"
                    class="p-2 text-zinc-400 hover:text-blue-600 hover:bg-blue-50/50 rounded-lg transition duration-150 focus:outline-none"
                    title="Email Invoice"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                        />
                    </svg>
                </button>

                <!-- Delete -->
                <button
                    @click="emit('delete')"
                    class="p-2 text-zinc-400 hover:text-rose-600 hover:bg-rose-50/50 rounded-lg transition duration-150 focus:outline-none"
                    title="Delete Invoice"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
