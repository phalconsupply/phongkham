# Role & Permission Management System

## Tổng Quan

Hệ thống quản lý phân quyền cho phép **Central Admin** kiểm soát đầy đủ các quyền truy cập của users theo vai trò (roles).

## Đặc Điểm

### ✅ Tính Năng Đã Triển Khai

1. **Quản lý Roles**
   - Xem danh sách tất cả roles
   - Tạo role mới
   - Xóa role (trừ roles core: admin, doctor, nurse, receptionist)
   - Badge màu sắc phân biệt role

2. **Quản lý Permissions**
   - Xem permissions theo nhóm module
   - Tạo permission mới
   - Xóa permission
   - Format chuẩn: `module.action` (e.g., `patient.view`, `patient.edit`)

3. **Assign Permissions to Roles**
   - Interface checkbox trực quan
   - Lưu thay đổi real-time
   - Group permissions theo module

4. **Bảo Mật**
   - Chỉ Central Admin có quyền truy cập
   - Protected routes với middleware
   - Không thể xóa core roles

## Permissions Mặc Định

### Patient Module
- `patient.view` - Xem danh sách bệnh nhân
- `patient.create` - Tạo bệnh nhân mới
- `patient.edit` - Chỉnh sửa thông tin bệnh nhân
- `patient.delete` - Xóa bệnh nhân

### Encounter Module
- `encounter.view` - Xem phiếu khám
- `encounter.create` - Tạo phiếu khám mới
- `encounter.edit` - Chỉnh sửa phiếu khám
- `encounter.delete` - Xóa phiếu khám

### Prescription Module
- `prescription.view` - Xem đơn thuốc
- `prescription.create` - Kê đơn thuốc
- `prescription.edit` - Chỉnh sửa đơn thuốc
- `prescription.delete` - Xóa đơn thuốc

### Appointment Module (Future)
- `appointment.view` - Xem lịch hẹn
- `appointment.create` - Tạo lịch hẹn
- `appointment.edit` - Chỉnh sửa lịch hẹn
- `appointment.delete` - Xóa lịch hẹn

### Tenant Management (Central Only)
- `tenant.view` - Xem danh sách tenant
- `tenant.create` - Tạo tenant mới
- `tenant.edit` - Chỉnh sửa tenant
- `tenant.delete` - Xóa tenant

### Role Management (Central Only)
- `role.view` - Xem roles
- `role.manage` - Quản lý roles và permissions

### Reports
- `report.view` - Xem báo cáo
- `report.export` - Xuất báo cáo

## Phân Quyền Mặc Định Theo Role

### Admin (Super User)
✅ **TẤT CẢ** permissions

### Doctor
✅ patient.view, patient.create, patient.edit
✅ encounter.view, encounter.create, encounter.edit
✅ prescription.view, prescription.create, prescription.edit
✅ appointment.view, appointment.create, appointment.edit
✅ report.view

### Nurse
✅ patient.view, patient.edit
✅ encounter.view, encounter.create
✅ prescription.view
✅ appointment.view, appointment.create, appointment.edit

### Receptionist
✅ patient.view, patient.create, patient.edit
✅ appointment.view, appointment.create, appointment.edit, appointment.delete

## Cách Sử Dụng

### 1. Truy Cập Trang Quản Lý
```
URL: http://localhost/phongkham/public/central/role-permissions
Login: admin@phongkham.test (Central Admin)
```

### 2. Quản Lý Permissions cho Role

**Bước 1:** Click vào một Role trong danh sách bên trái
**Bước 2:** Check/Uncheck các permissions bạn muốn gán
**Bước 3:** Click "Lưu Thay Đổi"

### 3. Tạo Permission Mới

**Bước 1:** Click "Thêm Permission"
**Bước 2:** Nhập tên permission theo format: `module.action`
   - Ví dụ: `laboratory.view`, `pharmacy.manage`
**Bước 3:** Click "Tạo"

### 4. Tạo Role Mới

**Bước 1:** Click "Thêm Role"
**Bước 2:** Nhập tên role (e.g., `pharmacist`, `laboratory-tech`)
**Bước 3:** Click "Tạo"
**Bước 4:** Assign permissions cho role mới

### 5. Xóa Permission/Role

- Click icon 🗑️ bên cạnh permission/role
- Xác nhận xóa
- **Lưu ý:** Không thể xóa core roles (admin, doctor, nurse, receptionist)

## Kiến Trúc Code

### Backend

**Controller:**
```
app/Http/Controllers/CentralAdmin/RolePermissionController.php
```

**Routes:**
```php
Route::prefix('central')->group(function () {
    Route::get('/role-permissions', [RolePermissionController::class, 'index']);
    Route::post('/roles/{role}/permissions', [RolePermissionController::class, 'updateRolePermissions']);
    Route::post('/permissions', [RolePermissionController::class, 'createPermission']);
    Route::delete('/permissions/{permission}', [RolePermissionController::class, 'deletePermission']);
    Route::post('/roles', [RolePermissionController::class, 'createRole']);
    Route::delete('/roles/{role}', [RolePermissionController::class, 'deleteRole']);
});
```

**Seeder:**
```
database/seeders/PermissionSeeder.php
```

**Chạy seeder:**
```bash
php artisan db:seed --class=PermissionSeeder
```

### Frontend

**Vue Component:**
```
resources/js/Pages/CentralAdmin/RolePermissions/Index.vue
```

**Features:**
- Real-time checkbox toggle
- Modal dialogs for create actions
- Color-coded role badges
- Grouped permissions by module

### Middleware Protection

**Routes được bảo vệ bởi:**
1. `auth` - User phải đăng nhập
2. `PreventAccessFromTenantDomains` - Chặn tenant domains
3. `CentralAdminOnly` - Chỉ central admin (whitelist email)

## Sử Dụng Permissions trong Code

### Check Permission trong Controller

```php
// Single permission
if (!auth()->user()->can('patient.create')) {
    abort(403, 'Unauthorized');
}

// Multiple permissions (OR)
if (!auth()->user()->canAny(['patient.view', 'patient.create'])) {
    abort(403);
}

// All permissions required (AND)
if (!auth()->user()->hasAllPermissions(['patient.view', 'patient.edit'])) {
    abort(403);
}
```

### Check Permission trong Blade/Vue

**Backend (Controller):**
```php
return Inertia::render('Patient/Index', [
    'patients' => $patients,
    'canCreate' => auth()->user()->can('patient.create'),
    'canEdit' => auth()->user()->can('patient.edit'),
]);
```

**Frontend (Vue):**
```vue
<button v-if="$page.props.canCreate">
    Thêm Bệnh Nhân
</button>
```

### Middleware cho Routes

```php
Route::middleware(['permission:patient.create'])->group(function () {
    Route::post('/patients', [PatientController::class, 'store']);
});
```

## Best Practices

### 1. Naming Convention
Format: `module.action`
- ✅ `patient.view`, `encounter.create`
- ❌ `view-patient`, `CreateEncounter`

### 2. Permission Granularity
- Tạo permissions cụ thể cho từng hành động
- Tránh permission quá rộng như `patient.manage` (nên tách thành view, create, edit, delete)

### 3. Core Roles Protection
- Không xóa/sửa tên các core roles
- Có thể điều chỉnh permissions của core roles

### 4. Testing After Changes
- Test login với các roles khác nhau
- Verify permissions hoạt động đúng
- Check UI elements ẩn/hiện theo quyền

## Troubleshooting

### Permission không áp dụng sau khi thay đổi

**Giải pháp:**
```bash
php artisan permission:cache-reset
php artisan optimize:clear
```

### User không thể truy cập dù có permission

**Kiểm tra:**
1. User có đúng role?
2. Role có đúng permission?
3. Cache đã clear?
4. Middleware đã apply đúng?

### Role bị xóa nhầm

**Khôi phục:**
```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=PermissionSeeder
```

## Future Enhancements

- [ ] Permission groups (bundles)
- [ ] Audit log cho thay đổi permissions
- [ ] Export/Import permissions configuration
- [ ] Permission templates
- [ ] User-level permission override
- [ ] Time-based permissions (temporary access)

## Files Created/Modified

### New Files
- ✅ `app/Http/Controllers/CentralAdmin/RolePermissionController.php`
- ✅ `resources/js/Pages/CentralAdmin/RolePermissions/Index.vue`
- ✅ `database/seeders/PermissionSeeder.php`

### Modified Files
- ✅ `routes/web.php` - Added role-permissions routes
- ✅ `resources/js/Layouts/AuthenticatedLayout.vue` - Added "Phân Quyền" menu

---

**Created:** December 21, 2025
**Status:** ✅ Production Ready
