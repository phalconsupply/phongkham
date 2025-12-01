# Patient Module

## Purpose
Manages patient registration, demographics, and medical records.

## Structure
```
Patient/
├── Controllers/
│   └── PatientController.php
├── Models/
│   └── Patient.php
├── Requests/
│   ├── StorePatientRequest.php
│   └── UpdatePatientRequest.php
├── Resources/
│   └── PatientResource.php
└── Services/
    └── PatientService.php
```

## Planned Features
- Patient registration with demographics
- Medical history tracking
- Document uploads (ID, insurance cards)
- Search and filtering
- Patient profile view
- Contact information management
- Emergency contacts
- Insurance information

## Database Schema (Planned)
```sql
CREATE TABLE patients (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    patient_code VARCHAR(50) UNIQUE,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    city VARCHAR(100),
    province VARCHAR(100),
    postal_code VARCHAR(10),
    id_number VARCHAR(50),
    insurance_number VARCHAR(50),
    emergency_contact_name VARCHAR(255),
    emergency_contact_phone VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP
);
```

## API Endpoints (Planned)
- `GET /patients` - List all patients
- `POST /patients` - Create new patient
- `GET /patients/{id}` - View patient details
- `PUT /patients/{id}` - Update patient
- `DELETE /patients/{id}` - Soft delete patient
- `GET /patients/search` - Search patients

## To Implement
- [ ] Create migration
- [ ] Create model with relationships
- [ ] Create controller with CRUD
- [ ] Create Vue components
- [ ] Add validation rules
- [ ] Add search functionality
- [ ] Add export functionality
- [ ] Add medical history timeline
