<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Welcome | Central Distriubutors CRM" />

    <div class="min-h-screen bg-white text-zinc-900 flex flex-col font-sans antialiased selection:bg-blue-600 selection:text-white">
        <!-- Top Navbar -->
        <header class="border-b border-zinc-200 bg-white sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <!-- Stark Monochrome Logo -->
                    <div class="h-8 w-8 bg-zinc-900 text-white rounded flex items-center justify-center font-bold tracking-tighter text-sm">
                        CC
                    </div>
                    <span class="font-semibold text-zinc-900 tracking-tight text-sm">Central Distriubutors CRM</span>
                </div>
                
                <nav v-if="canLogin" class="flex items-center space-x-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition shadow-none"
                    >
                        Go to Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-xs font-medium text-zinc-600 hover:text-zinc-950 transition"
                        >
                            Log in
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center px-3.5 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-medium rounded-lg transition"
                        >
                            Register
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Main Hero & Preview Container -->
        <main class="flex-grow flex flex-col justify-center py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center space-y-8">
                <!-- Hero Header -->
                <div class="space-y-4">
                    <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 bg-zinc-50 border border-zinc-200 rounded text-[10px] text-zinc-500 uppercase font-semibold tracking-wider">
                        <span>Central Distributors</span>
                        <span class="h-1 w-1 rounded-full bg-blue-600"></span>
                        <span>Release v1.0</span>
                    </div>
                    <h1 class="text-4xl sm:text-5xl font-semibold text-zinc-950 tracking-tight leading-none max-w-2xl mx-auto">
                        The CRM for a streamlined business.
                    </h1>
                    <p class="text-zinc-500 text-base max-w-lg mx-auto font-normal leading-relaxed">
                        Say goodbye to cluttered spreadsheets. Track quotations, manage customer interactions, and streamline your sales operations—all within one intuitive platform.
                    </p>
                </div>

                <!-- Call to Action -->
                <div class="flex items-center justify-center space-x-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition shadow-none"
                    >
                        Access Workspace
                        <svg class="h-3.5 w-3.5 ml-1.5 -mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition shadow-none"
                        >
                            Log in to Workspace
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center px-5 py-2.5 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-700 text-xs font-medium rounded-lg transition"
                        >
                            Create Account
                        </Link>
                    </template>
                </div>

                <!-- Minimalist UI Interface Preview (Pure HTML/CSS Mockup) -->
                <div class="pt-8 max-w-3xl mx-auto">
                    <div class="bg-white rounded-lg border border-zinc-200 text-left overflow-hidden shadow-none">
                        <!-- Preview Navbar -->
                        <div class="px-4 py-3 border-b border-zinc-150 bg-zinc-50/50 flex items-center justify-between">
                            <div class="flex items-center space-x-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-zinc-200"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-zinc-200"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-zinc-200"></span>
                            </div>
                            <div class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider">Workspace Live Preview</div>
                            <div class="h-4 w-4 rounded-full bg-zinc-200"></div>
                        </div>
                        
                        <!-- Preview Workspace Content -->
                        <div class="p-5 space-y-5">
                            <!-- Stats Preview -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-white p-4 rounded border border-zinc-150">
                                    <div class="text-[9px] text-zinc-400 font-semibold uppercase tracking-wider">Pipeline Valuation</div>
                                    <div class="text-sm font-semibold text-zinc-900 mt-0.5">Rs. 2,450,000.00</div>
                                </div>
                                <div class="bg-white p-4 rounded border border-zinc-150">
                                    <div class="text-[9px] text-zinc-400 font-semibold uppercase tracking-wider">Active Sent Deals</div>
                                    <div class="text-sm font-semibold text-zinc-900 mt-0.5">14</div>
                                </div>
                                <div class="bg-white p-4 rounded border border-zinc-150">
                                    <div class="text-[9px] text-zinc-400 font-semibold uppercase tracking-wider">Win Ratio</div>
                                    <div class="text-sm font-semibold text-zinc-900 mt-0.5">78%</div>
                                </div>
                            </div>

                            <!-- List Table Mockup -->
                            <div class="border border-zinc-150 rounded overflow-hidden">
                                <table class="w-full text-left border-collapse text-[10px] text-zinc-650">
                                    <thead>
                                        <tr class="bg-zinc-50 border-b border-zinc-150 font-semibold text-zinc-500 uppercase tracking-wider">
                                            <th class="p-2.5 pl-3">Client</th>
                                            <th class="p-2.5">Deal Details</th>
                                            <th class="p-2.5">Value</th>
                                            <th class="p-2.5 pr-3 text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-100 bg-white">
                                        <tr>
                                            <td class="p-2.5 pl-3 font-medium text-zinc-800">Acme Corporation</td>
                                            <td class="p-2.5">Enterprise Cloud Architecture Migration</td>
                                            <td class="p-2.5 font-medium text-zinc-900">Rs. 1,200,000.00</td>
                                            <td class="p-2.5 pr-3 text-right">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/50">Accepted</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="p-2.5 pl-3 font-medium text-zinc-800">Soylent Corp</td>
                                            <td class="p-2.5">Redundant Data Security & API Auditing</td>
                                            <td class="p-2.5 font-medium text-zinc-900">Rs. 850,000.00</td>
                                            <td class="p-2.5 pr-3 text-right">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-medium bg-blue-50 text-blue-700 border border-blue-200/50">Sent</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-zinc-200 bg-white py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-zinc-550 space-y-4 sm:space-y-0">
                <div>
                    &copy; Created by IsulaI 2026.
                </div>
                <div class="flex items-center space-x-1.5">
                    <span>Laravel v{{ laravelVersion }}</span>
                    <span>&middot;</span>
                    <span>PHP v{{ phpVersion }}</span>
                </div>
            </div>
        </footer>
    </div>
</template>
