# Hướng dẫn cấu hình Virtual Host cho Phòng Khám Multi-tenant

## Các bước thực hiện:

### 1. File hosts đã được cấu hình
File `C:\Windows\System32\drivers\etc\hosts` đã được thêm:
```
127.0.0.1       phongkham.test
127.0.0.1       pk1.phongkham.test
127.0.0.1       pk2.phongkham.test
127.0.0.1       pk3.phongkham.test
```

### 2. Cấu hình Apache Virtual Host

**Bước 1:** Mở file `c:\xampp\apache\conf\httpd.conf` với quyền Administrator

**Bước 2:** Tìm và bỏ comment (xóa dấu #) dòng sau:
```apache
Include conf/extra/httpd-vhosts.conf
```

**Bước 3:** Mở file `c:\xampp\apache\conf\extra\httpd-vhosts.conf` với quyền Administrator

**Bước 4:** Thêm nội dung từ file `apache-vhost.conf` vào cuối file httpd-vhosts.conf:
```apache
<VirtualHost *:80>
    ServerName phongkham.test
    ServerAlias *.phongkham.test
    DocumentRoot "c:/xampp/htdocs/phongkham/public"
    
    <Directory "c:/xampp/htdocs/phongkham/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/phongkham-error.log"
    CustomLog "logs/phongkham-access.log" common
</VirtualHost>
```

**Bước 5:** Khởi động lại Apache trong XAMPP Control Panel

### 3. Truy cập ứng dụng

Sau khi cấu hình xong, bạn có thể truy cập:

- **Domain chính (Central):** http://phongkham.test
- **Tenant 1:** http://pk1.phongkham.test
- **Tenant 2:** http://pk2.phongkham.test
- **Tenant 3:** http://pk3.phongkham.test

### 4. Tạo tenant mới

1. Truy cập http://phongkham.test
2. Đăng nhập với tài khoản admin (được tạo sau khi seed)
3. Vào phần Central -> Tenants
4. Tạo tenant mới với subdomain (vd: pk1, pk2, pk3)

### Lưu ý:

- Phải chạy các lệnh với quyền Administrator khi chỉnh sửa file hosts và Apache config
- Sau khi thay đổi cấu hình Apache, cần restart Apache service
- Subdomain trong file hosts có thể thêm nhiều hơn tùy theo số lượng tenant cần tạo

# Virtual Host Configuration for Phong Kham Multi-tenant System
# Copy this configuration to c:\xampp\apache\conf\extra\httpd-vhosts.conf
# Or include it in your Apache configuration

# Main domain
<VirtualHost *:80>
    ServerName phongkham.test
    ServerAlias *.phongkham.test
    DocumentRoot "c:/xampp/htdocs/phongkham/public"
    
    <Directory "c:/xampp/htdocs/phongkham/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/phongkham-error.log"
    CustomLog "logs/phongkham-access.log" common
</VirtualHost>
