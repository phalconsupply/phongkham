# Shared Module

## Purpose
Contains shared utilities, traits, helpers, and services used across multiple modules.

## Structure
```
Shared/
├── Traits/
│   ├── HasCode.php           # Auto-generate unique codes
│   ├── Auditable.php         # Track created_by, updated_by
│   └── SoftDeletesWithReason.php
├── Helpers/
│   ├── DateHelper.php        # Date formatting utilities
│   ├── StringHelper.php      # String manipulation
│   └── ValidationHelper.php  # Common validation rules
├── Services/
│   ├── ExportService.php     # Export to Excel/PDF
│   ├── NotificationService.php
│   └── AuditLogService.php
└── Enums/
    ├── AppointmentStatus.php
    ├── EncounterType.php
    └── PrescriptionStatus.php
```

## Planned Components

### Traits

#### HasCode Trait
Auto-generate unique codes for models:
```php
use App\Modules\Shared\Traits\HasCode;

class Patient extends Model {
    use HasCode;
    
    protected static function getCodePrefix(): string {
        return 'PT';
    }
}
// Generates: PT-20251201-0001
```

#### Auditable Trait
Track who created/updated records:
```php
use App\Modules\Shared\Traits\Auditable;

class Encounter extends Model {
    use Auditable;
}
// Automatically fills created_by, updated_by
```

### Helpers

#### DateHelper
```php
DateHelper::formatForDisplay($date);
DateHelper::calculateAge($birthDate);
DateHelper::isWeekend($date);
DateHelper::getBusinessDays($startDate, $endDate);
```

#### ValidationHelper
```php
ValidationHelper::phoneNumber(); // Phone validation rule
ValidationHelper::idNumber();    // ID number validation
ValidationHelper::vietnameseName(); // Vietnamese name validation
```

### Services

#### ExportService
Export data to various formats:
```php
ExportService::toExcel($data, 'patients.xlsx');
ExportService::toPdf($view, $data, 'prescription.pdf');
ExportService::toCsv($data, 'appointments.csv');
```

#### NotificationService
Centralized notification handling:
```php
NotificationService::sendAppointmentReminder($appointment);
NotificationService::sendPrescriptionReady($prescription);
NotificationService::sendWelcomeEmail($user);
```

#### AuditLogService
Track important actions:
```php
AuditLogService::log('patient.created', $patient);
AuditLogService::log('prescription.sent', $prescription);
AuditLogService::getLog($model);
```

### Enums (PHP 8.1+)

#### AppointmentStatus
```php
enum AppointmentStatus: string {
    case SCHEDULED = 'scheduled';
    case CONFIRMED = 'confirmed';
    case ARRIVED = 'arrived';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';
}
```

## Middleware

### Shared Middleware
- `CheckTenantAccess` - Verify user belongs to tenant
- `LogActivity` - Log user activities
- `CheckModulePermission` - Module-level permissions

## Vue Components

### Shared Vue Components
```
resources/js/Components/Shared/
├── DataTable.vue          # Reusable data table
├── SearchInput.vue        # Search with debounce
├── DatePicker.vue         # Touch-friendly date picker
├── TimePicker.vue         # Touch-friendly time picker
├── Modal.vue              # Modal dialog
├── ConfirmDialog.vue      # Confirmation dialog
├── StatusBadge.vue        # Status display
├── Avatar.vue             # User avatar
├── LoadingSpinner.vue     # Loading indicator
└── EmptyState.vue         # Empty state placeholder
```

### Composables
```javascript
// useDebounce.js
export function useDebounce(fn, delay) { ... }

// usePermissions.js
export function usePermissions() {
    const can = (permission) => { ... }
    const hasRole = (role) => { ... }
    return { can, hasRole }
}

// useToast.js
export function useToast() {
    const success = (message) => { ... }
    const error = (message) => { ... }
    return { success, error }
}
```

## Constants

### Application Constants
```php
// app/Modules/Shared/Constants/AppConstants.php
class AppConstants {
    const ITEMS_PER_PAGE = 20;
    const MAX_UPLOAD_SIZE = 5242880; // 5MB
    const DATE_FORMAT = 'Y-m-d';
    const DATETIME_FORMAT = 'Y-m-d H:i:s';
    const DISPLAY_DATE_FORMAT = 'd/m/Y';
}
```

## Utilities

### File Upload Handler
```php
class FileUploadService {
    public function upload($file, $path, $disk = 'public');
    public function delete($path, $disk = 'public');
    public function getUrl($path, $disk = 'public');
}
```

### Code Generator
```php
class CodeGenerator {
    public static function generate(string $prefix, int $length = 4): string;
    public static function generateUnique(string $model, string $field, string $prefix): string;
}
```

## To Implement
- [ ] Create all traits
- [ ] Create helper classes
- [ ] Create shared services
- [ ] Create enums for status fields
- [ ] Create Vue shared components
- [ ] Create composables
- [ ] Add unit tests for utilities
- [ ] Document usage examples
