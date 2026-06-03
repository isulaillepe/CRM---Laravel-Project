<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Create Account" />

    <div class="min-h-screen flex items-stretch bg-white">
        <!-- ═══════════════════════════════════════════ -->
        <!-- LEFT SIDE — Brand Sidebar                  -->
        <!-- ═══════════════════════════════════════════ -->
        <div
            class="hidden lg:flex lg:w-[46%] bg-zinc-900 text-white flex-col justify-between relative overflow-hidden"
        >
            <!-- Branding Block -->
            <div class="relative z-10 px-12 pt-12">
                <!-- Logo Mark -->
                <div class="flex items-center gap-3 mb-16">
                    <div
                        class="w-10 h-10 bg-white rounded-xl flex items-center justify-center"
                    >
                        <svg
                            class="w-5 h-5 text-zinc-900"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
                            />
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight"
                        >Workspace</span
                    >
                </div>

                <!-- Heading Stack -->
                <div class="space-y-4">
                    <h1
                        class="text-4xl xl:text-5xl font-extrabold leading-tight tracking-tight"
                    >
                        Your place to work
                    </h1>
                    <p
                        class="text-2xl xl:text-3xl font-bold text-zinc-400 tracking-tight"
                    >
                        Plan. Create. Control.
                    </p>
                </div>
            </div>

            <!-- Bottom Illustration -->
            <div class="relative z-10 px-12 pb-0">
                <svg
                    class="w-full max-w-md mx-auto opacity-20"
                    viewBox="0 0 400 280"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <!-- Kanban Board Illustration -->
                    <rect
                        x="40"
                        y="20"
                        width="320"
                        height="200"
                        rx="12"
                        stroke="white"
                        stroke-width="2"
                        fill="none"
                    />
                    <!-- Column Dividers -->
                    <line
                        x1="140"
                        y1="20"
                        x2="140"
                        y2="220"
                        stroke="white"
                        stroke-width="1"
                        opacity="0.5"
                    />
                    <line
                        x1="240"
                        y1="20"
                        x2="240"
                        y2="220"
                        stroke="white"
                        stroke-width="1"
                        opacity="0.5"
                    />

                    <!-- Column Headers -->
                    <rect
                        x="55"
                        y="35"
                        width="70"
                        height="20"
                        rx="4"
                        fill="white"
                        opacity="0.15"
                    />
                    <rect
                        x="155"
                        y="35"
                        width="70"
                        height="20"
                        rx="4"
                        fill="white"
                        opacity="0.15"
                    />
                    <rect
                        x="255"
                        y="35"
                        width="90"
                        height="20"
                        rx="4"
                        fill="white"
                        opacity="0.15"
                    />

                    <!-- To Do cards -->
                    <rect
                        x="55"
                        y="70"
                        width="70"
                        height="45"
                        rx="6"
                        fill="white"
                        opacity="0.1"
                    />
                    <rect
                        x="55"
                        y="125"
                        width="70"
                        height="45"
                        rx="6"
                        fill="white"
                        opacity="0.1"
                    />

                    <!-- In Progress cards -->
                    <rect
                        x="155"
                        y="70"
                        width="70"
                        height="60"
                        rx="6"
                        fill="white"
                        opacity="0.2"
                    />
                    <rect
                        x="155"
                        y="140"
                        width="70"
                        height="35"
                        rx="6"
                        fill="white"
                        opacity="0.1"
                    />

                    <!-- Done cards -->
                    <rect
                        x="255"
                        y="70"
                        width="90"
                        height="40"
                        rx="6"
                        fill="white"
                        opacity="0.08"
                    />
                    <rect
                        x="255"
                        y="120"
                        width="90"
                        height="50"
                        rx="6"
                        fill="white"
                        opacity="0.08"
                    />
                    <rect
                        x="255"
                        y="180"
                        width="90"
                        height="25"
                        rx="6"
                        fill="white"
                        opacity="0.08"
                    />

                    <!-- Person silhouette -->
                    <circle cx="200" cy="240" r="10" fill="white" opacity="0.15" />
                    <rect
                        x="192"
                        y="252"
                        width="16"
                        height="24"
                        rx="4"
                        fill="white"
                        opacity="0.12"
                    />
                </svg>
            </div>

            <!-- Subtle gradient overlay -->
            <div
                class="absolute inset-0 bg-gradient-to-b from-zinc-900 via-transparent to-zinc-950 pointer-events-none"
            ></div>
        </div>

        <!-- ═══════════════════════════════════════════ -->
        <!-- RIGHT SIDE — Auth Form Canvas              -->
        <!-- ═══════════════════════════════════════════ -->
        <div
            class="flex-1 flex items-center justify-center px-6 sm:px-12 lg:px-20 py-12"
        >
            <div class="w-full max-w-md">
                <!-- Mobile Logo (visible only below lg) -->
                <div class="flex items-center gap-3 mb-10 lg:hidden">
                    <div
                        class="w-10 h-10 bg-zinc-900 rounded-xl flex items-center justify-center"
                    >
                        <svg
                            class="w-5 h-5 text-white"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
                            />
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-zinc-900"
                        >Workspace</span
                    >
                </div>

                <!-- Header -->
                <h2
                    class="text-2xl sm:text-3xl font-bold text-zinc-900 tracking-tight text-center mb-8"
                >
                    Create an Account
                </h2>

                <!-- Registration Form -->
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Full Name Field -->
                    <div>
                        <label
                            for="register-name"
                            class="text-sm font-semibold text-zinc-700 tracking-wide mb-2 block"
                        >
                            Full Name
                        </label>
                        <input
                            id="register-name"
                            type="text"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="John Doe"
                            class="w-full h-12 px-4 text-base text-zinc-900 placeholder-zinc-400 bg-white border border-zinc-300 rounded-lg outline-none transition-all duration-200 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900"
                        />
                        <p
                            v-if="form.errors.name"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label
                            for="register-email"
                            class="text-sm font-semibold text-zinc-700 tracking-wide mb-2 block"
                        >
                            Email Address
                        </label>
                        <input
                            id="register-email"
                            type="email"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="you@company.com"
                            class="w-full h-12 px-4 text-base text-zinc-900 placeholder-zinc-400 bg-white border border-zinc-300 rounded-lg outline-none transition-all duration-200 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900"
                        />
                        <p
                            v-if="form.errors.email"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label
                            for="register-password"
                            class="text-sm font-semibold text-zinc-700 tracking-wide mb-2 block"
                        >
                            Password
                        </label>
                        <div class="relative">
                            <input
                                id="register-password"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                                class="w-full h-12 px-4 pr-12 text-base text-zinc-900 placeholder-zinc-400 bg-white border border-zinc-300 rounded-lg outline-none transition-all duration-200 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900"
                            />
                            <!-- Password Visibility Toggle -->
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-700 transition-colors focus:outline-none"
                                :aria-label="
                                    showPassword
                                        ? 'Hide password'
                                        : 'Show password'
                                "
                            >
                                <svg
                                    v-if="!showPassword"
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 001.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"
                                    />
                                </svg>
                            </button>
                        </div>
                        <p
                            v-if="form.errors.password"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <label
                            for="register-password-confirm"
                            class="text-sm font-semibold text-zinc-700 tracking-wide mb-2 block"
                        >
                            Confirm Password
                        </label>
                        <div class="relative">
                            <input
                                id="register-password-confirm"
                                :type="
                                    showConfirmPassword ? 'text' : 'password'
                                "
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                                class="w-full h-12 px-4 pr-12 text-base text-zinc-900 placeholder-zinc-400 bg-white border border-zinc-300 rounded-lg outline-none transition-all duration-200 focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900"
                            />
                            <!-- Confirm Password Visibility Toggle -->
                            <button
                                type="button"
                                @click="
                                    showConfirmPassword = !showConfirmPassword
                                "
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-700 transition-colors focus:outline-none"
                                :aria-label="
                                    showConfirmPassword
                                        ? 'Hide password'
                                        : 'Show password'
                                "
                            >
                                <svg
                                    v-if="!showConfirmPassword"
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 001.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"
                                    />
                                </svg>
                            </button>
                        </div>
                        <p
                            v-if="form.errors.password_confirmation"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full h-12 bg-zinc-900 text-white text-base font-semibold rounded-lg flex items-center justify-center gap-2 transition-all duration-200 hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed mt-2"
                    >
                        <span>Create Account</span>
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"
                            />
                        </svg>
                    </button>
                </form>

                <!-- Login Link -->
                <p class="mt-8 text-center text-sm text-zinc-500">
                    Already have an account?
                    <Link
                        :href="route('login')"
                        class="font-semibold text-zinc-900 hover:text-zinc-700 underline underline-offset-2 transition-colors"
                    >
                        Sign in
                    </Link>
                </p>
            </div>
        </div>
    </div>
</template>
