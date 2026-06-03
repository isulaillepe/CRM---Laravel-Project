<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Initialize the reactive Inertia Form Helper
const form = useForm({
    name: '',
    email: '',
    phone: '',
    status: 'active'
});

// Submit handler to send a POST request linearly to Laravel
const submit = () => {
    form.post(route('customers.store'));
};
</script>

<template>
    <Head title="Add New Customer" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-2xl text-zinc-900 tracking-tight">Add New Customer</h2>
                    <p class="text-xs text-zinc-500 mt-1">Register a new client profile into the CRM database.</p>
                </div>
                <Link 
                    :href="route('customers.index')" 
                    class="inline-flex items-center px-4 py-2 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 transition"
                >
                    <svg class="h-4 w-4 mr-1.5 -ml-1 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Database
                </Link>
            </div>
        </template>

        <div class="py-8 bg-white min-h-screen">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl border border-zinc-200 p-6 md:p-8">
                    
                    <form @submit.prevent="submit" class="space-y-5">
                        <!-- Full Name -->
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Full Name</label>
                            <input 
                                v-model="form.name" 
                                type="text" 
                                required 
                                placeholder="Isula Illeperuma"
                                class="mt-1.5 block w-full rounded-lg border-zinc-200 hover:border-zinc-300 focus:border-zinc-900 focus:ring-zinc-900 text-xs py-2 transition duration-150"
                            />
                            <div v-if="form.errors.name" class="text-rose-500 text-[10px] mt-1 font-medium">{{ form.errors.name }}</div>
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Email Address</label>
                            <input 
                                v-model="form.email" 
                                type="email" 
                                required 
                                placeholder="hello@gmail.com"
                                class="mt-1.5 block w-full rounded-lg border-zinc-200 hover:border-zinc-300 focus:border-zinc-900 focus:ring-zinc-900 text-xs py-2 transition duration-150"
                            />
                            <div v-if="form.errors.email" class="text-rose-500 text-[10px] mt-1 font-medium">{{ form.errors.email }}</div>
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Phone Number</label>
                            <input 
                                v-model="form.phone" 
                                type="text" 
                                placeholder="+94774710311"
                                class="mt-1.5 block w-full rounded-lg border-zinc-200 hover:border-zinc-300 focus:border-zinc-900 focus:ring-zinc-900 text-xs py-2 transition duration-150"
                            />
                            <div v-if="form.errors.phone" class="text-rose-500 text-[10px] mt-1 font-medium">{{ form.errors.phone }}</div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Profile Status</label>
                            <select 
                                v-model="form.status" 
                                class="mt-1.5 block w-full rounded-lg border-zinc-300 hover:border-zinc-400 focus:border-black focus:ring-black text-xs py-2 transition"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-5 border-t border-zinc-100 flex justify-end space-x-2 mt-6">
                            <Link 
                                :href="route('customers.index')" 
                                class="px-4 py-2 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition"
                            >
                                Cancel
                            </Link>
                            <button 
                                type="submit" 
                                :disabled="form.processing" 
                                class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 border border-transparent rounded-lg text-xs font-semibold text-white transition disabled:opacity-50"
                            >
                                {{ form.processing ? 'Saving...' : 'Save Customer' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
