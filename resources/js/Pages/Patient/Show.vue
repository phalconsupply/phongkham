<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    patient: Object,
});

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
    <Head title="Chi Tiết Bệnh Nhân" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Chi Tiết Bệnh Nhân
                </h2>
                <div class="flex gap-3">
                    <Link
                        :href="route('patients.edit', patient.id)"
                        class="rounded-lg bg-blue-600 px-6 py-3 text-white hover:bg-blue-700 transition-colors min-h-[48px] flex items-center"
                    >
                        Chỉnh Sửa
                    </Link>
                    <Link
                        :href="route('patients.index')"
                        class="rounded-lg bg-gray-600 px-6 py-3 text-white hover:bg-gray-700 transition-colors min-h-[48px] flex items-center"
                    >
                        Quay Lại
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 space-y-6">
                        <!-- Header Info -->
                        <div class="flex items-start justify-between pb-6 border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ patient.full_name }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Mã BN: {{ patient.patient_code }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Ngày tạo</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ new Date(patient.created_at).toLocaleDateString('vi-VN') }}
                                </p>
                            </div>
                        </div>

                        <!-- Thông Tin Cá Nhân -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Thông Tin Cá Nhân
                            </h4>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Ngày Sinh</p>
                                    <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">
                                        {{ new Date(patient.date_of_birth).toLocaleDateString('vi-VN') }}
                                        <span class="ml-2 text-sm text-gray-500">({{ patient.age }} tuổi)</span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Giới Tính</p>
                                    <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">
                                        {{ getGenderLabel(patient.gender) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Số Điện Thoại</p>
                                    <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">
                                        {{ patient.phone || '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                    <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">
                                        {{ patient.email || '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Nhóm Máu</p>
                                    <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">
                                        {{ patient.blood_type || '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Địa Chỉ -->
                        <div class="space-y-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Địa Chỉ
                            </h4>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Địa Chỉ</p>
                                    <p class="mt-1 text-base text-gray-900 dark:text-white">
                                        {{ patient.address || '-' }}
                                    </p>
                                </div>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Thành Phố</p>
                                        <p class="mt-1 text-base text-gray-900 dark:text-white">
                                            {{ patient.city || '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Tỉnh/Thành</p>
                                        <p class="mt-1 text-base text-gray-900 dark:text-white">
                                            {{ patient.province || '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Mã Bưu Chính</p>
                                        <p class="mt-1 text-base text-gray-900 dark:text-white">
                                            {{ patient.postal_code || '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thông Tin Bổ Sung -->
                        <div class="space-y-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Thông Tin Bổ Sung
                            </h4>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">CMND/CCCD</p>
                                    <p class="mt-1 text-base text-gray-900 dark:text-white">
                                        {{ patient.id_number || '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Số BHYT</p>
                                    <p class="mt-1 text-base text-gray-900 dark:text-white">
                                        {{ patient.insurance_number || '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Liên Hệ Khẩn Cấp -->
                        <div class="space-y-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Liên Hệ Khẩn Cấp
                            </h4>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Tên Người Liên Hệ</p>
                                    <p class="mt-1 text-base text-gray-900 dark:text-white">
                                        {{ patient.emergency_contact_name || '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Số Điện Thoại</p>
                                    <p class="mt-1 text-base text-gray-900 dark:text-white">
                                        {{ patient.emergency_contact_phone || '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Thông Tin Y Tế -->
                        <div class="space-y-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Thông Tin Y Tế
                            </h4>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Tiền Sử Bệnh</p>
                                    <p class="mt-1 text-base text-gray-900 dark:text-white whitespace-pre-wrap">
                                        {{ patient.medical_history || '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Dị Ứng</p>
                                    <p class="mt-1 text-base text-red-600 dark:text-red-400 whitespace-pre-wrap font-medium">
                                        {{ patient.allergies || 'Không có' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Ghi Chú</p>
                                    <p class="mt-1 text-base text-gray-900 dark:text-white whitespace-pre-wrap">
                                        {{ patient.notes || '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
