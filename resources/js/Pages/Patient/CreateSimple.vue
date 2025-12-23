<template>
    <Head title="Thêm Bệnh Nhân Mới" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Thêm Bệnh Nhân Mới (Simple Version)
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-2xl px-4">
                <div class="bg-white shadow rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- First Name -->
                        <div>
                            <label class="block text-sm font-medium mb-1">Họ *</label>
                            <input
                                v-model="form.first_name"
                                type="text"
                                required
                                class="w-full px-3 py-2 border rounded-md"
                            />
                            <p v-if="form.errors.first_name" class="text-red-600 text-sm mt-1">
                                {{ form.errors.first_name }}
                            </p>
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label class="block text-sm font-medium mb-1">Tên *</label>
                            <input
                                v-model="form.last_name"
                                type="text"
                                required
                                class="w-full px-3 py-2 border rounded-md"
                            />
                            <p v-if="form.errors.last_name" class="text-red-600 text-sm mt-1">
                                {{ form.errors.last_name }}
                            </p>
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label class="block text-sm font-medium mb-1">Ngày Sinh *</label>
                            <input
                                v-model="form.date_of_birth"
                                type="date"
                                required
                                class="w-full px-3 py-2 border rounded-md"
                            />
                            <p v-if="form.errors.date_of_birth" class="text-red-600 text-sm mt-1">
                                {{ form.errors.date_of_birth }}
                            </p>
                        </div>

                        <!-- Gender -->
                        <div>
                            <label class="block text-sm font-medium mb-1">Giới Tính *</label>
                            <select
                                v-model="form.gender"
                                required
                                class="w-full px-3 py-2 border rounded-md"
                            >
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-4 pt-4">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                            >
                                {{ form.processing ? 'Đang lưu...' : 'Lưu Bệnh Nhân' }}
                            </button>
                            
                            <a
                                href="/patients"
                                class="px-6 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                            >
                                Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

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
