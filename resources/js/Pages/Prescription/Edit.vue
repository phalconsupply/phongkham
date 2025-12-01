<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    prescription: Object,
    patients: Array,
    doctors: Array,
    encounters: Array,
});

const form = useForm({
    patient_id: props.prescription.patient_id,
    encounter_id: props.prescription.encounter_id || '',
    doctor_id: props.prescription.doctor_id,
    prescription_date: props.prescription.prescription_date,
    diagnosis: props.prescription.diagnosis || '',
    status: props.prescription.status,
    notes: props.prescription.notes || '',
    items: props.prescription.items.map(item => ({
        medication_name: item.medication_name,
        dosage: item.dosage,
        frequency: item.frequency,
        duration: item.duration,
        route: item.route,
        quantity: item.quantity,
        unit: item.unit,
        instructions: item.instructions || ''
    }))
});

const addMedication = () => {
    form.items.push({
        medication_name: '',
        dosage: '',
        frequency: '',
        duration: '',
        route: 'oral',
        quantity: '',
        unit: 'viên',
        instructions: ''
    });
};

const removeMedication = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const submit = () => {
    form.put(route('prescriptions.update', props.prescription.id));
};
</script>

<template>
    <Head title="Chỉnh Sửa Tơ Thuốc" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Chỉnh Sửa Tơ Thuốc: {{ prescription.prescription_code }}
                </h2>
                <Link
                    :href="route('prescriptions.index')"
                    class="rounded-lg bg-gray-600 px-6 py-3 text-white hover:bg-gray-700 transition-colors min-h-[48px] flex items-center"
                >
                    ← Quay Lại
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <form @submit.prevent="submit" class="p-6">
                        <!-- Thông Tin Cơ Bản -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Thông Tin Cơ Bản</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Bệnh Nhân <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.patient_id"
                                        required
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                    >
                                        <option value="">-- Chọn Bệnh Nhân --</option>
                                        <option v-for="patient in patients" :key="patient.id" :value="patient.id">
                                            {{ patient.patient_code }} - {{ patient.full_name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.patient_id" class="text-sm text-red-600 mt-1">{{ form.errors.patient_id }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Cuộc Khám (Tùy Chọn)
                                    </label>
                                    <select
                                        v-model="form.encounter_id"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                    >
                                        <option value="">-- Không Liên Kết --</option>
                                        <option v-for="encounter in encounters" :key="encounter.id" :value="encounter.id">
                                            {{ encounter.encounter_code }} - {{ new Date(encounter.encounter_date).toLocaleDateString('vi-VN') }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.encounter_id" class="text-sm text-red-600 mt-1">{{ form.errors.encounter_id }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Bác Sĩ Kê Tơ <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.doctor_id"
                                        required
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                    >
                                        <option value="">-- Chọn Bác Sĩ --</option>
                                        <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                                            {{ doctor.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.doctor_id" class="text-sm text-red-600 mt-1">{{ form.errors.doctor_id }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Ngày Kê Tơ <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.prescription_date"
                                        type="date"
                                        required
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                    />
                                    <div v-if="form.errors.prescription_date" class="text-sm text-red-600 mt-1">{{ form.errors.prescription_date }}</div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Chẩn Đoán
                                    </label>
                                    <textarea
                                        v-model="form.diagnosis"
                                        rows="2"
                                        placeholder="Chẩn đoán bệnh..."
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    ></textarea>
                                    <div v-if="form.errors.diagnosis" class="text-sm text-red-600 mt-1">{{ form.errors.diagnosis }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Trạng Thái <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.status"
                                        required
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                    >
                                        <option value="pending">Chờ Xử Lý</option>
                                        <option value="dispensed">Đã Cấp</option>
                                        <option value="completed">Hoàn Thành</option>
                                        <option value="cancelled">Đã Hủy</option>
                                    </select>
                                    <div v-if="form.errors.status" class="text-sm text-red-600 mt-1">{{ form.errors.status }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Danh Sách Thuốc -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Danh Sách Thuốc</h3>
                                <button
                                    type="button"
                                    @click="addMedication"
                                    class="rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700 transition-colors min-h-[44px] flex items-center"
                                >
                                    + Thêm Thuốc
                                </button>
                            </div>

                            <div v-for="(item, index) in form.items" :key="index" class="mb-6 p-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-base font-medium text-gray-900 dark:text-gray-100">Thuốc #{{ index + 1 }}</h4>
                                    <button
                                        v-if="form.items.length > 1"
                                        type="button"
                                        @click="removeMedication(index)"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400"
                                    >
                                        Xóa
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Tên Thuốc <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            v-model="item.medication_name"
                                            type="text"
                                            required
                                            placeholder="VD: Paracetamol 500mg"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Liều Lượng <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            v-model="item.dosage"
                                            type="text"
                                            required
                                            placeholder="VD: 1 viên"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Tần Suất <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            v-model="item.frequency"
                                            type="text"
                                            required
                                            placeholder="VD: 3 lần/ngày"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Thời Gian <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            v-model="item.duration"
                                            type="text"
                                            required
                                            placeholder="VD: 5 ngày"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Đường Dùng <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            v-model="item.route"
                                            required
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                        >
                                            <option value="oral">Uống</option>
                                            <option value="injection">Tiêm</option>
                                            <option value="topical">Bôi Ngoài</option>
                                            <option value="inhalation">Hít</option>
                                            <option value="rectal">Đặt Hậu Môn</option>
                                            <option value="sublingual">Ngậm Dưới Lưỡi</option>
                                            <option value="other">Khác</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Số Lượng <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            v-model="item.quantity"
                                            type="number"
                                            step="0.01"
                                            required
                                            placeholder="VD: 15"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Đơn Vị <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            v-model="item.unit"
                                            required
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                        >
                                            <option value="viên">Viên</option>
                                            <option value="vỉ">Vỉ</option>
                                            <option value="hộp">Hộp</option>
                                            <option value="lọ">Lọ</option>
                                            <option value="ống">Ống</option>
                                            <option value="chai">Chai</option>
                                            <option value="túi">Túi</option>
                                            <option value="gói">Gói</option>
                                            <option value="ml">ml</option>
                                            <option value="mg">mg</option>
                                        </select>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Hướng Dẫn Sử Dụng
                                        </label>
                                        <textarea
                                            v-model="item.instructions"
                                            rows="2"
                                            placeholder="VD: Uống sau ăn, tránh dùng khi đói..."
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ghi Chú -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ghi Chú</h3>
                            <textarea
                                v-model="form.notes"
                                rows="3"
                                placeholder="Ghi chú thêm về tơ thuốc..."
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            ></textarea>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <Link
                                :href="route('prescriptions.index')"
                                class="rounded-lg bg-gray-300 dark:bg-gray-600 px-6 py-3 text-gray-700 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors min-h-[48px] flex items-center"
                            >
                                Hủy
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-lg bg-blue-600 px-6 py-3 text-white hover:bg-blue-700 transition-colors min-h-[48px] flex items-center disabled:opacity-50"
                            >
                                {{ form.processing ? 'Đang Cập Nhật...' : 'Cập Nhật Tơ Thuốc' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
