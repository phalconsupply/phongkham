<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    roles: Array,
    permissions: Object,
});

const selectedRole = ref(null);
const showCreatePermissionModal = ref(false);
const showCreateRoleModal = ref(false);

// Form for updating role permissions
const rolePermissionsForm = useForm({
    permissions: [],
});

// Form for creating new permission
const newPermissionForm = useForm({
    name: '',
    guard_name: 'web',
});

// Form for creating new role
const newRoleForm = useForm({
    name: '',
    guard_name: 'web',
});

// Select a role to manage
const selectRole = (role) => {
    selectedRole.value = role;
    rolePermissionsForm.permissions = role.permissions.map(p => p.name);
};

// Check if permission is assigned to selected role
const hasPermission = (permissionName) => {
    return rolePermissionsForm.permissions.includes(permissionName);
};

// Toggle permission for role
const togglePermission = (permissionName) => {
    const index = rolePermissionsForm.permissions.indexOf(permissionName);
    if (index > -1) {
        rolePermissionsForm.permissions.splice(index, 1);
    } else {
        rolePermissionsForm.permissions.push(permissionName);
    }
};

// Save role permissions
const saveRolePermissions = () => {
    if (!selectedRole.value) return;
    
    rolePermissionsForm.post(route('central.roles.permissions.update', selectedRole.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Reload permissions after update
        },
    });
};

// Create new permission
const createPermission = () => {
    newPermissionForm.post(route('central.permissions.create'), {
        preserveScroll: true,
        onSuccess: () => {
            newPermissionForm.reset();
            showCreatePermissionModal.value = false;
        },
    });
};

// Create new role
const createRole = () => {
    newRoleForm.post(route('central.roles.create'), {
        preserveScroll: true,
        onSuccess: () => {
            newRoleForm.reset();
            showCreateRoleModal.value = false;
        },
    });
};

// Delete permission
const deletePermission = (permissionId) => {
    if (confirm('Are you sure you want to delete this permission?')) {
        useForm({}).delete(route('central.permissions.delete', permissionId), {
            preserveScroll: true,
        });
    }
};

// Delete role
const deleteRole = (roleId) => {
    if (confirm('Are you sure you want to delete this role?')) {
        useForm({}).delete(route('central.roles.delete', roleId), {
            preserveScroll: true,
            onSuccess: () => {
                if (selectedRole.value?.id === roleId) {
                    selectedRole.value = null;
                }
            },
        });
    }
};

// Get role badge color
const getRoleBadgeColor = (roleName) => {
    const colors = {
        admin: 'bg-red-100 text-red-800',
        doctor: 'bg-blue-100 text-blue-800',
        nurse: 'bg-green-100 text-green-800',
        receptionist: 'bg-yellow-100 text-yellow-800',
    };
    return colors[roleName] || 'bg-gray-100 text-gray-800';
};

// Check if role is protected
const isProtectedRole = (roleName) => {
    return ['admin', 'doctor', 'nurse', 'receptionist'].includes(roleName);
};
</script>

<template>
    <Head title="Quản Lý Phân Quyền" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Quản Lý Roles & Permissions
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        
                        <!-- Action Buttons -->
                        <div class="mb-6 flex justify-between">
                            <div>
                                <h3 class="text-lg font-medium">Roles & Permissions Management</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Quản lý phân quyền cho các vai trò trong hệ thống</p>
                            </div>
                            <div class="flex gap-2">
                                <button
                                    @click="showCreateRoleModal = true"
                                    class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                                >
                                    + Thêm Role
                                </button>
                                <button
                                    @click="showCreatePermissionModal = true"
                                    class="rounded-md bg-green-600 px-4 py-2 text-white hover:bg-green-700"
                                >
                                    + Thêm Permission
                                </button>
                            </div>
                        </div>

                        <!-- Grid Layout -->
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                            
                            <!-- Left: Roles List -->
                            <div class="lg:col-span-1">
                                <div class="rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="border-b border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                                        <h4 class="font-semibold">Danh Sách Roles</h4>
                                    </div>
                                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <div
                                            v-for="role in roles"
                                            :key="role.id"
                                            @click="selectRole(role)"
                                            class="cursor-pointer p-4 hover:bg-gray-50 dark:hover:bg-gray-700"
                                            :class="{
                                                'bg-blue-50 dark:bg-blue-900': selectedRole?.id === role.id
                                            }"
                                        >
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span :class="['rounded-full px-3 py-1 text-xs font-medium', getRoleBadgeColor(role.name)]">
                                                        {{ role.name }}
                                                    </span>
                                                    <p class="mt-1 text-xs text-gray-500">
                                                        {{ role.permissions.length }} permissions
                                                    </p>
                                                </div>
                                                <button
                                                    v-if="!isProtectedRole(role.name)"
                                                    @click.stop="deleteRole(role.id)"
                                                    class="text-red-600 hover:text-red-800"
                                                >
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Permissions Management -->
                            <div class="lg:col-span-2">
                                <div v-if="selectedRole" class="rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="border-b border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                                        <div class="flex items-center justify-between">
                                            <h4 class="font-semibold">
                                                Permissions cho: <span class="text-blue-600">{{ selectedRole.name }}</span>
                                            </h4>
                                            <button
                                                @click="saveRolePermissions"
                                                :disabled="rolePermissionsForm.processing"
                                                class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700 disabled:opacity-50"
                                            >
                                                {{ rolePermissionsForm.processing ? 'Đang lưu...' : 'Lưu Thay Đổi' }}
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4">
                                        <!-- Permissions grouped by module -->
                                        <div v-for="(perms, module) in permissions" :key="module" class="mb-6">
                                            <h5 class="mb-3 text-sm font-semibold uppercase text-gray-700 dark:text-gray-300">
                                                {{ module }}
                                            </h5>
                                            <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                                <div
                                                    v-for="permission in perms"
                                                    :key="permission.id"
                                                    class="flex items-center gap-2"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        :id="'perm-' + permission.id"
                                                        :checked="hasPermission(permission.name)"
                                                        @change="togglePermission(permission.name)"
                                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                    />
                                                    <label
                                                        :for="'perm-' + permission.id"
                                                        class="flex-1 cursor-pointer text-sm"
                                                    >
                                                        {{ permission.name }}
                                                    </label>
                                                    <button
                                                        @click="deletePermission(permission.id)"
                                                        class="text-red-500 hover:text-red-700"
                                                        title="Xóa permission"
                                                    >
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="rounded-lg border border-gray-200 p-8 text-center dark:border-gray-700">
                                    <p class="text-gray-500">Chọn một role để quản lý permissions</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Create Permission Modal -->
        <div v-if="showCreatePermissionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="w-full max-w-md rounded-lg bg-white p-6 dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold">Tạo Permission Mới</h3>
                <form @submit.prevent="createPermission">
                    <div class="mb-4">
                        <label class="mb-1 block text-sm font-medium">Permission Name</label>
                        <input
                            v-model="newPermissionForm.name"
                            type="text"
                            placeholder="e.g., patient.view"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-700"
                            required
                        />
                        <p class="mt-1 text-xs text-gray-500">Format: module.action (e.g., patient.view, patient.edit)</p>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            @click="showCreatePermissionModal = false"
                            class="rounded-md bg-gray-200 px-4 py-2 hover:bg-gray-300 dark:bg-gray-700"
                        >
                            Hủy
                        </button>
                        <button
                            type="submit"
                            :disabled="newPermissionForm.processing"
                            class="rounded-md bg-green-600 px-4 py-2 text-white hover:bg-green-700 disabled:opacity-50"
                        >
                            Tạo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Create Role Modal -->
        <div v-if="showCreateRoleModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="w-full max-w-md rounded-lg bg-white p-6 dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold">Tạo Role Mới</h3>
                <form @submit.prevent="createRole">
                    <div class="mb-4">
                        <label class="mb-1 block text-sm font-medium">Role Name</label>
                        <input
                            v-model="newRoleForm.name"
                            type="text"
                            placeholder="e.g., pharmacist"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-700"
                            required
                        />
                    </div>
                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            @click="showCreateRoleModal = false"
                            class="rounded-md bg-gray-200 px-4 py-2 hover:bg-gray-300 dark:bg-gray-700"
                        >
                            Hủy
                        </button>
                        <button
                            type="submit"
                            :disabled="newRoleForm.processing"
                            class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            Tạo
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
