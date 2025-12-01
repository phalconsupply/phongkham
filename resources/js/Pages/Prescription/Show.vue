<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    prescription: Object,
});

const getStatusLabel = (status) => {
    const labels = {
        'pending': 'Chờ Xử Lý',
        'dispensed': 'Đã Cấp',
        'completed': 'Hoàn Thành',
        'cancelled': 'Đã Hủy'
    };
    return labels[status] || status;
};

const getStatusClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'dispensed': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800'
    };
    return classes[status] || '';
};

const getRouteLabel = (route) => {
    const labels = {
        'oral': 'Uống',
        'injection': 'Tiêm',
        'topical': 'Bôi Ngoài',
        'inhalation': 'Hít',
        'rectal': 'Đặt Hậu Môn',
        'sublingual': 'Ngậm Dưới Lưỡi',
        'other': 'Khác'
    };
    return labels[route] || route;
};
</script>

<template>
    <Head title="Chi Tiết Tơ Thuốc" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Chi Tiết Tơ Thuốc: {{ prescription.prescription_code }}
                </h2>
                <div class="flex gap-3">
                    <Link
                        :href="route('prescriptions.edit', prescription.id)"
                        class="rounded-lg bg-green-600 px-6 py-3 text-white hover:bg-green-700 transition-colors min-h-[48px] flex items-center"
                    >
                        Chỉnh Sửa
                    </Link>
                    <Link
                        :href="route('prescriptions.index')"
                        class="rounded-lg bg-gray-600 px-6 py-3 text-white hover:bg-gray-700 transition-colors min-h-[48px] flex items-center"
                    >
                        ← Quay Lại
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <!-- Thông Tin Cơ Bản -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Thông Tin Cơ Bản</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Mã Tơ Thuốc</p>
                                <p class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ prescription.prescription_code }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Trạng Thái</p>
                                <span :class="getStatusClass(prescription.status)" class="mt-1 inline-block px-3 py-1 rounded-full text-sm font-medium">
                                    {{ getStatusLabel(prescription.status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Bệnh Nhân</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ prescription.patient?.full_name || '-' }}</p>
                                <p class="text-sm text-gray-500">Mã BN: {{ prescription.patient?.patient_code }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Bác Sĩ Kê Tơ</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ prescription.doctor?.name || '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Ngày Kê Tơ</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">
                                    {{ new Date(prescription.prescription_date).toLocaleDateString('vi-VN') }}
                                </p>
                            </div>
                            <div v-if="prescription.encounter">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Liên Kết Cuộc Khám</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ prescription.encounter.encounter_code }}</p>
                            </div>
                        </div>

                        <div v-if="prescription.diagnosis" class="mt-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Chẩn Đoán</p>
                            <p class="mt-1 text-base text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ prescription.diagnosis }}</p>
                        </div>
                    </div>
                </div>

                <!-- Danh Sách Thuốc -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Danh Sách Thuốc</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            #
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Tên Thuốc
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Liều Lượng
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Tần Suất
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Thời Gian
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Đường Dùng
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Số Lượng
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                    <tr v-for="(item, index) in prescription.items" :key="item.id">
                                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ index + 1 }}
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ item.medication_name }}
                                            <p v-if="item.instructions" class="mt-1 text-xs text-gray-500 dark:text-gray-400 italic">
                                                {{ item.instructions }}
                                            </p>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ item.dosage }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ item.frequency }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ item.duration }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ getRouteLabel(item.route) }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ item.quantity }} {{ item.unit }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Ghi Chú -->
                <div v-if="prescription.notes" class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ghi Chú</h3>
                        <p class="text-base text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ prescription.notes }}</p>
                    </div>
                </div>

                <!-- Thời Gian -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Thông Tin Hệ Thống</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Ngày Tạo</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ new Date(prescription.created_at).toLocaleString('vi-VN') }}</p>
                            </div>
                            <div v-if="prescription.updated_at !== prescription.created_at">
                                <p class="text-gray-500 dark:text-gray-400">Cập Nhật Lần Cuối</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ new Date(prescription.updated_at).toLocaleString('vi-VN') }}</p>
                            </div>
                            <div v-if="prescription.dispensed_at">
                                <p class="text-gray-500 dark:text-gray-400">Ngày Cấp Thuốc</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ new Date(prescription.dispensed_at).toLocaleString('vi-VN') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
