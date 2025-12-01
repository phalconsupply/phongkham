<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    encounter: Object,
});

const getStatusLabel = (status) => {
    const labels = {
        'scheduled': 'Đã Hẹn',
        'in_progress': 'Đang Khám',
        'completed': 'Hoàn Thành',
        'cancelled': 'Đã Hủy'
    };
    return labels[status] || status;
};

const getStatusClass = (status) => {
    const classes = {
        'scheduled': 'bg-blue-100 text-blue-800',
        'in_progress': 'bg-yellow-100 text-yellow-800',
        'completed': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800'
    };
    return classes[status] || '';
};

const getTypeLabel = (type) => {
    const labels = {
        'outpatient': 'Ngoại Trú',
        'inpatient': 'Nội Trú',
        'emergency': 'Cấp Cứu',
        'followup': 'Tái Khám'
    };
    return labels[type] || type;
};
</script>

<template>
    <Head title="Chi Tiết Cuộc Khám" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Chi Tiết Cuộc Khám: {{ encounter.encounter_code }}
                </h2>
                <div class="flex gap-3">
                    <Link
                        :href="route('encounters.edit', encounter.id)"
                        class="rounded-lg bg-green-600 px-6 py-3 text-white hover:bg-green-700 transition-colors min-h-[48px] flex items-center"
                    >
                        Chỉnh Sửa
                    </Link>
                    <Link
                        :href="route('encounters.index')"
                        class="rounded-lg bg-gray-600 px-6 py-3 text-white hover:bg-gray-700 transition-colors min-h-[48px] flex items-center"
                    >
                        ← Quay Lại
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <!-- Thông Tin Cơ Bản -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Thông Tin Cơ Bản</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Mã Cuộc Khám</p>
                                <p class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ encounter.encounter_code }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Trạng Thái</p>
                                <span :class="getStatusClass(encounter.status)" class="mt-1 inline-block px-3 py-1 rounded-full text-sm font-medium">
                                    {{ getStatusLabel(encounter.status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Bệnh Nhân</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ encounter.patient?.full_name || '-' }}</p>
                                <p class="text-sm text-gray-500">Mã BN: {{ encounter.patient?.patient_code }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Bác Sĩ</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ encounter.doctor?.name || '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Ngày Giờ Khám</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">
                                    {{ new Date(encounter.encounter_date).toLocaleString('vi-VN') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Loại Khám</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ getTypeLabel(encounter.encounter_type) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lý Do Khám -->
                <div v-if="encounter.chief_complaint || encounter.present_illness" class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Lý Do Khám</h3>
                        <div class="space-y-4">
                            <div v-if="encounter.chief_complaint">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Lý Do Chính</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ encounter.chief_complaint }}</p>
                            </div>
                            <div v-if="encounter.present_illness">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Bệnh Sử Hiện Tại</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ encounter.present_illness }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dấu Hiệu Sinh Tồn -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Dấu Hiệu Sinh Tồn</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div v-if="encounter.temperature">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nhiệt Độ</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ encounter.temperature }}°C</p>
                            </div>
                            <div v-if="encounter.blood_pressure_systolic && encounter.blood_pressure_diastolic">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Huyết Áp</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">
                                    {{ encounter.blood_pressure_systolic }}/{{ encounter.blood_pressure_diastolic }} mmHg
                                </p>
                            </div>
                            <div v-if="encounter.heart_rate">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nhịp Tim</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ encounter.heart_rate }} bpm</p>
                            </div>
                            <div v-if="encounter.respiratory_rate">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nhịp Thở</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ encounter.respiratory_rate }} lần/phút</p>
                            </div>
                            <div v-if="encounter.weight">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Cân Nặng</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ encounter.weight }} kg</p>
                            </div>
                            <div v-if="encounter.height">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Chiều Cao</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ encounter.height }} cm</p>
                            </div>
                            <div v-if="encounter.bmi">
                                <p class="text-sm text-gray-500 dark:text-gray-400">BMI</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ encounter.bmi }}</p>
                            </div>
                            <div v-if="encounter.spo2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">SpO2</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ encounter.spo2 }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Khám Lâm Sàng -->
                <div v-if="encounter.physical_examination || encounter.diagnosis || encounter.treatment_plan" class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Khám Lâm Sàng & Chẩn Đoán</h3>
                        <div class="space-y-4">
                            <div v-if="encounter.physical_examination">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Khám Thể Chất</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ encounter.physical_examination }}</p>
                            </div>
                            <div v-if="encounter.diagnosis">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Chẩn Đoán</p>
                                <p class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ encounter.diagnosis }}</p>
                            </div>
                            <div v-if="encounter.treatment_plan">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Kế Hoạch Điều Trị</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ encounter.treatment_plan }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ghi Chú -->
                <div v-if="encounter.notes" class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ghi Chú</h3>
                        <p class="text-base text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ encounter.notes }}</p>
                    </div>
                </div>

                <!-- Thời Gian -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Thông Tin Hệ Thống</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Ngày Tạo</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ new Date(encounter.created_at).toLocaleString('vi-VN') }}</p>
                            </div>
                            <div v-if="encounter.updated_at !== encounter.created_at">
                                <p class="text-gray-500 dark:text-gray-400">Cập Nhật Lần Cuối</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ new Date(encounter.updated_at).toLocaleString('vi-VN') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
