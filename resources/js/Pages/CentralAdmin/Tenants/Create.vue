<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    subdomain: '',
    clinic_name: '',
    admin_name: '',
    admin_email: '',
    admin_password: '',
    primary_color: '#3b82f6',
    logo: null,
});

const submit = () => {
    form.post(route('central.tenants.store'));
};
</script>

<template>
    <Head title="Tạo Phòng Khám" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Tạo Phòng Khám Mới
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Subdomain -->
                        <div>
                            <InputLabel for="subdomain" value="Tên Miền Phụ" />
                            <TextInput
                                id="subdomain"
                                v-model="form.subdomain"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                            />
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Sẽ truy cập tại: subdomain.localhost
                            </p>
                            <InputError class="mt-2" :message="form.errors.subdomain" />
                        </div>

                        <!-- Clinic Name -->
                        <div>
                            <InputLabel for="clinic_name" value="Tên Phòng Khám" />
                            <TextInput
                                id="clinic_name"
                                v-model="form.clinic_name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.clinic_name" />
                        </div>

                        <!-- Primary Color -->
                        <div>
                            <InputLabel for="primary_color" value="Màu Chủ Đạo" />
                            <div class="flex items-center gap-4 mt-1">
                                <input
                                    id="primary_color"
                                    v-model="form.primary_color"
                                    type="color"
                                    class="h-12 w-20 rounded border-gray-300 dark:border-gray-700"
                                />
                                <TextInput
                                    v-model="form.primary_color"
                                    type="text"
                                    class="flex-1"
                                />
                            </div>
                            <InputError class="mt-2" :message="form.errors.primary_color" />
                        </div>

                        <!-- Logo -->
                        <div>
                            <InputLabel for="logo" value="Logo (Tùy Chọn)" />
                            <input
                                id="logo"
                                type="file"
                                accept="image/*"
                                @input="form.logo = $event.target.files[0]"
                                class="mt-1 block w-full text-gray-900 dark:text-gray-100"
                            />
                            <InputError class="mt-2" :message="form.errors.logo" />
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <!-- Admin User Details -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tài Khoản Quản Trị</h3>

                            <div>
                                <InputLabel for="admin_name" value="Tên Quản Trị Viên" />
                                <TextInput
                                    id="admin_name"
                                    v-model="form.admin_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.admin_name" />
                            </div>

                            <div>
                                <InputLabel for="admin_email" value="Email Quản Trị Viên" />
                                <TextInput
                                    id="admin_email"
                                    v-model="form.admin_email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.admin_email" />
                            </div>

                            <div>
                                <InputLabel for="admin_password" value="Mật Khẩu Quản Trị Viên" />
                                <TextInput
                                    id="admin_password"
                                    v-model="form.admin_password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.admin_password" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <PrimaryButton :disabled="form.processing" class="min-h-[48px] px-6">
                                Tạo Phòng Khám
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
