<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    tenants: Array,
});
</script>

<template>
    <Head title="Quản Lý Phòng Khám" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Quản Lý Phòng Khám
                </h2>
                <Link
                    :href="route('central.tenants.create')"
                    class="rounded-lg bg-blue-600 px-6 py-3 text-white hover:bg-blue-700 transition-colors min-h-[48px] flex items-center text-base font-medium"
                >
                    Tạo Phòng Khám Mới
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <div class="space-y-4">
                            <div
                                v-for="tenant in tenants"
                                :key="tenant.id"
                                class="rounded-lg border border-gray-200 p-6 dark:border-gray-700"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ tenant.id }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Domain: {{ tenant.domains[0]?.domain }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div
                                v-if="!tenants || tenants.length === 0"
                                class="text-center py-8 text-gray-600 dark:text-gray-400"
                            >
                                Chưa có phòng khám nào. Tạo phòng khám đầu tiên để bắt đầu.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
