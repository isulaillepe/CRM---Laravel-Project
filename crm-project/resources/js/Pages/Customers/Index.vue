<script setup>
import { Head, Link, router } from '@inertiajs/vue3';

// Define the data payload passed down from the Laravel Controller
defineProps({
    customers: Array
});

// Helper function to send a linear request to delete a record
const deleteCustomer = (id) => {
    if (confirm('Are you sure you want to delete this customer?')) {
        router.delete(route('customers.destroy', id));
    }
};
</script>

<template>
    <Head title="Customers Dashboard" />

    <div class="min-h-screen bg-gray-100 p-8">
        <div class="max-w-6xl mx-auto bg-white rounded shadow p-6">
            
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <h1 class="text-2xl font-bold text-gray-800">CRM Customer Management</h1>
                <div class="space-x-4">
                    <span class="text-sm text-gray-600">Admin Portal</span>
                    <Link :href="route('logout')" method="post" as="button" class="text-red-500 hover:underline">Logout</Link>
                </div>
            </div>

            <div class="mb-4 flex justify-between items-center">
                <p class="text-gray-600 text-sm">Review, modify, or create client accounts.</p>
                <Link :href="route('customers.create')" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-indigo-700">
                    + Add Customer
                </Link>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-700 text-sm uppercase font-semibold">
                            <th class="p-3">Client Name</th>
                            <th class="p-3">Email Address</th>
                            <th class="p-3">Phone</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-right">Management Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        <tr v-for="customer in customers" :key="customer.id" class="border-b hover:bg-gray-50">
                            <td class="p-3 font-medium text-gray-900">{{ customer.name }}</td>
                            <td class="p-3">{{ customer.email }}</td>
                            <td class="p-3">{{ customer.phone || 'N/A' }}</td>
                            <td class="p-3">
                                <span :class="customer.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded text-xs font-bold uppercase">
                                    {{ customer.status }}
                                </span>
                            </td>
                            <td class="p-3 text-right space-x-3">
                                <Link :href="route('customers.edit', customer.id)" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</Link>
                                <button @click="deleteCustomer(customer.id)" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="customers.length === 0">
                            <td colspan="5" class="p-8 text-center text-gray-400 italic">
                                No clients currently available. Click "+ Add Customer" to seed your database.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</template>