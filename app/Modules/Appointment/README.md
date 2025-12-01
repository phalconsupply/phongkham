# Appointment Module

## Purpose
Manages appointment scheduling, calendar view, and appointment reminders.

## Structure
```
Appointment/
├── Controllers/
│   ├── AppointmentController.php
│   └── CalendarController.php
├── Models/
│   └── Appointment.php
├── Requests/
│   ├── StoreAppointmentRequest.php
│   └── UpdateAppointmentRequest.php
├── Resources/
│   └── AppointmentResource.php
└── Services/
    └── AppointmentService.php
```

## Planned Features
- Schedule appointments
- Calendar view (day/week/month)
- Appointment types (consultation, follow-up, procedure)
- Recurring appointments
- Appointment status tracking
- Patient reminders (SMS/Email)
- Doctor availability management
- Conflict detection
- Waitlist management
- Touch-optimized calendar interface

## Database Schema (Planned)
```sql
CREATE TABLE appointments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    appointment_code VARCHAR(50) UNIQUE,
    patient_id BIGINT,
    doctor_id BIGINT,
    appointment_date DATE,
    start_time TIME,
    end_time TIME,
    appointment_type ENUM('consultation', 'follow_up', 'procedure', 'checkup'),
    status ENUM('scheduled', 'confirmed', 'arrived', 'in_progress', 'completed', 'cancelled', 'no_show'),
    reason TEXT,
    notes TEXT,
    reminder_sent BOOLEAN DEFAULT FALSE,
    reminder_sent_at TIMESTAMP NULL,
    created_by BIGINT,
    cancelled_by BIGINT NULL,
    cancelled_at TIMESTAMP NULL,
    cancellation_reason TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE doctor_schedules (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    doctor_id BIGINT,
    day_of_week TINYINT, -- 0=Sunday, 6=Saturday
    start_time TIME,
    end_time TIME,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES users(id)
);

CREATE TABLE schedule_exceptions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    doctor_id BIGINT,
    exception_date DATE,
    start_time TIME NULL,
    end_time TIME NULL,
    is_available BOOLEAN DEFAULT FALSE,
    reason VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES users(id)
);
```

## API Endpoints (Planned)
- `GET /appointments` - List appointments
- `POST /appointments` - Create new appointment
- `GET /appointments/{id}` - View appointment details
- `PUT /appointments/{id}` - Update appointment
- `DELETE /appointments/{id}` - Cancel appointment
- `GET /appointments/calendar` - Calendar view data
- `GET /appointments/available-slots` - Get available time slots
- `POST /appointments/{id}/check-in` - Check-in patient
- `POST /appointments/{id}/complete` - Complete appointment
- `GET /doctors/{id}/schedule` - Doctor schedule
- `POST /doctors/{id}/schedule` - Set doctor availability

## Touch-Optimized Calendar

### Design Considerations
- Large touch targets (minimum 48x48px)
- Swipe gestures for navigation
- Pull-to-refresh
- Clear visual indicators for appointment status
- Easy drag-and-drop rescheduling
- Quick actions (call patient, check-in, cancel)

### Calendar Views
- Day view: Hourly slots with all appointments
- Week view: 7-day overview
- Month view: Monthly calendar with appointment count
- List view: Scrollable list of upcoming appointments

## To Implement
- [ ] Create migrations
- [ ] Create models with relationships
- [ ] Create controllers
- [ ] Create calendar Vue component
- [ ] Add appointment form with time picker
- [ ] Add doctor availability management
- [ ] Add conflict detection logic
- [ ] Add appointment reminders (queue job)
- [ ] Add status transition workflow
- [ ] Add recurring appointment logic
- [ ] Add waitlist feature
- [ ] Add drag-and-drop rescheduling
- [ ] Add SMS/Email reminder integration
