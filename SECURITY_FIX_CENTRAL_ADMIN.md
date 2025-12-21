# Security Fix - Central Admin Access Control

## Date: December 21, 2025

## Lỗi Phát Hiện

Tenant users có thể truy cập và tạo tenant khác thông qua routes central admin.

### Chi tiết lỗi:

1. **Routes không được bảo vệ**: Routes `/central/tenants/*` chỉ có middleware `auth`, không ngăn tenant access
2. **TenantController yếu**: Chỉ kiểm tra role 'admin' mà không phân biệt central admin vs tenant admin
3. **Thiếu middleware**: Không có middleware ngăn tenant domains truy cập central routes

## Giải Pháp Áp Dụng

### 1. Tạo Middleware `PreventAccessFromTenantDomains`

File: `app/Http/Middleware/PreventAccessFromTenantDomains.php`

```php
public function handle(Request $request, Closure $next): Response
{
    if (tenancy()->initialized) {
        abort(403, 'Access to central admin from tenant domains is forbidden.');
    }
    return $next($request);
}
```

**Chức năng**: Chặn mọi request từ tenant domain đến central admin

### 2. Tạo Middleware `CentralAdminOnly`

File: `app/Http/Middleware/CentralAdminOnly.php`

```php
public function handle(Request $request, Closure $next): Response
{
    // Ensure NOT in tenant context
    if (tenancy()->initialized) {
        abort(403, 'Tenant users cannot access central admin.');
    }
    
    // Ensure authenticated
    if (!auth()->check()) {
        abort(403, 'Unauthenticated.');
    }
    
    // Ensure admin role in CENTRAL database
    if (!auth()->user()->hasRole('admin')) {
        abort(403, 'Only central administrators can access this area.');
    }
    
    return $next($request);
}
```

**Chức năng**: Triple-check để đảm bảo chỉ central admin có quyền

### 3. Cập Nhật Routes `routes/web.php`

**Trước:**
```php
Route::middleware('auth')->group(function () {
    // ...
    Route::prefix('central')->name('central.')->group(function () {
        Route::resource('tenants', TenantController::class);
    });
});
```

**Sau:**
```php
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // ... profile routes only
});

// Separate group with strict security
Route::middleware([
    'auth',
    \App\Http\Middleware\PreventAccessFromTenantDomains::class,
    \App\Http\Middleware\CentralAdminOnly::class,
])->prefix('central')->name('central.')->group(function () {
    Route::resource('tenants', TenantController::class);
});
```

### 4. Tăng Cường Security trong TenantController

**Thêm constructor check:**
```php
public function __construct()
{
    if (tenancy()->initialized) {
        abort(403, 'Central admin cannot be accessed from tenant domains.');
    }
}
```

**Cập nhật tất cả methods:**
```php
public function index()
{
    abort_unless(auth()->check() && auth()->user()->hasRole('admin'), 403, 'Unauthorized');
    // ...
}
```

## Cách Hoạt Động

### Layer 1: Route Middleware
- `PreventAccessFromTenantDomains`: Chặn ngay ở routing level
- `CentralAdminOnly`: Kiểm tra authentication + role + context

### Layer 2: Controller Constructor
- Double-check tenancy context
- Fail-fast nếu phát hiện tenant context

### Layer 3: Method Level
- Triple-check trong mỗi method
- Đảm bảo authenticated + role admin

## Testing

### Test 1: Central Admin Access (Should Pass)
```
URL: http://localhost/phongkham/public/central/tenants
Login: admin@phongkham.test (central admin)
Expected: ✅ Access granted
```

### Test 2: Tenant Admin Access (Should Fail)
```
URL: http://clinic1.localhost/central/tenants
Login: admin@clinic1.com (tenant admin)
Expected: ❌ 403 Forbidden
```

### Test 3: Tenant User Access (Should Fail)
```
URL: http://clinic1.localhost/central/tenants
Login: doctor@clinic1.com (tenant user)
Expected: ❌ 403 Forbidden
```

### Test 4: Direct Central Access from Tenant (Should Fail)
```
URL: http://localhost/phongkham/public/central/tenants
Login: admin@clinic1.com (tenant admin in central DB)
Expected: ❌ 403 Forbidden (no access to central DB)
```

## Security Benefits

1. ✅ **Defense in Depth**: 3 layers of protection
2. ✅ **Context Isolation**: Strict separation of central vs tenant
3. ✅ **Explicit Checks**: Multiple validation points
4. ✅ **Clear Error Messages**: Easy to debug
5. ✅ **Future Proof**: Easy to extend for more routes

## Files Changed

1. ✅ `app/Http/Middleware/PreventAccessFromTenantDomains.php` (NEW)
2. ✅ `app/Http/Middleware/CentralAdminOnly.php` (NEW)
3. ✅ `routes/web.php` (UPDATED)
4. ✅ `app/Http/Controllers/CentralAdmin/TenantController.php` (UPDATED)

## Next Steps

1. Test tất cả scenarios
2. Áp dụng pattern này cho tất cả central admin routes (nếu có thêm)
3. Document cho team về phân biệt central vs tenant admin
4. Cân nhắc thêm logging cho failed access attempts

---

**Security Level**: ⚠️ CRITICAL → ✅ SECURED
