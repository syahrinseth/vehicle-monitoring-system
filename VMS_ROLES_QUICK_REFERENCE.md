# Vehicle Monitoring System - Roles Quick Reference Guide

## Role Comparison Matrix

| Aspect | Admin | Institute Authority | Guard | Student |
|--------|-------|-------------------|-------|---------|
| **Panel URL** | `/admin` | `/authority` | `/guard` | `/student` |
| **Color** | Blue | Purple | Orange | Green |
| **Primary Function** | System oversight | Final approval | Access control | Vehicle registration |
| **Data Visibility** | All system data | Registrations only | Own scans only | Own data only |
| **Panel Access** | role='admin' | role='institute_authority' | role='guard' | role='student' |

---

## Resources Accessibility Matrix

### Admin Panel Resources
```
✓ Users              [CRUD] - Create/Read/Update/Delete all users
✓ Students          [CRUD] - Manage student profiles
✓ Vehicles          [CRUD] - Manage all vehicles
✓ Registrations     [CRUD + Actions] - Full control + verify/reject/generate sticker
✓ Digital Stickers  [Read + Actions] - View, revoke, download QR
✓ Check-In Logs     [Read-Only] - View all guard scans
✓ Vehicle Types     [CRUD] - Manage vehicle categories
```

### Authority Panel Resources
```
✓ Registrations     [Read + Actions] - View & approve/reject verified registrations
```

### Guard Panel Resources
```
✓ Scan/Lookup       [Execute] - Scan QR code or lookup by plate
✓ Scan History      [Read-Only] - View own check-in logs
```

### Student Panel Resources
```
✓ My Vehicles       [CRUD] - Create/Read/Update/Delete own vehicles
✓ My Registrations  [Create + Read] - Submit new & view own registrations
✓ Sticker Actions   [Download/View/Renew] - Manage own sticker
```

---

## Registration Status Workflow & Approvals

```
┌─────────────────┐
│ Student Submits │ (status='pending')
└────────┬────────┘
         │
    ┌────▼──────────────────────────────┐
    │ Admin (Optional Verification Step) │
    │ - Can verify or skip               │
    └────┬──────────────────────────────┘
         │
    ┌────▼──────────────────┐
    │ Status: 'verified'    │ (if verified)
    │ (or still 'pending')  │ (if skipped)
    └────┬──────────────────┘
         │
    ┌────▼────────────────────────────┐
    │ Authority Approval Step          │
    │ - Reviews student & vehicle info │
    │ - Approves with sticker dates    │
    │ - Or rejects with reason         │
    └────┬───────────────┬──────────────┘
         │               │
    ┌────▼──────────┐ ┌─▼──────────────┐
    │ APPROVED      │ │ REJECTED       │
    │ + Sticker     │ │ + Reason Sent  │
    └────┬──────────┘ └────────────────┘
         │
    ┌────▼───────────────┐
    │ DigitalSticker     │ (UUID token + QR PNG)
    │ Valid Until: [date]│
    └────────────────────┘
```

---

## Key User Workflows

### ADMIN Workflow
```
Login → Dashboard
    ├─ Users: Create guards/authorities/students
    ├─ Students: Link user accounts to student profiles
    ├─ Vehicles: Register vehicles to students
    ├─ Registrations: 
    │   ├─ Verify pending registrations
    │   └─ Generate stickers for approved ones
    ├─ Digital Stickers: Monitor validity, revoke if needed
    ├─ Check-In Logs: View all guard scanning activity
    └─ Vehicle Types: Add/modify vehicle categories
```

### AUTHORITY Workflow
```
Login → Dashboard
    └─ Registrations:
        ├─ View verified registrations
        ├─ Approve with sticker validity dates
        │   → Creates DigitalSticker
        │   → Issues QR code to student
        │   → Notifies student
        └─ Reject with reason
            → Student can request renewal
```

### GUARD Workflow
```
Login → Dashboard
    ├─ Scan/Lookup Page:
    │   ├─ Scan QR Code → Verify sticker → Grant/Deny access
    │   └─ Enter Plate # → Lookup sticker → Grant/Deny access
    │
    └─ Scan History:
        └─ View own scans with access results
```

### STUDENT Workflow
```
Login → Dashboard
    ├─ My Vehicles:
    │   ├─ Create new vehicle
    │   │   └─ Upload registration document
    │   ├─ Edit vehicle details
    │   └─ Delete vehicle
    │
    └─ My Registrations:
        ├─ Create registration (submit for approval)
        │   └─ Select vehicle
        │
        └─ Track registration:
            ├─ Status: pending → verified → approved
            ├─ Download sticker QR when approved
            ├─ View sticker on phone
            └─ Request renewal when expires
```

---

## Data Isolation & Security

### Admin
- ✓ Can view ALL data
- ✓ Can access ALL resources
- ✓ No data filtering

### Authority
- ✓ Views only Registrations in [verified, approved, rejected] status
- ✓ Cannot see users, vehicles, or student management
- ✓ Cannot create/edit registrations

### Guard
- ✓ Views only own CheckInLog records
- ✓ Query filtered: `WHERE guard_id = auth()->id()`
- ✓ Cannot see other guards' scans
- ✓ Cannot view student or registration data

### Student
- ✓ Views only own Vehicles and Registrations
- ✓ Query filtered: `WHERE student_id = auth()->user()->student->id`
- ✓ Cannot see other students' data
- ✓ Cannot approve or verify registrations

---

## Digital Sticker Lifecycle

### Generation (Admin or Authority)
```
Registration approved → "Generate Sticker" button appears
    ↓
Authority/Admin sets validity dates
    ↓
QRCodeService::generateForRegistration() called
    ↓
Creates DigitalSticker record:
  - registration_id: linked registration
  - qr_code_token: UUID (unique identifier)
  - qr_code_image_path: PNG file path
  - validity_start_date: from date
  - validity_end_date: to date
  - status: 'valid'
  - generated_at: now()
```

### Usage (Guard)
```
Guard scans QR code
    ↓
System retrieves DigitalSticker by qr_code_token
    ↓
System validates:
  - status = 'valid' (not revoked)
  - current date between validity dates
    ↓
✓ Valid → ACCESS GRANTED
✗ Expired/Revoked → ACCESS DENIED
```

### Expiration (Student)
```
Current date > validity_end_date
    ↓
Status automatically becomes: 'expired'
    ↓
Student sees "Sticker Status: expired"
    ↓
"Request Renewal" button appears
    ↓
Student clicks → New Registration created
    ↓
Workflow starts again (pending → approved → new sticker)
```

### Revocation (Admin)
```
Admin views Digital Stickers → finds active sticker
    ↓
Admin clicks "Revoke" → Confirmation required
    ↓
Sticker status changed: 'valid' → 'revoked'
    ↓
Guard scans same QR → ACCESS DENIED (Sticker revoked)
```

---

## Check-In Log Entry

### What Gets Recorded
```
Each scan creates CheckInLog record:
{
  vehicle_id: Vehicle scanned,
  digital_sticker_id: Sticker used (if QR), null if plate lookup failed,
  guard_id: Which guard scanned,
  scan_method: 'qr' or 'plate',
  access_granted: true/false,
  denial_reason: null (if granted), 'Sticker expired' (if denied),
  scanner_ip: IP address of guard's device,
  notes: null (optional field),
  scanned_at: Timestamp of scan
}
```

### Access Denied Reasons
- "Vehicle not found in system"
- "No valid sticker"
- "Sticker revoked"
- "Sticker expired"
- "QR token not found"

---

## Role Access Control Summary

### Can Create Records
| Role | Users | Students | Vehicles | Registrations | Stickers | CheckInLogs |
|------|-------|----------|----------|---------------|----------|-------------|
| Admin | ✓ | ✓ | ✓ | ✓ | ✓ (via action) | ✗ |
| Authority | ✗ | ✗ | ✗ | ✗ | ✓ (via approval) | ✗ |
| Guard | ✗ | ✗ | ✗ | ✗ | ✗ | ✓ (auto) |
| Student | ✗ | ✗ | ✓ (own) | ✓ (own) | ✗ | ✗ |

### Can Read Records
| Role | Users | Students | Vehicles | Registrations | Stickers | CheckInLogs |
|------|-------|----------|----------|---------------|----------|-------------|
| Admin | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Authority | ✗ | ✗ | ✗ | ✓ (verified/approved) | ✗ | ✗ |
| Guard | ✗ | ✗ | ✗ (indirect) | ✗ | ✓ (via scan) | ✓ (own) |
| Student | ✗ | ✗ | ✓ (own) | ✓ (own) | ✓ (own) | ✗ |

### Can Update Records
| Role | Users | Students | Vehicles | Registrations | Stickers | CheckInLogs |
|------|-------|----------|----------|---------------|----------|-------------|
| Admin | ✓ | ✓ | ✓ | ✓ | ✓ (actions) | ✗ |
| Authority | ✗ | ✗ | ✗ | ✓ (approve/reject) | ✗ | ✗ |
| Guard | ✗ | ✗ | ✗ | ✗ | ✗ | ✗ |
| Student | ✗ | ✗ | ✓ (own) | ✗ (can request renewal) | ✗ | ✗ |

### Can Delete Records
| Role | Users | Students | Vehicles | Registrations | Stickers | CheckInLogs |
|------|-------|----------|----------|---------------|----------|-------------|
| Admin | ✓ | ✓ | ✓ | ✓ | ✗ | ✗ |
| Authority | ✗ | ✗ | ✗ | ✗ | ✗ | ✗ |
| Guard | ✗ | ✗ | ✗ | ✗ | ✗ | ✗ |
| Student | ✗ | ✗ | ✓ (own) | ✗ | ✗ | ✗ |

---

## File Organization

### By Role (Filament Providers & Resources)
```
app/Providers/Filament/
├── AdminPanelProvider.php
├── AuthorityPanelProvider.php (Institute Authority)
├── GuardPanelProvider.php
└── StudentPanelProvider.php

app/Filament/
├── Admin/
│   ├── Resources/
│   │   ├── UserResource.php
│   │   ├── StudentResource.php
│   │   ├── VehicleResource.php
│   │   ├── RegistrationResource.php
│   │   ├── DigitalStickerResource.php
│   │   ├── VehicleTypeResource.php
│   │   └── CheckInLogResource.php
│   └── Pages/ (Dashboard, etc.)
├── Authority/
│   ├── Resources/
│   │   └── RegistrationResource.php
│   └── Pages/ (Dashboard, etc.)
├── Guard/
│   ├── Resources/
│   │   └── CheckInLogResource.php
│   ├── Pages/
│   │   └── ScanVehicle.php (Custom page)
│   └── Pages/ (Dashboard, etc.)
└── Student/
    ├── Resources/
    │   ├── VehicleResource.php
    │   └── RegistrationResource.php
    └── Pages/ (Dashboard, etc.)
```

### Database & Models
```
app/Models/
├── User.php
├── Student.php
├── Vehicle.php
├── VehicleType.php
├── Registration.php
├── DigitalSticker.php
└── CheckInLog.php

database/migrations/
├── 0001_01_01_000000_create_users_table.php
├── 2026_03_30_070421_create_students_table.php
├── 2026_03_30_070421_create_vehicles_table.php
├── 2026_03_30_070421_create_vehicle_types_table.php
├── 2026_03_30_070421_create_registrations_table.php
├── 2026_03_30_070422_create_digital_stickers_table.php
└── 2026_03_30_070422_create_check_in_logs_table.php
```

---

## Quick Lookup: Which Role Does What?

### Student Registration Process
1. **Student**: Creates vehicle in "My Vehicles"
2. **Student**: Submits registration in "My Registrations"
3. **Admin**: (Optional) Verifies the registration
4. **Authority**: Approves & issues digital sticker
5. **Student**: Downloads sticker QR code
6. **Guard**: Scans sticker at gate, grants access

### Access Control
- **Guard**: Scans & makes access decision
- **System**: Logs every scan (who, what, when, granted/denied)
- **Admin**: Views all scans in reports

### Sticker Management
- **Authority**: Creates sticker with validity dates
- **Admin**: Can view/revoke/manage all stickers
- **Student**: Can download and share sticker
- **Guard**: Scans and verifies validity

### User Management
- **Admin**: Full control over all users and roles
- **Authority**: Can only view registrations
- **Guard**: Cannot manage any users
- **Student**: Cannot manage any users

---

## Common User Scenarios

### Scenario 1: New Student Registration
```
1. Student creates account and logs in
2. Student registers their vehicle (car details + upload doc)
3. Student submits registration (request approval)
4. Admin verifies the vehicle details are correct
5. Authority approves and issues QR sticker (1 year validity)
6. Student downloads sticker
7. Next visit: Guard scans QR → Access granted ✓
```

### Scenario 2: Guard Denies Access
```
1. Guard scans sticker at gate
2. System finds sticker but status='revoked' (admin revoked it earlier)
3. Guard sees: "Access Denied - Sticker revoked"
4. Guard notes reason in scan log
5. Admin can see in Check-In Logs that access was denied
6. Authority/Admin follow up with student
```

### Scenario 3: Sticker Expiration & Renewal
```
1. One year passes, sticker validity_end_date is today
2. Student sees sticker status = "expired"
3. Student clicks "Request Renewal"
4. New registration created (status='pending')
5. Authority approves with new 1-year validity
6. Student downloads new sticker
7. Next visit: Guard scans new sticker → Access granted ✓
```

### Scenario 4: Vehicle Lookup (No QR)
```
1. Guard at gate, QR scanner not working
2. Student gives vehicle plate number
3. Guard enters plate in "Lookup" form
4. System finds vehicle and checks latest sticker
5. If sticker valid: "Access Granted"
6. If no sticker: "Access Denied - No valid sticker"
7. Scan logged with method='plate' instead of 'qr'
```

