<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    prescriptions: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const searchPrescriptions = () => {
    router.get(route('prescriptions.index'), { 
        search: search.value,
        status: status.value 
    }, {
        preserveState: true,
        replace: true,
    });
};

const deletePrescription = (prescription) => {
    if (confirm(`Bạn có chắc muốn xóa tơ thuốc ${prescription.prescription_code}?`)) {
        router.delete(route('prescriptions.destroy', prescription.id));
    }
};

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
        'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'dispensed': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    };
    return classes[status] || '';
};
</script>

<template>
    <Head title="Quản Lý Tơ Thuốc" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Quản Lý Tơ Thuốc
                </h2>
                <Link
                    :href="route('prescriptions.create')"
                    class="rounded-lg bg-blue-600 px-6 py-3 text-white hover:bg-blue-700 transition-colors min-h-[48px] flex items-center text-base font-medium"
                >
                    + Thêm Tơ Thuốc
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Search & Filter Bar -->
                <div class="mb-6 bg-white shadow-sm sm:rounded-lg p-6 dark:bg-gray-800">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <input
                            v-model="search"
                            @keyup.enter="searchPrescriptions"
                            type="text"
                            placeholder="Tìm kiếm theo mã tơ, bệnh nhân..."
                            class="col-span-2 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                        />
                        <select
                            v-model="status"
                            @change="searchPrescriptions"
                            class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                        >
                            <option value="">Tất Cả Trạng Thái</option>
                            <option value="pending">Chờ Xử Lý</option>
                            <option value="dispensed">Đã Cấp</option>
                            <option value="completed">Hoàn Thành</option>
                            <option value="cancelled">Đã Hủy</option>
                        </select>
                    </div>
                </div>

                <!-- Prescriptions Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Mã Tơ
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Bệnh Nhân
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Bác Sĩ
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Ngày Kê Tơ
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Số Thuốc
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Trạng Thái
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Thao Tác
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                <tr v-for="prescription in prescriptions.data" :key="prescription.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ prescription.prescription_code }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        {{ prescription.patient?.full_name || '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ prescription.doctor?.name || '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ new Date(prescription.prescription_date).toLocaleDateString('vi-VN') }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ prescription.items_count || 0 }} thuốc
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <span :class="getStatusClass(prescription.status)" class="px-3 py-1 rounded-full text-xs font-medium">
                                            {{ getStatusLabel(prescription.status) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium space-x-2">
                                        <Link
                                            :href="route('prescriptions.show', prescription.id)"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            Xem
                                        </Link>
                                        <Link
                                            :href="route('prescriptions.edit', prescription.id)"
                                            class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                        >
                                            Sửa
                                        </Link>
                                        <button
                                            @click="deletePrescription(prescription)"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            Xóa
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!prescriptions.data || prescriptions.data.length === 0">
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        Chưa có tơ thuốc nào. Nhấn "Thêm Tơ Thuốc" để bắt đầu.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="prescriptions.data && prescriptions.data.length > 0" class="border-t border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-800 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Hiển thị <span class="font-medium">{{ prescriptions.from }}</span> đến
                                <span class="font-medium">{{ prescriptions.to }}</span> trong tổng số
                                <span class="font-medium">{{ prescriptions.total }}</span> tơ thuốc
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    v-if="prescriptions.prev_page_url"
                                    :href="prescriptions.prev_page_url"
                                    class="rounded-lg bg-gray-600 px-4 py-2 text-white hover:bg-gray-700 min-h-[44px] flex items-center"
                                >
                                    « Trước
                                </Link>
                                <Link
                                    v-if="prescriptions.next_page_url"
                                    :href="prescriptions.next_page_url"
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
