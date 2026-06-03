<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const showingProfileDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-zinc-50 flex flex-col sm:flex-row">
            
            <!-- Mobile Navigation Bar (visible only on mobile viewports) -->
            <nav class="bg-white border-b border-zinc-200 sm:hidden w-full z-30">
                <div class="px-4 h-16 flex items-center justify-between">
                    <div class="shrink-0 flex items-center">
                        <Link :href="route('dashboard')" class="flex items-center gap-2">
                            <ApplicationLogo />
                            <span class="font-bold text-zinc-900 tracking-tight text-sm">Central CRM</span>
                        </Link>
                    </div>

                    <div class="flex items-center">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center p-2 rounded-md text-zinc-500 hover:text-zinc-700 hover:bg-zinc-105 focus:outline-none transition duration-150 ease-in-out"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Dropdown Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden border-t border-zinc-100 bg-white"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('customers.index')" :active="route().current('customers.*')">
                            Customers
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('proposals.index')" :active="route().current('proposals.*')">
                            Proposals
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('invoices.index')" :active="route().current('invoices.*')">
                            Invoices
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('transactions.index')" :active="route().current('transactions.*')">
                            Transactions
                        </ResponsiveNavLink>
                    </div>

                    <div class="pt-4 pb-4 border-t border-zinc-200">
                        <div class="px-4 mb-3">
                            <div class="font-semibold text-sm text-zinc-900">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-xs text-zinc-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')" :active="route().current('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Desktop Sticky Sidebar (visible only on desktop/tablet viewports) -->
            <aside class="hidden sm:flex sm:flex-col sm:w-64 bg-white border-r border-zinc-200 h-screen sticky top-0 shrink-0 z-20">
                <!-- Branding Header -->
                <div class="h-16 px-6 border-b border-zinc-200/60 flex items-center gap-3">
                    <Link :href="route('dashboard')" class="flex items-center gap-2.5">
                        <ApplicationLogo />
                        <span class="font-bold text-zinc-900 tracking-tight text-sm">Central CRM</span>
                    </Link>
                </div>

                <!-- Navigation Links Stack -->
                <nav class="flex-1 px-4 py-6 space-y-1.5 flex flex-col overflow-y-auto">
                    <Link
                        :href="route('dashboard')"
                        :class="[
                            route().current('dashboard')
                                ? 'bg-zinc-900 text-white font-semibold shadow-sm'
                                : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 font-medium',
                            'flex items-center gap-3 px-4 py-2.5 text-xs rounded-lg transition duration-200'
                        ]"
                    >
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </Link>

                    <Link
                        :href="route('customers.index')"
                        :class="[
                            route().current('customers.*')
                                ? 'bg-zinc-900 text-white font-semibold shadow-sm'
                                : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 font-medium',
                            'flex items-center gap-3 px-4 py-2.5 text-xs rounded-lg transition duration-200'
                        ]"
                    >
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Customers
                    </Link>

                    <Link
                        :href="route('proposals.index')"
                        :class="[
                            route().current('proposals.*')
                                ? 'bg-zinc-900 text-white font-semibold shadow-sm'
                                : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 font-medium',
                            'flex items-center gap-3 px-4 py-2.5 text-xs rounded-lg transition duration-200'
                        ]"
                    >
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Proposals
                    </Link>

                    <Link
                        :href="route('invoices.index')"
                        :class="[
                            route().current('invoices.*')
                                ? 'bg-zinc-900 text-white font-semibold shadow-sm'
                                : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 font-medium',
                            'flex items-center gap-3 px-4 py-2.5 text-xs rounded-lg transition duration-200'
                        ]"
                    >
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-6 2h6m-6 2h6m2 2H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Invoices
                    </Link>

                    <Link
                        :href="route('transactions.index')"
                        :class="[
                            route().current('transactions.*')
                                ? 'bg-zinc-900 text-white font-semibold shadow-sm'
                                : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 font-medium',
                            'flex items-center gap-3 px-4 py-2.5 text-xs rounded-lg transition duration-200'
                        ]"
                    >
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Transactions
                    </Link>
                </nav>

                <!-- Sidebar Footer User Profile Dropdown -->
                <div class="mt-auto p-4 border-t border-zinc-200/60 bg-white">
                    <div class="relative">
                        <button
                            type="button"
                            @click="showingProfileDropdown = !showingProfileDropdown"
                            class="w-full flex items-center justify-between px-3 py-2 text-left text-xs font-semibold rounded-lg text-zinc-750 hover:bg-zinc-50 hover:text-zinc-900 transition duration-150 ease-in-out focus:outline-none"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <!-- User Initials Avatar -->
                                <div class="h-8 w-8 rounded-full bg-zinc-900 flex items-center justify-center text-white text-[10px] font-bold shrink-0 uppercase">
                                    {{ $page.props.auth.user.name.charAt(0) }}
                                </div>
                                <div class="truncate text-left">
                                    <p class="font-semibold text-zinc-900 text-xs truncate leading-tight">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-zinc-400 text-[10px] truncate leading-tight">{{ $page.props.auth.user.email }}</p>
                                </div>
                            </div>
                            <svg class="h-4 w-4 text-zinc-400 shrink-0 ml-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <!-- Dropdown Background Overlay -->
                        <div v-show="showingProfileDropdown" class="fixed inset-0 z-40" @click="showingProfileDropdown = false"></div>

                        <!-- Dropdown Content (opens upwards) -->
                        <Transition
                            enter-active-class="transition ease-out duration-200"
                            enter-from-class="opacity-0 translate-y-2 scale-95"
                            enter-to-class="opacity-100 translate-y-0 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="opacity-100 translate-y-0 scale-100"
                            leave-to-class="opacity-0 translate-y-2 scale-95"
                        >
                            <div
                                v-show="showingProfileDropdown"
                                class="absolute bottom-full left-0 right-0 z-50 mb-2 bg-white border border-zinc-200 rounded-lg shadow-lg py-1 overflow-hidden"
                            >
                                <div class="px-4 py-2 border-b border-zinc-50 bg-zinc-50/50">
                                    <p class="font-semibold text-zinc-900 text-xs">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-zinc-400 text-[10px]">{{ $page.props.auth.user.email }}</p>
                                </div>
                                <Link
                                    :href="route('profile.edit')"
                                    class="block w-full px-4 py-2 text-left text-xs text-zinc-700 hover:bg-zinc-50 transition duration-150 font-medium"
                                    @click="showingProfileDropdown = false"
                                >
                                    Profile Settings
                                </Link>
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="block w-full px-4 py-2 text-left text-xs text-rose-600 hover:bg-rose-50/50 transition duration-150 font-semibold border-t border-zinc-100"
                                    @click="showingProfileDropdown = false"
                                >
                                    Log Out
                                </Link>
                            </div>
                        </Transition>
                    </div>
                </div>
            </aside>

            <!-- Main Page Content Section -->
            <div class="flex-1 flex flex-col min-w-0 bg-zinc-50/50">
                <!-- Page Header (if slot is provided) -->
                <header class="bg-white border-b border-zinc-200/80 py-6 px-6 sm:px-8" v-if="$slots.header">
                    <div class="max-w-7xl mx-auto">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Body Slot -->
                <main class="flex-1">
                    <slot />
                </main>
            </div>

        </div>
    </div>
</template>