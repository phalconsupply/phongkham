# Encounter Module

## Purpose
Manages clinical encounters, consultations, examinations, diagnoses, and treatment notes.

## Structure
```
Encounter/
├── Controllers/
│   └── EncounterController.php
├── Models/
│   ├── Encounter.php
│   ├── Diagnosis.php
│   └── VitalSign.php
├── Requests/
│   ├── StoreEncounterRequest.php
│   └── UpdateEncounterRequest.php
├── Resources/
│   └── EncounterResource.php
└── Services/
    └── EncounterService.php
```

## Planned Features
- Create clinical encounters
- Record vital signs (BP, HR, temp, weight, height)
- Document chief complaints
- Record physical examination findings
- Add diagnoses (ICD-10 codes)
- Treatment plans and notes
- Link to prescriptions
- Encounter history timeline
- Print encounter summary

## Database Schema (Planned)
```sql
CREATE TABLE encounters (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    patient_id BIGINT,
    encounter_code VARCHAR(50) UNIQUE,
    encounter_date DATETIME,
    encounter_type ENUM('consultation', 'follow_up', 'emergency'),
    chief_complaint TEXT,
    history_present_illness TEXT,
    physical_examination TEXT,
    assessment TEXT,
    plan TEXT,
    notes TEXT,
    doctor_id BIGINT,
    status ENUM('draft', 'completed', 'cancelled'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES users(id)
);

CREATE TABLE vital_signs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    encounter_id BIGINT,
    systolic_bp INT,
    diastolic_bp INT,
    heart_rate INT,
    temperature DECIMAL(4,1),
    weight DECIMAL(5,2),
    height DECIMAL(5,2),
    bmi DECIMAL(5,2),
    oxygen_saturation INT,
    respiratory_rate INT,
    recorded_at TIMESTAMP,
    FOREIGN KEY (encounter_id) REFERENCES encounters(id)
);

CREATE TABLE diagnoses (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    encounter_id BIGINT,
    icd10_code VARCHAR(10),
    diagnosis_name TEXT,
    diagnosis_type ENUM('primary', 'secondary'),
    notes TEXT,
    FOREIGN KEY (encounter_id) REFERENCES encounters(id)
);
```

## API Endpoints (Planned)
- `GET /encounters` - List encounters
- `POST /encounters` - Create new encounter
- `GET /encounters/{id}` - View encounter details
- `PUT /encounters/{id}` - Update encounter
- `POST /encounters/{id}/vitals` - Add vital signs
- `POST /encounters/{id}/diagnoses` - Add diagnosis
- `GET /patients/{id}/encounters` - Patient encounter history

## To Implement
- [ ] Create migrations
- [ ] Create models with relationships
- [ ] Create controllers
- [ ] Create Vue components for encounter form
- [ ] Add vital signs component
- [ ] Add ICD-10 diagnosis search
- [ ] Add SOAP note template
- [ ] Add encounter timeline view
- [ ] Add print functionality
