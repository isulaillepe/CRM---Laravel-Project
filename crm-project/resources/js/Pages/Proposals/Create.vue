<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    customers: {
        type: Array,
        required: true,
    }
});

// Initialize Inertia Form Helper
const form = useForm({
    customer_id: '',
    title: '',
    description: '',
    value: '',
    status: 'draft'
});

const submit = () => {
    form.post(route('proposals.store'));
};
</script>

<template>
    <Head title="Initiate New Proposal" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 tracking-tight">Initiate Proposal</h2>
                    <p class="text-sm text-gray-500 mt-1">Configure and assign a commercial proposal to an existing customer profile.</p>
                </div>
                <Link 
                    :href="route('proposals.index')" 
                    class="inline-flex items-center px-4 py-2 border border-gray-250 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:shadow-sm transition"
                >
                    <svg class="h-5 w-5 mr-1.5 -ml-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to List
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                    
                    <form @submit.prevent="submit" class="space-y-5">
                        
                        <!-- Customer Selection -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Associated Client</label>
                            <select 
                                v-model="form.customer_id" 
                                required
                                class="mt-2 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
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
                            <div v-if="form.errors.customer_id" class="text-rose-500 text-xs mt-1 font-medium">{{ form.errors.customer_id }}</div>
                            <p v-if="customers.length === 0" class="text-amber-600 text-xs mt-1.5 font-medium">
                                No clients exist in database yet. 
                                <Link :href="route('customers.index')" class="underline text-indigo-600 font-semibold">Create one first</Link>.
                            </p>
                        </div>

                        <!-- Proposal Title -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Proposal Title</label>
                            <input 
                                v-model="form.title" 
                                type="text" 
                                required 
                                placeholder="E.g. Enterprise Cloud Migration Contract"
                                class="mt-2 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                            />
                            <div v-if="form.errors.title" class="text-rose-500 text-xs mt-1 font-medium">{{ form.errors.title }}</div>
                        </div>

                        <!-- Proposal Description -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Detailed Description</label>
                            <textarea 
                                v-model="form.description" 
                                rows="5"
                                required
                                placeholder="Describe the scope of work, timeline, and deliverables..."
                                class="mt-2 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                            ></textarea>
                            <div v-if="form.errors.description" class="text-rose-500 text-xs mt-1 font-medium">{{ form.errors.description }}</div>
                        </div>

                        <!-- Deal Value & Initial Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Deal Value -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Deal Value (LKR)</label>
                                <div class="mt-2 relative rounded-xl shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <span class="text-gray-400 text-sm">Rs.</span>
                                    </div>
                                    <input 
                                        v-model="form.value" 
                                        type="number" 
                                        step="0.01"
                                        min="0"
                                        required 
                                        placeholder="0.00"
                                        class="pl-11 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                                    />
                                </div>
                                <div v-if="form.errors.value" class="text-rose-500 text-xs mt-1 font-medium">{{ form.errors.value }}</div>
                            </div>

                            <!-- Proposal Status -->
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Initial Status</label>
                                <select 
                                    v-model="form.status" 
                                    required
                                    class="mt-2 block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 transition duration-150"
                                >
                                    <option value="draft">Draft (Internal Only)</option>
                                    <option value="sent">Sent to Client</option>
                                    <option value="accepted">Accepted (Won)</option>
                                    <option value="declined">Declined (Lost)</option>
                                </select>
                                <div v-if="form.errors.status" class="text-rose-500 text-xs mt-1 font-medium">{{ form.errors.status }}</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-5 border-t border-gray-100 flex justify-end space-x-3 mt-6">
                            <Link 
                                :href="route('proposals.index')" 
                                class="px-4 py-2.5 border border-gray-250 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition"
                            >
                                Cancel
                            </Link>
                            <button 
                                type="submit" 
                                :disabled="form.processing" 
                                class="px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50 transition shadow-sm hover:shadow"
                            >
                                {{ form.processing ? 'Saving...' : 'Initiate Deal' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
