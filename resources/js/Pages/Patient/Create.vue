<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

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
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
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
