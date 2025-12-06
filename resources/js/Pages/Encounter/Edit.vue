<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ICD10Search from '@/Components/ICD10Search.vue';

const props = defineProps({
    encounter: Object,
    patients: Array,
    doctors: Array,
});

const form = useForm({
    patient_id: props.encounter.patient_id,
    doctor_id: props.encounter.doctor_id,
    encounter_date: props.encounter.encounter_date.slice(0, 16),
    encounter_type: props.encounter.encounter_type,
    status: props.encounter.status,
    chief_complaint: props.encounter.chief_complaint || '',
    present_illness: props.encounter.present_illness || '',
    temperature: props.encounter.temperature || '',
    blood_pressure_systolic: props.encounter.blood_pressure_systolic || '',
    blood_pressure_diastolic: props.encounter.blood_pressure_diastolic || '',
    heart_rate: props.encounter.heart_rate || '',
    respiratory_rate: props.encounter.respiratory_rate || '',
    weight: props.encounter.weight || '',
    height: props.encounter.height || '',
    spo2: props.encounter.spo2 || '',
    physical_examination: props.encounter.physical_examination || '',
    diagnosis: props.encounter.diagnosis || '',
    icd10_code_id: props.encounter.icd10_code_id || null,
    treatment_plan: props.encounter.treatment_plan || '',
    notes: props.encounter.notes || '',
});

const submit = () => {
    form.put(route('encounters.update', props.encounter.id));
};
</script>

<template>
    <Head title="Chỉnh Sửa Cuộc Khám" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Chỉnh Sửa Cuộc Khám: {{ encounter.encounter_code }}
                </h2>
                <Link
                    :href="route('encounters.index')"
                    class="rounded-lg bg-gray-600 px-6 py-3 text-white hover:bg-gray-700 transition-colors min-h-[48px] flex items-center"
                >
                    ← Quay Lại
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
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
                                        Bác Sĩ <span class="text-red-500">*</span>
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
                                        Ngày Giờ Khám <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.encounter_date"
                                        type="datetime-local"
                                        required
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                    />
                                    <div v-if="form.errors.encounter_date" class="text-sm text-red-600 mt-1">{{ form.errors.encounter_date }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Loại Khám <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.encounter_type"
                                        required
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                    >
                                        <option value="outpatient">Ngoại Trú</option>
                                        <option value="inpatient">Nội Trú</option>
                                        <option value="emergency">Cấp Cứu</option>
                                        <option value="followup">Tái Khám</option>
                                    </select>
                                    <div v-if="form.errors.encounter_type" class="text-sm text-red-600 mt-1">{{ form.errors.encounter_type }}</div>
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
                                        <option value="scheduled">Đã Hẹn</option>
                                        <option value="in_progress">Đang Khám</option>
                                        <option value="completed">Hoàn Thành</option>
                                        <option value="cancelled">Đã Hủy</option>
                                    </select>
                                    <div v-if="form.errors.status" class="text-sm text-red-600 mt-1">{{ form.errors.status }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Lý Do Khám -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Lý Do Khám</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Lý Do Chính
                                    </label>
                                    <textarea
                                        v-model="form.chief_complaint"
                                        rows="3"
                                        placeholder="Mô tả triệu chứng chính..."
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    ></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Bệnh Sử Hiện Tại
                                    </label>
                                    <textarea
                                        v-model="form.present_illness"
                                        rows="3"
                                        placeholder="Mô tả chi tiết về tình trạng hiện tại..."
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Dấu Hiệu Sinh Tồn -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Dấu Hiệu Sinh Tồn</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nhiệt Độ (°C)
                                    </label>
                                    <input
                                        v-model="form.temperature"
                                        type="number"
                                        step="0.1"
                                        placeholder="36.5"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        HA Tâm Thu (mmHg)
                                    </label>
                                    <input
                                        v-model="form.blood_pressure_systolic"
                                        type="number"
                                        placeholder="120"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        HA Tâm Trương (mmHg)
                                    </label>
                                    <input
                                        v-model="form.blood_pressure_diastolic"
                                        type="number"
                                        placeholder="80"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nhịp Tim (bpm)
                                    </label>
                                    <input
                                        v-model="form.heart_rate"
                                        type="number"
                                        placeholder="72"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nhịp Thở (lần/phút)
                                    </label>
                                    <input
                                        v-model="form.respiratory_rate"
                                        type="number"
                                        placeholder="16"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Cân Nặng (kg)
                                    </label>
                                    <input
                                        v-model="form.weight"
                                        type="number"
                                        step="0.1"
                                        placeholder="65.5"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Chiều Cao (cm)
                                    </label>
                                    <input
                                        v-model="form.height"
                                        type="number"
                                        step="0.1"
                                        placeholder="170"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        SpO2 (%)
                                    </label>
                                    <input
                                        v-model="form.spo2"
                                        type="number"
                                        step="0.1"
                                        placeholder="98"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 min-h-[48px]"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Khám Lâm Sàng -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Khám Lâm Sàng & Chẩn Đoán</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Khám Thể Chất
                                    </label>
                                    <textarea
                                        v-model="form.physical_examination"
                                        rows="3"
                                        placeholder="Kết quả khám thể chất..."
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    ></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Chẩn Đoán
                                    </label>
                                    <textarea
                                        v-model="form.diagnosis"
                                        rows="3"
                                        placeholder="Chẩn đoán bệnh..."
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    ></textarea>
                                </div>

                                <div>
                                    <ICD10Search
                                        v-model="form.icd10_code_id"
                                        :patient-id="form.patient_id"
                                        label="Mã ICD-10 Chẩn Đoán"
                                        :error-message="form.errors.icd10_code_id"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Kế Hoạch Điều Trị
                                    </label>
                                    <textarea
                                        v-model="form.treatment_plan"
                                        rows="3"
                                        placeholder="Kế hoạch điều trị..."
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    ></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Ghi Chú
                                    </label>
                                    <textarea
                                        v-model="form.notes"
                                        rows="2"
                                        placeholder="Ghi chú thêm..."
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <Link
                                :href="route('encounters.index')"
                                class="rounded-lg bg-gray-300 dark:bg-gray-600 px-6 py-3 text-gray-700 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors min-h-[48px] flex items-center"
                            >
                                Hủy
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-lg bg-blue-600 px-6 py-3 text-white hover:bg-blue-700 transition-colors min-h-[48px] flex items-center disabled:opacity-50"
                            >
                                {{ form.processing ? 'Đang Cập Nhật...' : 'Cập Nhật Cuộc Khám' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
