<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

// Define incoming customer data passed down from Laravel
const props = defineProps({
    customer: Object
});

// Initialize form helper with current customer attributes
const form = useForm({
    name: props.customer.name,
    email: props.customer.email,
    phone: props.customer.phone || '',
    status: props.customer.status
});

// Submit handler to send a PATCH request linearly to update the record
const submit = () => {
    form.patch(route('customers.update', props.customer.id));
};
</script>

<template>
    <Head title="Edit Customer" />

    <div class="min-h-screen bg-gray-100 p-8">
        <div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
            
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <h1 class="text-xl font-bold text-gray-800">Edit Customer</h1>
                <Link :href="route('customers.index')" class="text-sm text-indigo-600 hover:underline">← Back to List</Link>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input v-model="form.name" type="text" required class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input v-model="form.email" type="email" required class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input v-model="form.phone" type="text" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    <div v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select v-model="form.status" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="pt-4 flex justify-end space-x-2">
                    <Link :href="route('customers.index')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300">Cancel</Link>
                    <button type="submit" :disabled="form.processing" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700 disabled:opacity-50">
                        {{ form.processing ? 'Saving...' : 'Update Customer' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</template>