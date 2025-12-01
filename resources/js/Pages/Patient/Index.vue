<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    patients: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

const searchPatients = () => {
    router.get(route('patients.index'), { search: search.value }, {
        preserveState: true,
        replace: true,
    });
};

const deletePatient = (patient) => {
    if (confirm(`Bạn có chắc muốn xóa bệnh nhân ${patient.full_name}?`)) {
        router.delete(route('patients.destroy', patient.id));
    }
};

const getGenderLabel = (gender) => {
    const labels = {
        'male': 'Nam',
        'female': 'Nữ',
        'other': 'Khác'
    };
    return labels[gender] || gender;
};
</script>

<template>
    <Head title="Quản Lý Bệnh Nhân" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Quản Lý Bệnh Nhân
                </h2>
                <Link
                    :href="route('patients.create')"
                    class="rounded-lg bg-blue-600 px-6 py-3 text-white hover:bg-blue-700 transition-colors min-h-[48px] flex items-center text-base font-medium"
                >
                    + Thêm Bệnh Nhân
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Search Bar -->
                <div class="mb-6 bg-white shadow-sm sm:rounded-lg p-6 dark:bg-gray-800">
                    <div class="flex gap-4">
                        <input
                            v-model="search"
                            @keyup.enter="searchPatients"
                            type="text"
                            placeholder="Tìm kiếm theo tên, mã BN, số điện thoại..."
                            class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                        />
                        <button
                            @click="searchPatients"
                            class="rounded-lg bg-gray-600 px-6 py-3 text-white hover:bg-gray-700 transition-colors min-h-[48px] flex items-center"
                        >
                            Tìm Kiếm
                        </button>
                    </div>
                </div>

                <!-- Patients Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Mã BN
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Họ Tên
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Ngày Sinh
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Giới Tính
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Số Điện Thoại
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Thao Tác
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                <tr v-for="patient in patients.data" :key="patient.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ patient.patient_code }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        {{ patient.full_name }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ new Date(patient.date_of_birth).toLocaleDateString('vi-VN') }}
                                        <span class="ml-2 text-xs text-gray-400">({{ patient.age }} tuổi)</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ getGenderLabel(patient.gender) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ patient.phone || '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium space-x-2">
                                        <Link
                                            :href="route('patients.show', patient.id)"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            Xem
                                        </Link>
                                        <Link
                                            :href="route('patients.edit', patient.id)"
                                            class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                        >
                                            Sửa
                                        </Link>
                                        <button
                                            @click="deletePatient(patient)"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            Xóa
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!patients.data || patients.data.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        Chưa có bệnh nhân nào. Nhấn "Thêm Bệnh Nhân" để bắt đầu.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="patients.data && patients.data.length > 0" class="border-t border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-800 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Hiển thị <span class="font-medium">{{ patients.from }}</span> đến
                                <span class="font-medium">{{ patients.to }}</span> trong tổng số
                                <span class="font-medium">{{ patients.total }}</span> bệnh nhân
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    v-if="patients.prev_page_url"
                                    :href="patients.prev_page_url"
                                    class="rounded-lg bg-gray-600 px-4 py-2 text-white hover:bg-gray-700 min-h-[44px] flex items-center"
                                >
                                    « Trước
                                </Link>
                                <Link
                                    v-if="patients.next_page_url"
                                    :href="patients.next_page_url"
                                    class="rounded-lg bg-gray-600 px-4 py-2 text-white hover:bg-gray-700 min-h-[44px] flex items-center"
                                >
                                    Sau »
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
