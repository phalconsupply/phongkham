# Prescription Module

## Purpose
Manages medication prescriptions and integrates with external pharmacy systems.

## Structure
```
Prescription/
├── Controllers/
│   └── PrescriptionController.php
├── Models/
│   ├── Prescription.php
│   └── PrescriptionItem.php
├── Requests/
│   ├── StorePrescriptionRequest.php
│   └── UpdatePrescriptionRequest.php
├── Resources/
│   └── PrescriptionResource.php
└── Services/
    ├── PrescriptionService.php
    └── PrescriptionGatewayService.php
```

## Planned Features
- Create prescriptions
- Add multiple medications
- Specify dosage, frequency, duration
- Drug interaction warnings
- Send to pharmacy via gateway
- Track prescription status
- Refill management
- Print prescription
- E-prescription support

## Database Schema (Planned)
```sql
CREATE TABLE prescriptions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    prescription_code VARCHAR(50) UNIQUE,
    patient_id BIGINT,
    encounter_id BIGINT,
    doctor_id BIGINT,
    prescription_date DATE,
    status ENUM('draft', 'active', 'sent', 'filled', 'cancelled'),
    notes TEXT,
    pharmacy_id VARCHAR(100),
    pharmacy_status VARCHAR(50),
    sent_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (encounter_id) REFERENCES encounters(id),
    FOREIGN KEY (doctor_id) REFERENCES users(id)
);

CREATE TABLE prescription_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    prescription_id BIGINT,
    medication_name VARCHAR(255),
    medication_code VARCHAR(50),
    dosage VARCHAR(100),
    frequency VARCHAR(100),
    duration VARCHAR(100),
    quantity INT,
    unit VARCHAR(50),
    instructions TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id)
);
```

## Gateway Integration

The module uses the PrescriptionGateway adapter pattern:

```php
// app/Services/PrescriptionGateway/PrescriptionGatewayInterface.php
interface PrescriptionGatewayInterface {
    public function sendPrescription(array $prescriptionData): array;
    public function checkStatus(string $prescriptionId): array;
    public function cancelPrescription(string $prescriptionId): bool;
}
```

### Available Implementations
- **DummyPrescriptionGateway**: For development/testing
- **Custom Gateways**: Create new implementations for specific pharmacy systems

### Adding New Gateway
1. Create class implementing `PrescriptionGatewayInterface`
2. Add configuration to `.env`:
```env
PRESCRIPTION_GATEWAY_PROVIDER=pharmacy_name
PRESCRIPTION_GATEWAY_API_URL=https://api.pharmacy.com
PRESCRIPTION_GATEWAY_API_KEY=your_api_key
```
3. Bind in service provider:
```php
app()->bind(PrescriptionGatewayInterface::class, YourGateway::class);
```

## API Endpoints (Planned)
- `GET /prescriptions` - List prescriptions
- `POST /prescriptions` - Create new prescription
- `GET /prescriptions/{id}` - View prescription details
- `PUT /prescriptions/{id}` - Update prescription
- `POST /prescriptions/{id}/send` - Send to pharmacy
- `GET /prescriptions/{id}/status` - Check pharmacy status
- `POST /prescriptions/{id}/cancel` - Cancel prescription
- `GET /patients/{id}/prescriptions` - Patient prescription history

## To Implement
- [ ] Create migrations
- [ ] Create models with relationships
- [ ] Create controllers
- [ ] Create Vue prescription form
- [ ] Add medication search/autocomplete
- [ ] Integrate gateway service
- [ ] Add drug interaction checker (optional)
- [ ] Add prescription template
- [ ] Add print/PDF generation
- [ ] Add refill tracking
- [ ] Implement real pharmacy gateway
