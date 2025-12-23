<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

// Temporarily disable QRScanner for debugging
// const QRScanner = defineAsyncComponent(() => import('@/Components/QRScanner.vue'));

const showQRScanner = ref(false);
const scanSuccess = ref(false);
const scanMessage = ref('');

const form = useForm({
    first_name: '',
    last_name: '',
    date_of_birth: '',
    gender: 'male',
    phone: '',
    email: '',
    address: '',
    city: '',
    province: '',
    postal_code: '',
    id_number: '',
    insurance_number: '',
    emergency_contact_name: '',
    emergency_contact_phone: '',
    notes: '',
    medical_history: '',
    allergies: '',
    blood_type: '',
});

// Handle QR scan success
const handleQRScan = (cccdData) => {
    // Fill form with CCCD data
    form.id_number = cccdData.id_number || form.id_number;
    form.first_name = cccdData.first_name || form.first_name;
    form.last_name = cccdData.last_name || form.last_name;
    form.date_of_birth = cccdData.date_of_birth || form.date_of_birth;
    form.gender = cccdData.gender || form.gender;
    form.address = cccdData.address || form.address;
    
    // Show success message
    scanSuccess.value = true;
    scanMessage.value = `✅ Đã quét thành công: ${cccdData.full_name}`;
    
    // Close scanner
    showQRScanner.value = false;
    
    // Hide success message after 5 seconds
    setTimeout(() => {
        scanSuccess.value = false;
    }, 5000);
};

// Handle QR scan error
const handleQRError = (error) => {
    console.error('QR Scan Error:', error);
};

const submit = () => {
    form.post(route('patients.store'));
};
</script>

<template>
    <Head title="Thêm Bệnh Nhân Mới" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Thêm Bệnh Nhân Mới
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div
                    v-if="scanSuccess"
                    class="mb-4 rounded-lg bg-green-100 p-4 text-green-800 dark:bg-green-900 dark:text-green-200"
                >
                    {{ scanMessage }}
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- QR Scanner Button (Temporarily disabled) -->
                        <!--
                        <div class="flex justify-end">
                            <button
                                type="button"
                                @click="showQRScanner = true"
                                class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-3 text-white hover:bg-blue-700 transition-colors min-h-[48px]"
                            >
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <span>Quét CCCD</span>
                            </button>
                        </div>
                        -->

                        <!-- Thông Tin Cá Nhân -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">
                                Thông Tin Cá Nhân
                            </h3>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <InputLabel for="last_name" value="Họ *" />
                                    <TextInput
                                        id="last_name"
                                        v-model="form.last_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.last_name" />
                                </div>

                                <div>
                                    <InputLabel for="first_name" value="Tên *" />
                                    <TextInput
                                        id="first_name"
                                        v-model="form.first_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.first_name" />
                                </div>

                                <div>
                                    <InputLabel for="date_of_birth" value="Ngày Sinh *" />
                                    <TextInput
                                        id="date_of_birth"
                                        v-model="form.date_of_birth"
                                        type="date"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.date_of_birth" />
                                </div>

                                <div>
                                    <InputLabel for="gender" value="Giới Tính *" />
                                    <select
                                        id="gender"
                                        v-model="form.gender"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 min-h-[48px]"
                                        required
                                    >
                                        <option value="male">Nam</option>
                                        <option value="female">Nữ</option>
                                        <option value="other">Khác</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.gender" />
                                </div>

                                <div>
                                    <InputLabel for="phone" value="Số Điện Thoại" />
                                    <TextInput
                                        id="phone"
                                        v-model="form.phone"
                                        type="tel"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.phone" />
                                </div>

                                <div>
                                    <InputLabel for="email" value="Email" />
                                    <TextInput
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.email" />
                                </div>
                            </div>
                        </div>

                        <!-- Địa Chỉ -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">
                                Địa Chỉ
                            </h3>

                            <div>
                                <InputLabel for="address" value="Địa Chỉ" />
                                <textarea
                                    id="address"
                                    v-model="form.address"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    rows="2"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.address" />
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                                <div>
                                    <InputLabel for="city" value="Thành Phố" />
                                    <TextInput
                                        id="city"
                                        v-model="form.city"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.city" />
                                </div>

                                <div>
                                    <InputLabel for="province" value="Tỉnh/Thành" />
                                    <TextInput
                                        id="province"
                                        v-model="form.province"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.province" />
                                </div>

                                <div>
                                    <InputLabel for="postal_code" value="Mã Bưu Chính" />
                                    <TextInput
                                        id="postal_code"
                                        v-model="form.postal_code"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.postal_code" />
                                </div>
                            </div>
                        </div>

                        <!-- Thông Tin Bổ Sung -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">
                                Thông Tin Bổ Sung
                            </h3>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                                <div>
                                    <InputLabel for="id_number" value="CMND/CCCD" />
                                    <TextInput
                                        id="id_number"
                                        v-model="form.id_number"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.id_number" />
                                </div>

                                <div>
                                    <InputLabel for="insurance_number" value="Số BHYT" />
                                    <TextInput
                                        id="insurance_number"
                                        v-model="form.insurance_number"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.insurance_number" />
                                </div>

                                <div>
                                    <InputLabel for="blood_type" value="Nhóm Máu" />
                                    <TextInput
                                        id="blood_type"
                                        v-model="form.blood_type"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder="A, B, AB, O"
                                    />
                                    <InputError class="mt-2" :message="form.errors.blood_type" />
                                </div>
                            </div>
                        </div>

                        <!-- Liên Hệ Khẩn Cấp -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">
                                Liên Hệ Khẩn Cấp
                            </h3>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <InputLabel for="emergency_contact_name" value="Tên Người Liên Hệ" />
                                    <TextInput
                                        id="emergency_contact_name"
                                        v-model="form.emergency_contact_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.emergency_contact_name" />
                                </div>

                                <div>
                                    <InputLabel for="emergency_contact_phone" value="Số Điện Thoại Liên Hệ" />
                                    <TextInput
                                        id="emergency_contact_phone"
                                        v-model="form.emergency_contact_phone"
                                        type="tel"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.emergency_contact_phone" />
                                </div>
                            </div>
                        </div>

                        <!-- Thông Tin Y Tế -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">
                                Thông Tin Y Tế
                            </h3>

                            <div>
                                <InputLabel for="medical_history" value="Tiền Sử Bệnh" />
                                <textarea
                                    id="medical_history"
                                    v-model="form.medical_history"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    rows="3"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.medical_history" />
                            </div>

                            <div>
                                <InputLabel for="allergies" value="Dị Ứng" />
                                <textarea
                                    id="allergies"
                                    v-model="form.allergies"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    rows="2"
                                    placeholder="Dị ứng thuốc, thức ăn, vật dụng..."
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.allergies" />
                            </div>

                            <div>
                                <InputLabel for="notes" value="Ghi Chú" />
                                <textarea
                                    id="notes"
                                    v-model="form.notes"

        <!-- QR Scanner Modal (Temporarily disabled for debugging) -->
        <!--
        <Suspense v-if="showQRScanner">
            <QRScanner
                :show="showQRScanner"
                @scanned="handleQRScan"
                @error="handleQRError"
                @close="showQRScanner = false"
            />
            <template #fallback>
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                    <div class="text-white">Đang tải...</div>
                </div>
            </template>
        </Suspense>
        -->
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    rows="3"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.notes" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4">
                            <a
                                :href="route('patients.index')"
                                class="rounded-lg bg-gray-600 px-6 py-3 text-white hover:bg-gray-700 transition-colors min-h-[48px] flex items-center"
                            >
                                Hủy
                            </a>
                            <PrimaryButton :disabled="form.processing" class="min-h-[48px] px-6">
                                Lưu Bệnh Nhân
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
