# Hướng Dẫn Sử Dụng - Phòng Khám

## ✅ Hệ Thống Đã Sẵn Sàng!

### Đăng Nhập Hệ Thống

1. Mở trình duyệt và truy cập: `http://localhost/phongkham/public`
2. Đăng nhập với tài khoản admin:
   - **Email**: admin@phongkham.test
   - **Password**: password

### Bước 1: Tạo Tenant (Phòng Khám) Đầu Tiên

1. Sau khi đăng nhập, click vào **"Quản Lý Tenant"** trên menu
2. Click nút **"Create New Tenant"**
3. Điền thông tin:
   - **Subdomain**: clinic1 (hoặc tên bạn muốn)
   - **Clinic Name**: Phòng Khám ABC
   - **Primary Color**: Chọn màu chủ đạo
   - **Logo**: Upload logo (nếu có)
   - **Admin Name**: Tên admin phòng khám
   - **Admin Email**: admin@clinic1.com
   - **Admin Password**: password123
4. Click **"Create Tenant"**

### Bước 2: Truy Cập Tenant Vừa Tạo

**Lưu ý quan trọng**: Để truy cập tenant qua subdomain, bạn cần cấu hình:

#### Cách 1: Cấu hình Virtual Host (Khuyến nghị)

1. Mở file `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Thêm vào cuối file:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/phongkham/public"
    ServerName phongkham.test
    ServerAlias *.phongkham.test
    
    <Directory "C:/xampp/htdocs/phongkham/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Mở file `C:\Windows\System32\drivers\etc\hosts` (chạy Notepad as Administrator)
4. Thêm dòng:
```
127.0.0.1 phongkham.test
127.0.0.1 clinic1.phongkham.test
```

5. Restart Apache trong XAMPP Control Panel

6. Truy cập:
   - Central: `http://phongkham.test`
   - Tenant: `http://clinic1.phongkham.test`

#### Cách 2: Test Nhanh (Không cần config)

Hiện tại tenant routes đã được tạo, nhưng để test nhanh bạn có thể truy cập trực tiếp database của tenant.

### Bước 3: Quản Lý Bệnh Nhân

Sau khi đăng nhập vào tenant:

1. **Thêm Bệnh Nhân Mới**:
   - Click menu "Bệnh Nhân" (sau khi thêm vào menu)
   - Click nút **"+ Thêm Bệnh Nhân"**
   - Điền đầy đủ thông tin:
     - Họ tên, ngày sinh, giới tính **(bắt buộc)**
     - Số điện thoại, email, địa chỉ
     - Thông tin BHYT, CMND
     - Liên hệ khẩn cấp
     - Tiền sử bệnh, dị ứng
   - Click **"Lưu Bệnh Nhân"**
   - Mã bệnh nhân sẽ tự động tạo (VD: BN202512010001)

2. **Tìm Kiếm Bệnh Nhân**:
   - Nhập tên, mã BN, hoặc số điện thoại vào ô tìm kiếm
   - Click nút **"Tìm Kiếm"** hoặc Enter

3. **Xem Chi Tiết Bệnh Nhân**:
   - Click vào nút **"Xem"** ở hàng bệnh nhân
   - Xem đầy đủ thông tin: cá nhân, y tế, liên hệ

4. **Chỉnh Sửa Thông Tin**:
   - Click nút **"Sửa"** ở danh sách hoặc trang chi tiết
   - Cập nhật thông tin cần thiết
   - Click **"Cập Nhật"**

5. **Xóa Bệnh Nhân**:
   - Click nút **"Xóa"** ở danh sách
   - Xác nhận xóa
   - Bệnh nhân sẽ bị xóa mềm (soft delete)

## Tính Năng Đã Hoàn Thành

### ✅ Central Admin (Quản Lý Tenant)
- Tạo tenant mới với subdomain riêng
- Cấu hình theme per-tenant (logo, màu sắc)
- Tạo admin user cho mỗi tenant
- Database isolation (mỗi tenant 1 database riêng)

### ✅ Module Patient (Quản Lý Bệnh Nhân)
- **CRUD đầy đủ**: Tạo, đọc, cập nhật, xóa
- **Tự động tạo mã BN**: Format BN + YYYYMMDD + số thứ tự
- **Tính tuổi tự động**: Hiển thị tuổi từ ngày sinh
- **Tìm kiếm**: Theo tên, mã BN, số điện thoại
- **Phân trang**: 20 bệnh nhân/trang
- **Soft delete**: Xóa mềm, có thể khôi phục
- **Touch-optimized**: Nút lớn (48px), dễ bấm
- **Dark mode**: Hỗ trợ giao diện tối

### ✅ Phân Quyền (RBAC)
- **Admin**: Toàn quyền hệ thống
- **Doctor**: Quản lý bệnh nhân, khám bệnh
- **Nurse**: Hỗ trợ bệnh nhân
- **Receptionist**: Tiếp nhận, hẹn khám

## Cấu Trúc Module Patient

```
app/Modules/Patient/
├── Models/
│   └── Patient.php             # Model với auto-generate code
├── Controllers/
│   └── PatientController.php   # CRUD controller
└── README.md                   # Documentation

resources/js/Pages/Patient/
├── Index.vue                   # Danh sách bệnh nhân
├── Create.vue                  # Form thêm mới
├── Edit.vue                    # Form chỉnh sửa
└── Show.vue                    # Chi tiết bệnh nhân
```

## Cơ Sở Dữ Liệu

### Bảng `patients` (trong tenant database)

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| id | BIGINT | ID tự tăng |
| patient_code | VARCHAR(50) | Mã BN (unique) |
| first_name | VARCHAR(100) | Tên |
| last_name | VARCHAR(100) | Họ |
| date_of_birth | DATE | Ngày sinh |
| gender | ENUM | Giới tính (male/female/other) |
| phone | VARCHAR(20) | Số điện thoại |
| email | VARCHAR | Email |
| address | TEXT | Địa chỉ |
| city | VARCHAR(100) | Thành phố |
| province | VARCHAR(100) | Tỉnh/Thành |
| postal_code | VARCHAR(10) | Mã bưu chính |
| id_number | VARCHAR(50) | CMND/CCCD |
| insurance_number | VARCHAR(50) | Số BHYT |
| emergency_contact_name | VARCHAR | Người liên hệ khẩn cấp |
| emergency_contact_phone | VARCHAR(20) | SĐT khẩn cấp |
| notes | TEXT | Ghi chú |
| medical_history | TEXT | Tiền sử bệnh |
| allergies | TEXT | Dị ứng |
| blood_type | VARCHAR(10) | Nhóm máu |

## Các Lệnh Hữu Ích

### Chạy Development Server
```powershell
cd c:\xampp\htdocs\phongkham
npm run dev
```

### Build Production
```powershell
npm run build
```

### Chạy Migration cho Tenant Mới
```powershell
c:\xampp\php\php.exe artisan tenants:migrate --tenants=clinic1
```

### Xóa Cache
```powershell
c:\xampp\php\php.exe artisan cache:clear
c:\xampp\php\php.exe artisan config:clear
c:\xampp\php\php.exe artisan view:clear
```

### Backup Database
```powershell
c:\xampp\mysql\bin\mysqldump -u root phongkham > backup_central.sql
c:\xampp\mysql\bin\mysqldump -u root tenant_clinic1 > backup_clinic1.sql
```

## Tiếp Theo - Module Cần Phát Triển

### 1. Module Encounter (Khám Bệnh)
- Tạo phiếu khám
- Ghi nhận triệu chứng, chẩn đoán
- Chỉ số sinh tồn (huyết áp, nhiệt độ, v.v.)
- SOAP notes
- Liên kết với bệnh nhân

### 2. Module Prescription (Đơn Thuốc)
- Kê đơn thuốc
- Liên kết encounter
- Gửi đơn qua PrescriptionGateway
- In đơn thuốc

### 3. Module Appointment (Lịch Hẹn)
- Đặt lịch khám
- Calendar view
- Nhắc nhở SMS/Email
- Quản lý lịch bác sĩ

## Xử Lý Lỗi Thường Gặp

### Lỗi: "Class 'Patient' not found"
```powershell
c:\xampp\php\php.exe c:\xampp\php\composer dump-autoload
```

### Lỗi: "SQLSTATE[HY000] [1049] Unknown database"
- Kiểm tra database đã tạo chưa
- Chạy migration: `php artisan migrate`

### Lỗi: "Route not defined"
- Xóa cache: `php artisan route:clear`
- Kiểm tra routes trong `routes/tenant.php`

### Lỗi: "Permission denied" trên storage
```powershell
icacls storage /grant Everyone:(OI)(CI)F /T
```

### Assets không load
```powershell
npm run build
c:\xampp\php\php.exe artisan view:clear
```

## Tips & Tricks

### 1. Phím Tắt Hữu Ích
- **Ctrl + /** : Comment code
- **Alt + ↑/↓** : Di chuyển dòng
- **Ctrl + D** : Select next occurrence

### 2. Debug
- Xem log: `storage/logs/laravel.log`
- Dùng `dd($variable)` để debug
- Browser DevTools: F12

### 3. Performance
- Cache config: `php artisan config:cache`
- Cache routes: `php artisan route:cache`
- Optimize autoload: `composer dump-autoload -o`

## Tài Liệu Tham Khảo

- **README.md**: Hướng dẫn cài đặt đầy đủ
- **docs/architecture.md**: Kiến trúc hệ thống
- **docs/QUICK_START.md**: Quick start guide (English)
- **Module READMEs**: Chi tiết từng module

## Hỗ Trợ

Nếu gặp vấn đề:
1. Kiểm tra log: `storage/logs/laravel.log`
2. Xóa cache: `php artisan optimize:clear`
3. Rebuild assets: `npm run build`
4. Restart Apache + MySQL

---

**Chúc bạn phát triển thành công! 🎉**
