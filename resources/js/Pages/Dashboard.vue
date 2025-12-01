<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Bảng Điều Khiển" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Bảng Điều Khiển
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Touch-optimized placeholder content -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                            <!-- Stats Cards -->
                            <div class="rounded-lg bg-blue-50 p-6 dark:bg-blue-900/20">
                                <h3 class="text-sm font-medium text-blue-600 dark:text-blue-400">Bệnh Nhân Hôm Nay</h3>
                                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">0</p>
                            </div>
                            <div class="rounded-lg bg-green-50 p-6 dark:bg-green-900/20">
                                <h3 class="text-sm font-medium text-green-600 dark:text-green-400">Lịch Hẹn</h3>
                                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">0</p>
                            </div>
                            <div class="rounded-lg bg-yellow-50 p-6 dark:bg-yellow-900/20">
                                <h3 class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Tơ Thuốc</h3>
                                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">0</p>
                            </div>
                            <div class="rounded-lg bg-purple-50 p-6 dark:bg-purple-900/20">
                                <h3 class="text-sm font-medium text-purple-600 dark:text-purple-400">Cuộc Khám</h3>
                                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">0</p>
                            </div>
                        </div>

                        <!-- Quick Actions - Touch optimized -->
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Thao Tác Nhanh</h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <Link 
                                    v-if="route().has('patients.create')"
                                    :href="route('patients.create')" 
                                    class="flex items-center justify-center rounded-lg bg-blue-600 px-6 py-4 text-white hover:bg-blue-700 transition-colors min-h-[60px] text-base font-medium"
                                >
                                    Bệnh Nhân Mới
                                </Link>
                                <button 
                                    v-else
                                    disabled
                                    class="flex items-center justify-center rounded-lg bg-gray-400 px-6 py-4 text-white min-h-[60px] text-base font-medium cursor-not-allowed"
                                >
                                    Lịch Hẹn Mới
                                </button>
                                <Link 
                                    v-if="route().has('encounters.create')"
                                    :href="route('encounters.create')" 
                                    class="flex items-center justify-center rounded-lg bg-purple-600 px-6 py-4 text-white hover:bg-purple-700 transition-colors min-h-[60px] text-base font-medium"
                                >
                                    Cuộc Khám Mới
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
