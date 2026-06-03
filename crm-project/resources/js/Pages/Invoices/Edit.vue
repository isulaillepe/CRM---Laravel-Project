<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    invoice: {
        type: Object,
        required: true,
    },
    customers: {
        type: Array,
        required: true,
    }
});

const isFromBoard = ref(false);

onMounted(() => {
    isFromBoard.value = new URLSearchParams(window.location.search).get('from') === 'board';
});

// Initialize form helper with loaded invoice data
const form = useForm({
    customer_id: props.invoice.customer_id || '',
    invoice_number: props.invoice.invoice_number || '',
    amount: props.invoice.amount || '',
    status: props.invoice.status || 'unpaid',
    due_date: props.invoice.due_date || ''
});

const submit = () => {
    form.put(route('invoices.update', props.invoice.id) + (isFromBoard.value ? '?redirect_to=board' : ''));
};
</script>

<template>
    <Head title="Edit Invoice" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-zinc-900 tracking-tight">Edit Invoice</h2>
                    <p class="text-xs text-zinc-500 mt-1">Modify invoice parameters and update the billing status.</p>
                </div>
                <Link 
                    :href="isFromBoard ? route('invoices.board') : route('invoices.index')" 
                    class="inline-flex items-center px-4 py-2 border border-zinc-200 rounded-lg text-xs font-medium text-zinc-700 bg-white hover:bg-zinc-50 transition"
                >
                    <svg class="h-4 w-4 mr-1.5 -ml-1 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ isFromBoard ? 'Back to Board' : 'Back to List' }}
                </Link>
            </div>
        </template>

        <div class="py-12 bg-white min-h-screen">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg border border-zinc-200 p-6 md:p-8">
                    
                    <form @submit.prevent="submit" class="space-y-5">
                        
                        <!-- Customer Selection -->
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Billing Client</label>
                            <select 
                                v-model="form.customer_id" 
                                required
                                class="mt-1.5 block w-full rounded-lg border-zinc-200 hover:border-zinc-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-xs py-2 transition"
                            >
                                <option value="" disabled>-- Select Customer Account --</option>
                                <option 
                                    v-for="customer in customers" 
                                    :key="customer.id" 
                                    :value="customer.id"
                                >
                                    {{ customer.name }} ({{ customer.email }})
                                </option>
                            </select>
                            <div v-if="form.errors.customer_id" class="text-red-650 text-[10px] mt-1 font-medium">{{ form.errors.customer_id }}</div>
                        </div>

                        <!-- Invoice Number -->
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Invoice Number</label>
                            <input 
                                v-model="form.invoice_number" 
                                type="text" 
                                required 
                                placeholder="INV-YYYYMMDD-XXXX"
                                class="mt-1.5 block w-full rounded-lg border-zinc-200 hover:border-zinc-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-xs py-2 transition duration-150"
                            />
                            <div v-if="form.errors.invoice_number" class="text-red-650 text-[10px] mt-1 font-medium">{{ form.errors.invoice_number }}</div>
                        </div>

                        <!-- Invoice Amount & Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Amount -->
                            <div>
                                <label class="block text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Billing Amount (LKR)</label>
                                <div class="mt-1.5 relative rounded-lg">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-zinc-400 text-xs">Rs.</span>
                                    </div>
                                    <input 
                                        v-model="form.amount" 
                                        type="number" 
                                        step="0.01"
                                        min="0"
                                        required 
                                        placeholder="0.00"
                                        class="pl-8 block w-full rounded-lg border-zinc-200 hover:border-zinc-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-xs py-2 transition duration-150"
                                    />
                                </div>
                                <div v-if="form.errors.amount" class="text-red-650 text-[10px] mt-1 font-medium">{{ form.errors.amount }}</div>
                            </div>

                            <!-- Invoice Status Dropdown -->
                            <div>
                                <label class="block text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Invoice Status</label>
                                <select 
                                    v-model="form.status" 
                                    required
                                    class="mt-1.5 block w-full rounded-lg border-zinc-200 hover:border-zinc-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-xs py-2 transition duration-150"
                                >
                                    <option value="unpaid">Unpaid</option>
                                    <option value="paid">Paid (Cleared)</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                                <div v-if="form.errors.status" class="text-red-650 text-[10px] mt-1 font-medium">{{ form.errors.status }}</div>
                            </div>
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Due Date</label>
                            <input 
                                v-model="form.due_date" 
                                type="date" 
                                required
                                class="mt-1.5 block w-full rounded-lg border-zinc-200 hover:border-zinc-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-xs py-2 transition duration-150"
                            />
                            <div v-if="form.errors.due_date" class="text-red-650 text-[10px] mt-1 font-medium">{{ form.errors.due_date }}</div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-5 border-t border-zinc-100 flex justify-end space-x-2 mt-6">
                            <Link 
                                :href="isFromBoard ? route('invoices.board') : route('invoices.index')" 
                                class="px-4 py-2 border border-zinc-200 rounded-lg text-xs font-medium text-zinc-700 hover:bg-zinc-50 transition"
                            >
                                Cancel
                            </Link>
                            <button 
                                type="submit" 
                                :disabled="form.processing" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 border border-transparent rounded-lg text-xs font-medium text-white transition disabled:opacity-50 shadow-none"
                            >
                                {{ form.processing ? 'Saving...' : 'Update Invoice' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
