<script setup>
import Chart from 'primevue/chart';
import { computed } from 'vue';

const props = defineProps({
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
    },
});

const totalCount = computed(() => props.paidCount + props.unpaidCount + props.overdueCount);

const percentage = (val) => {
    if (totalCount.value === 0) return 0;
    return Math.round((val / totalCount.value) * 100);
};

const chartData = computed(() => ({
    labels: ['Paid', 'Unpaid', 'Overdue'],
    datasets: [
        {
            data: [props.paidCount, props.unpaidCount, props.overdueCount],
            backgroundColor: ['#18181b', '#e4e4e7', '#71717a'],
            hoverBackgroundColor: ['#27272a', '#d4d4d8', '#52525b'],
            borderWidth: 0,
            borderRadius: 2,
        },
    ],
}));

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    cutout: '75%',
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            backgroundColor: '#18181b',
            titleFont: { size: 13, weight: '600', family: "'Figtree', sans-serif" },
            bodyFont: { size: 12, family: "'Figtree', sans-serif" },
            padding: 12,
            cornerRadius: 8,
            displayColors: true,
            boxWidth: 10,
            boxHeight: 10,
            boxPadding: 4,
            callbacks: {
                label: (context) => {
                    const val = context.parsed;
                    const pct = percentage(val);
                    return ` ${context.label}: ${val} invoice${val !== 1 ? 's' : ''} (${pct}%)`;
                },
            },
        },
    },
    animation: {
        animateRotate: true,
        animateScale: false,
        duration: 800,
        easing: 'easeOutQuart',
    },
}));

const legendItems = computed(() => [
    { label: 'Paid', color: '#18181b', count: props.paidCount, pct: percentage(props.paidCount) },
    { label: 'Unpaid', color: '#e4e4e7', count: props.unpaidCount, pct: percentage(props.unpaidCount) },
    { label: 'Overdue', color: '#71717a', count: props.overdueCount, pct: percentage(props.overdueCount) },
]);
</script>

<template>
    <div class="bg-white rounded-xl border border-zinc-200 p-6 flex flex-col">
        <!-- Header -->
        <div class="mb-6">
            <h3 class="text-base font-semibold text-zinc-900 tracking-tight">
                Invoice Status
            </h3>
            <p class="text-xs text-zinc-400 mt-0.5">
                Distribution of invoices by payment status
            </p>
        </div>

        <!-- Chart Area -->
        <div class="relative flex items-center justify-center" style="height: 220px">
            <!-- Empty State -->
            <div
                v-if="totalCount === 0"
                class="flex flex-col items-center justify-center text-center"
            >
                <svg
                    class="w-10 h-10 text-zinc-200 mb-3"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"
                    />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"
                    />
                </svg>
                <p class="text-xs text-zinc-400 font-medium">No invoices yet</p>
            </div>

            <!-- Doughnut Chart -->
            <template v-else>
                <Chart
                    type="doughnut"
                    :data="chartData"
                    :options="chartOptions"
                    class="w-full h-full"
                />

                <!-- Center Label -->
                <div
                    class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none"
                >
                    <span class="text-2xl font-bold text-zinc-900 leading-none">
                        {{ totalCount }}
                    </span>
                    <span class="text-[10px] text-zinc-400 font-medium uppercase tracking-wider mt-1">
                        Total
                    </span>
                </div>
            </template>
        </div>

        <!-- Custom Legend -->
        <div class="mt-6 pt-5 border-t border-zinc-100 grid grid-cols-3 gap-3">
            <div
                v-for="item in legendItems"
                :key="item.label"
                class="flex flex-col items-center text-center"
            >
                <div class="flex items-center gap-1.5 mb-1">
                    <span
                        class="w-2.5 h-2.5 rounded-full shrink-0"
                        :style="{ backgroundColor: item.color }"
                    ></span>
                    <span class="text-[10px] text-zinc-500 font-semibold uppercase tracking-wider">
                        {{ item.label }}
                    </span>
                </div>
                <span class="text-sm font-bold text-zinc-900">
                    {{ item.count }}
                </span>
                <span class="text-[10px] text-zinc-400">
                    {{ item.pct }}%
                </span>
            </div>
        </div>
    </div>
</template>
