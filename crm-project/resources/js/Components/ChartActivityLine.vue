<script setup>
import Chart from 'primevue/chart';
import { ref, computed, onMounted } from 'vue';

// ─── Props (wire to Laravel controller arrays later) ────────────────────────
const props = defineProps({
    unpaidWeekly: {
        type: Array,
        default: () => null, // Array of 10 numbers
    },
    overdueWeekly: {
        type: Array,
        default: () => null, // Array of 10 numbers
    },
    weekLabels: {
        type: Array,
        default: () => null, // Array of 10 strings
    },
});

// ─── Fallback mock data (renders out-of-the-box) ───────────────────────────
const defaultLabels = [
    'Week 01', 'Week 02', 'Week 03', 'Week 04', 'Week 05',
    'Week 06', 'Week 07', 'Week 08', 'Week 09', 'Week 10',
];

const defaultUnpaid = [
    12000, 8500, 22000, 18500, 31000, 47500, 35000, 26000, 19500, 42000,
];

const defaultOverdue = [
    5000, 3200, 14000, 26000, 19000, 24000, 21000, 22500, 28000, 30000,
];

// ─── Resolved reactive data ────────────────────────────────────────────────
const labels = computed(() => props.weekLabels ?? defaultLabels);
const unpaidData = computed(() => props.unpaidWeekly ?? defaultUnpaid);
const overdueData = computed(() => props.overdueWeekly ?? defaultOverdue);

// ─── Dataset visibility toggles ────────────────────────────────────────────
const showUnpaid = ref(true);
const showOverdue = ref(true);

// ─── Time horizon dropdown ─────────────────────────────────────────────────
const selectedHorizon = ref('This Month');
const horizonOptions = ['This Week', 'This Month', 'This Quarter', 'This Year'];
const dropdownOpen = ref(false);

const selectHorizon = (option) => {
    selectedHorizon.value = option;
    dropdownOpen.value = false;
};

// ─── Currency formatter ────────────────────────────────────────────────────
const formatCurrency = (val) => {
    return 'Rs. ' + parseFloat(val || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

// ─── Chart Data (reactive, rebuilds on toggle) ─────────────────────────────
const chartData = computed(() => {
    const datasets = [];

    if (showUnpaid.value) {
        datasets.push({
            label: 'Unpaid Amount',
            data: unpaidData.value,
            borderColor: '#18181b',
            backgroundColor: 'transparent',
            borderWidth: 3,
            tension: 0.4,
            pointRadius: 0,
            pointHoverRadius: 7,
            pointHoverBackgroundColor: '#18181b',
            pointHoverBorderColor: '#ffffff',
            pointHoverBorderWidth: 3,
            fill: false,
        });
    }

    if (showOverdue.value) {
        datasets.push({
            label: 'Overdue Balance',
            data: overdueData.value,
            borderColor: '#a1a1aa',
            backgroundColor: 'transparent',
            borderWidth: 3,
            tension: 0.4,
            pointRadius: 0,
            pointHoverRadius: 7,
            pointHoverBackgroundColor: '#a1a1aa',
            pointHoverBorderColor: '#ffffff',
            pointHoverBorderWidth: 3,
            fill: false,
        });
    }

    return {
        labels: labels.value,
        datasets,
    };
});

// ─── Chart Options ─────────────────────────────────────────────────────────
const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        mode: 'index',
        intersect: false,
    },
    scales: {
        x: {
            grid: {
                color: '#f4f4f5',
                lineWidth: 1,
                drawBorder: false,
            },
            border: {
                display: false,
            },
            ticks: {
                font: {
                    size: 14,
                    family: "'Inter', sans-serif",
                    weight: '500',
                },
                color: '#71717a',
                padding: 12,
            },
        },
        y: {
            grid: {
                color: '#f4f4f5',
                lineWidth: 1,
                drawBorder: false,
            },
            border: {
                display: false,
            },
            ticks: {
                font: {
                    size: 14,
                    family: "'Inter', sans-serif",
                    weight: '500',
                },
                color: '#71717a',
                padding: 16,
                callback: (value) => {
                    if (value >= 1000) {
                        return (value / 1000).toFixed(0) + 'k';
                    }
                    return value;
                },
            },
            beginAtZero: true,
        },
    },
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            enabled: true,
            backgroundColor: '#18181b',
            titleColor: '#ffffff',
            bodyColor: '#d4d4d8',
            titleFont: {
                size: 13,
                weight: '600',
                family: "'Inter', sans-serif",
            },
            bodyFont: {
                size: 13,
                family: "'Inter', sans-serif",
            },
            padding: { top: 14, bottom: 14, left: 18, right: 18 },
            cornerRadius: 10,
            displayColors: true,
            boxWidth: 10,
            boxHeight: 10,
            boxPadding: 6,
            caretSize: 6,
            caretPadding: 8,
            callbacks: {
                title: (items) => {
                    if (items.length) {
                        return items[0].label;
                    }
                    return '';
                },
                label: (context) => {
                    const datasetLabel = context.dataset.label || '';
                    const value = context.parsed.y;
                    return ` ${datasetLabel}: ${formatCurrency(value)}`;
                },
            },
        },
    },
    animation: {
        duration: 900,
        easing: 'easeOutQuart',
    },
}));
</script>

<template>
    <div
        class="bg-white border-2 border-zinc-200 p-8 rounded-2xl w-full flex flex-col justify-between min-h-[480px]"
    >
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!-- HEADER ROW                                                     -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
            <!-- Left: Title -->
            <h3 class="text-2xl font-bold text-zinc-900 tracking-tight">
                Chart Activity
            </h3>

            <!-- Center: Filter toggle switches -->
            <div class="flex items-center gap-6">
                <!-- Unpaid toggle -->
                <label class="flex items-center gap-2.5 cursor-pointer select-none group">
                    <span class="text-sm font-semibold text-zinc-700 group-hover:text-zinc-900 transition-colors">
                        Unpaid
                    </span>
                    <button
                        type="button"
                        role="switch"
                        :aria-checked="showUnpaid"
                        @click="showUnpaid = !showUnpaid"
                        class="relative inline-flex h-6 w-11 shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-zinc-400 focus:ring-offset-2"
                        :class="showUnpaid ? 'bg-zinc-900' : 'bg-zinc-300'"
                    >
                        <span
                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out"
                            :class="showUnpaid ? 'translate-x-5' : 'translate-x-0'"
                        />
                    </button>
                </label>

                <!-- Overdue toggle -->
                <label class="flex items-center gap-2.5 cursor-pointer select-none group">
                    <span class="text-sm font-semibold text-zinc-700 group-hover:text-zinc-900 transition-colors">
                        Overdue
                    </span>
                    <button
                        type="button"
                        role="switch"
                        :aria-checked="showOverdue"
                        @click="showOverdue = !showOverdue"
                        class="relative inline-flex h-6 w-11 shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-zinc-400 focus:ring-offset-2"
                        :class="showOverdue ? 'bg-zinc-500' : 'bg-zinc-300'"
                    >
                        <span
                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out"
                            :class="showOverdue ? 'translate-x-5' : 'translate-x-0'"
                        />
                    </button>
                </label>
            </div>

            <!-- Right: Time horizon dropdown -->
            <div class="relative">
                <button
                    @click="dropdownOpen = !dropdownOpen"
                    type="button"
                    class="inline-flex items-center justify-between border border-zinc-300 rounded-lg h-11 px-4 text-base bg-white text-zinc-900 font-medium min-w-[160px] hover:border-zinc-400 transition-colors focus:outline-none focus:ring-2 focus:ring-zinc-400 focus:ring-offset-1"
                >
                    <span>{{ selectedHorizon }}</span>
                    <svg
                        class="ml-3 h-4 w-4 text-zinc-500 shrink-0 transition-transform duration-200"
                        :class="{ 'rotate-180': dropdownOpen }"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown panel -->
                <Transition
                    enter-active-class="transition ease-out duration-100"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95"
                >
                    <div
                        v-if="dropdownOpen"
                        class="absolute right-0 z-50 mt-2 w-full min-w-[160px] rounded-lg border border-zinc-200 bg-white shadow-lg py-1"
                    >
                        <button
                            v-for="option in horizonOptions"
                            :key="option"
                            @click="selectHorizon(option)"
                            class="block w-full text-left px-4 py-2.5 text-sm font-medium transition-colors"
                            :class="
                                option === selectedHorizon
                                    ? 'bg-zinc-100 text-zinc-900'
                                    : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900'
                            "
                        >
                            {{ option }}
                        </button>
                    </div>
                </Transition>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!-- CHART CANVAS                                                   -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <div class="flex-1 min-h-[280px]">
            <Chart
                type="line"
                :data="chartData"
                :options="chartOptions"
                class="w-full h-full"
            />
        </div>

        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!-- BOTTOM LEGEND                                                  -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <div class="mt-6 pt-5 border-t border-zinc-100 flex items-center justify-center gap-10">
            <!-- Unpaid legend item -->
            <div class="flex items-center gap-2.5">
                <span class="w-4 h-4 rounded-full bg-zinc-900 shrink-0"></span>
                <span class="text-base font-semibold text-zinc-800">
                    Unpaid Amount
                </span>
            </div>

            <!-- Overdue legend item -->
            <div class="flex items-center gap-2.5">
                <span class="w-4 h-4 rounded-full bg-zinc-400 shrink-0"></span>
                <span class="text-base font-semibold text-zinc-800">
                    Overdue Balance
                </span>
            </div>
        </div>
    </div>
</template>
