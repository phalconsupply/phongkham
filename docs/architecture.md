# Architecture Documentation - Phòng Khám

## System Overview

Phòng Khám is a multi-tenant clinic management system designed with a database-per-tenant architecture for maximum data isolation and customization per clinic.

## Architecture Patterns

### Multi-Tenancy Architecture

#### Database-Per-Tenant Pattern

Each tenant (clinic) has its own isolated database, providing:
- **Data Isolation**: Complete separation of tenant data
- **Customization**: Independent schema evolution per tenant
- **Performance**: No cross-tenant queries
- **Compliance**: Easier to meet data residency requirements
- **Scalability**: Can distribute tenants across different database servers

```
Central Database (phongkham)
├── tenants table
├── domains table
├── tenant_themes table
└── central users/config

Tenant Database (tenant_{id})
├── users
├── patients
├── encounters
├── prescriptions
├── appointments
└── ...
```

#### Tenant Identification

Tenants are identified via subdomain:
- `clinic1.yourdomain.com` → Tenant ID: `clinic1`
- `clinic2.yourdomain.com` → Tenant ID: `clinic2`

The `stancl/tenancy` package handles automatic tenant resolution via middleware.

### Module Architecture

#### Modular Design

```
app/Modules/
├── Patient/
│   ├── Controllers/
│   ├── Models/
│   ├── Requests/
│   ├── Resources/
│   └── Services/
├── Encounter/
│   └── ...
├── Prescription/
│   └── ...
├── Appointment/
│   └── ...
└── Shared/
    ├── Traits/
    ├── Helpers/
    └── Services/
```

**Benefits:**
- Clear separation of concerns
- Easy to navigate and maintain
- Reusable components in Shared module
- Can be extracted to packages if needed

### Frontend Architecture

#### Vue 3 + Inertia.js Stack

```
resources/js/
├── Pages/
│   ├── CentralAdmin/     # Central admin pages
│   ├── Patient/          # Patient module pages
│   ├── Encounter/        # Encounter module pages
│   ├── Prescription/     # Prescription module pages
│   ├── Appointment/      # Appointment module pages
│   └── Dashboard.vue     # Main dashboard
├── Components/
│   ├── Shared/           # Reusable components
│   └── Forms/            # Form components
└── Layouts/
    ├── AuthenticatedLayout.vue
    └── GuestLayout.vue
```

**Key Principles:**
- Server-driven routing via Inertia.js
- No need for separate API layer
- Props passed from Laravel controllers
- Form handling with Inertia forms

#### Touch-Optimized UI

Design guidelines:
- **Minimum button size**: 48px × 48px
- **Touch target spacing**: 8px minimum
- **Font sizes**: Base 16px, larger for primary actions
- **Layout**: Generous whitespace, clear visual hierarchy

### Authentication & Authorization

#### Laravel Breeze + Spatie Permissions

```
Authentication Flow:
User Login → Breeze Auth
    ↓
Tenant Detection → Middleware
    ↓
Database Switch → Tenancy
    ↓
Permission Check → Spatie
    ↓
Access Granted/Denied
```

#### Role Hierarchy

```
Admin (Full Access)
├── manage_tenants
├── manage_users
└── all_permissions

Doctor
├── view/create/edit patients
├── view/create/edit encounters
├── view/create/edit prescriptions
└── view/create/edit appointments

Nurse
├── view/create/edit patients
├── view/create encounters
└── view/create/edit appointments

Receptionist
├── view/create/edit patients
└── view/create/edit/delete appointments
```

### Theming System

#### Per-Tenant Customization

```php
// Central database stores theme
TenantTheme {
    id: string (tenant_id)
    clinic_name: string
    logo_path: string
    primary_color: string (#hex)
    secondary_color: string (#hex)
}
```

**Implementation:**
1. Theme stored in central database
2. Retrieved during tenant initialization
3. Injected into Vue app via Inertia props
4. Applied via CSS variables or Tailwind config

**Future Enhancement:**
```javascript
// Dynamic Tailwind theme
const theme = usePage().props.theme;
<div :style="{ '--primary-color': theme.primary_color }">
```

### Integration Architecture

#### PrescriptionGateway Adapter Pattern

```
┌─────────────────┐
│  Prescription   │
│  Controller     │
└────────┬────────┘
         │
         v
┌─────────────────────────┐
│ PrescriptionGateway     │
│ Interface               │
└─────────┬───────────────┘
          │
          ├──────────────────┬─────────────────┐
          v                  v                 v
┌──────────────────┐  ┌─────────────┐  ┌──────────────┐
│ Dummy Gateway    │  │ Pharmacy A  │  │ Pharmacy B   │
│ (Development)    │  │ Gateway     │  │ Gateway      │
└──────────────────┘  └─────────────┘  └──────────────┘
```

**Interface:**
```php
interface PrescriptionGatewayInterface {
    public function sendPrescription(array $data): array;
    public function checkStatus(string $id): array;
    public function cancelPrescription(string $id): bool;
}
```

**Usage:**
```php
// Service Provider binding
app()->bind(PrescriptionGatewayInterface::class, function() {
    return match(config('services.prescription_gateway.provider')) {
        'pharmacy_a' => new PharmacyAGateway(),
        'pharmacy_b' => new PharmacyBGateway(),
        default => new DummyPrescriptionGateway(),
    };
});

// In controller
$gateway = app(PrescriptionGatewayInterface::class);
$result = $gateway->sendPrescription($prescriptionData);
```

### Docker Architecture

#### Container Structure

```
┌──────────────────────────────────────────────┐
│                    Host                      │
│  ┌────────────────────────────────────────┐ │
│  │         Docker Network                 │ │
│  │  ┌──────────┐  ┌──────────┐           │ │
│  │  │  Nginx   │  │   PHP    │           │ │
│  │  │   :80    │→→│  :9000   │           │ │
│  │  └──────────┘  └────┬─────┘           │ │
│  │                     │                  │ │
│  │       ┌─────────────┼─────────────┐   │ │
│  │       │             │             │   │ │
│  │   ┌───▼────┐   ┌───▼────┐   ┌───▼─┐  │ │
│  │   │ MySQL  │   │ Redis  │   │Horiz│  │ │
│  │   │ :3306  │   │ :6379  │   │ on  │  │ │
│  │   └────────┘   └────────┘   └─────┘  │ │
│  └────────────────────────────────────────┘ │
└──────────────────────────────────────────────┘
```

**Services:**
- **Nginx**: Web server, reverse proxy
- **PHP-FPM**: Application runtime
- **MySQL**: Primary database (central + tenant DBs)
- **Redis**: Cache and queue backend
- **Horizon**: Queue worker dashboard and management
- **Node**: Asset compilation (can be removed after build)

#### Data Persistence

Volumes:
- `mysql_data:/var/lib/mysql` - Database persistence
- `redis_data:/data` - Cache/queue persistence
- `./storage` - Uploaded files, logs
- `./public/storage` - Public file access

### Database Schema

#### Central Database

```sql
-- Tenants
CREATE TABLE tenants (
    id VARCHAR(255) PRIMARY KEY,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    data JSON
);

-- Domains
CREATE TABLE domains (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    domain VARCHAR(255) UNIQUE,
    tenant_id VARCHAR(255),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

-- Tenant Themes
CREATE TABLE tenant_themes (
    id VARCHAR(255) PRIMARY KEY,  -- tenant_id
    clinic_name VARCHAR(255),
    logo_path VARCHAR(255),
    primary_color VARCHAR(7),
    secondary_color VARCHAR(7),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Users (central)
-- Permissions & Roles (Spatie)
```

#### Tenant Database (per tenant)

```sql
-- Users
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    ...
);

-- Patients (to be implemented)
CREATE TABLE patients (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    date_of_birth DATE,
    ...
);

-- Encounters (to be implemented)
-- Prescriptions (to be implemented)
-- Appointments (to be implemented)
```

### Security Architecture

#### Multi-Layer Security

1. **Network Layer** (Docker/VPS)
   - Firewall rules
   - Container isolation
   - Internal network for inter-service communication

2. **Application Layer**
   - CSRF protection (Laravel)
   - SQL injection protection (Eloquent ORM)
   - XSS protection (Vue escaping)
   - Rate limiting

3. **Authentication Layer**
   - Password hashing (bcrypt)
   - Session management
   - Remember token

4. **Authorization Layer**
   - Role-based access control
   - Permission checks on routes
   - Policy-based authorization

5. **Data Layer**
   - Database-per-tenant isolation
   - Encrypted connections
   - Regular backups

### Performance Optimization

#### Caching Strategy

```
┌──────────────┐
│   Request    │
└──────┬───────┘
       │
       v
┌──────────────┐     Cache Hit
│  Redis       │────────────────→ Response
│  Cache       │
└──────┬───────┘
       │ Cache Miss
       v
┌──────────────┐
│  Database    │
└──────┬───────┘
       │
       v
    Update Cache
       │
       v
    Response
```

**Cache Keys:**
- User permissions: `permissions:user:{id}`
- Tenant theme: `theme:tenant:{id}`
- Query results: Custom per query

#### Queue System

**Use Cases:**
- Email notifications
- Report generation
- Data exports
- Prescription gateway communication
- Audit log processing

**Implementation:**
```php
// Dispatch job
SendPrescriptionJob::dispatch($prescription);

// Process in background
class SendPrescriptionJob implements ShouldQueue {
    public function handle(PrescriptionGatewayInterface $gateway) {
        $gateway->sendPrescription($this->prescription);
    }
}
```

#### Database Optimization

- Indexes on foreign keys
- Composite indexes on common queries
- Query result caching
- Eager loading relationships
- Connection pooling

### Scalability Considerations

#### Horizontal Scaling

**Application Servers:**
- Stateless PHP-FPM containers
- Load balancer in front (Nginx/HAProxy)
- Session storage in Redis

**Database Scaling:**
- Read replicas for reporting
- Shard tenants across DB servers
- Connection pooling

**Cache Scaling:**
- Redis Cluster for high availability
- Cache warming strategies

#### Vertical Scaling

- Increase container resources
- Optimize queries
- Add indexes
- Code profiling

### Deployment Pipeline

#### CI/CD Flow

```
Code Push → GitHub
    ↓
Run Tests (PHPUnit, Pest)
    ↓
Build Assets (npm run build)
    ↓
Build Docker Images
    ↓
Push to Registry
    ↓
Deploy to VPS
    ↓
Run Migrations
    ↓
Zero-downtime Restart
```

### Monitoring & Logging

#### Application Monitoring

- **Laravel Telescope** (development): Request/query debugging
- **Laravel Horizon**: Queue monitoring
- **Log aggregation**: Centralized logging (future: ELK stack)

#### Infrastructure Monitoring

- **Docker stats**: Container resource usage
- **MySQL slow query log**: Performance bottlenecks
- **Nginx access logs**: Traffic patterns

### Backup Strategy

#### Database Backups

```bash
# Daily automated backup
docker-compose exec mysql mysqldump -u root -p phongkham > backup.sql

# Tenant database backup
docker-compose exec mysql mysqldump -u root -p tenant_clinic1 > tenant_clinic1_backup.sql
```

#### File Backups

- Storage directory (uploads, logs)
- Public storage directory
- Environment files

**Schedule:**
- Daily incremental
- Weekly full backup
- Monthly archive
- Off-site replication

### Future Architecture Enhancements

1. **Microservices**: Extract modules to separate services
2. **Event Sourcing**: Audit trail with event store
3. **CQRS**: Separate read/write models for performance
4. **API Layer**: REST/GraphQL for mobile apps
5. **Real-time**: WebSockets for live updates
6. **Multi-region**: Deploy across geographic regions
7. **AI Integration**: ML models for diagnostics assistance

## Technology Decisions

### Why Database-Per-Tenant?

**Pros:**
✓ Maximum data isolation
✓ Easier compliance (HIPAA, GDPR)
✓ Independent scaling per tenant
✓ Restore individual tenant without affecting others
✓ Customize schema per tenant if needed

**Cons:**
✗ More databases to manage
✗ Higher resource usage
✗ Cross-tenant reporting requires aggregation

**Decision**: For healthcare data, isolation and compliance outweigh management complexity.

### Why Vue 3 + Inertia?

**Pros:**
✓ No need for separate API
✓ Server-side routing
✓ Easier to maintain (single codebase)
✓ Better SEO than SPA
✓ Familiar Blade-like development

**Cons:**
✗ Less flexibility than full SPA
✗ Page reloads on navigation (mitigated by Inertia caching)

**Decision**: Simpler architecture and faster development for this use case.

### Why Docker?

**Pros:**
✓ Consistent environments (dev/prod)
✓ Easy deployment
✓ Isolated services
✓ Scalable

**Cons:**
✗ Learning curve
✗ Resource overhead

**Decision**: Benefits outweigh learning curve for production deployments.

## Conclusion

This architecture provides a solid foundation for a multi-tenant clinic management system with:
- Strong data isolation
- Scalability
- Maintainability
- Security
- Touch-optimized UX

The modular design allows for incremental feature development while maintaining code quality and separation of concerns.
