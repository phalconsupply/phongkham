<template>
    <Head title="Thêm Bệnh Nhân Mới" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Thêm Bệnh Nhân Mới
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-4xl px-4">
                <div class="bg-white shadow rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Thông Tin Cá Nhân -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold border-b pb-2">Thông Tin Cá Nhân</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Họ *</label>
                                    <input v-model="form.last_name" type="text" required 
                                           class="w-full px-3 py-2 border rounded-md" />
                                    <p v-if="form.errors.last_name" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.last_name }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Tên *</label>
                                    <input v-model="form.first_name" type="text" required 
                                           class="w-full px-3 py-2 border rounded-md" />
                                    <p v-if="form.errors.first_name" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.first_name }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Ngày Sinh *</label>
                                    <input v-model="form.date_of_birth" type="date" required 
                                           class="w-full px-3 py-2 border rounded-md" />
                                    <p v-if="form.errors.date_of_birth" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.date_of_birth }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Giới Tính *</label>
                                    <select v-model="form.gender" required 
                                            class="w-full px-3 py-2 border rounded-md">
                                        <option value="male">Nam</option>
                                        <option value="female">Nữ</option>
                                        <option value="other">Khác</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Số Điện Thoại</label>
                                    <input v-model="form.phone" type="tel" 
                                           class="w-full px-3 py-2 border rounded-md" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Email</label>
                                    <input v-model="form.email" type="email" 
                                           class="w-full px-3 py-2 border rounded-md" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Số CCCD/CMND</label>
                                    <input v-model="form.id_number" type="text" 
                                           class="w-full px-3 py-2 border rounded-md" />
                                </div>
                            </div>
                        </div>

                        <!-- Địa Chỉ -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold border-b pb-2">Địa Chỉ</h3>
                            
                            <div>
                                <label class="block text-sm font-medium mb-1">Địa Chỉ</label>
                                <textarea v-model="form.address" rows="2" 
                                          class="w-full px-3 py-2 border rounded-md"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Thành Phố</label>
                                <input v-model="form.city" type="text" 
                                       class="w-full px-3 py-2 border rounded-md" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Tỉnh/Thành</label>
                                <input v-model="form.province" type="text" 
                                       class="w-full px-3 py-2 border rounded-md" />
                            </div>
                        </div>

                        <!-- Thông Tin Y Tế -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold border-b pb-2">Thông Tin Y Tế</h3>
                            
                            <div>
                                <label class="block text-sm font-medium mb-1">Nhóm Máu</label>
                                <select v-model="form.blood_type" 
                                        class="w-full px-3 py-2 border rounded-md">
                                    <option value="">Chọn nhóm máu</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Tiền Sử Bệnh</label>
                                <textarea v-model="form.medical_history" rows="2" 
                                          class="w-full px-3 py-2 border rounded-md"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Dị Ứng</label>
                                <textarea v-model="form.allergies" rows="2" 
                                          class="w-full px-3 py-2 border rounded-md"></textarea>
                            </div>
                        </div>

                        <!-- Liên Hệ Khẩn Cấp -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold border-b pb-2">Liên Hệ Khẩn Cấp</h3>
                            
                            <div>
                                <label class="block text-sm font-medium mb-1">Tên Người Liên Hệ</label>
                                <input v-model="form.emergency_contact_name" type="text" 
                                       class="w-full px-3 py-2 border rounded-md" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Số Điện Thoại Khẩn Cấp</label>
                                <input v-model="form.emergency_contact_phone" type="tel" 
                                       class="w-full px-3 py-2 border rounded-md" />
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-4 pt-4 border-t">
                            <button type="submit" :disabled="form.processing"
                                    class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
                                {{ form.processing ? 'Đang lưu...' : 'Lưu Bệnh Nhân' }}
                            </button>
                            
                            <a href="/patients" 
                               class="px-6 py-3 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 flex items-center">
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
