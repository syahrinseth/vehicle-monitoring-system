# Vehicle Monitoring System - Complete Role & Workflow Documentation

Welcome! This documentation provides a comprehensive analysis of all user roles and workflows in the Vehicle Monitoring System (VMS).

## Quick Navigation

### Start Here
1. **[DOCUMENTATION_SUMMARY.txt](./DOCUMENTATION_SUMMARY.txt)** - High-level overview and common questions
2. **[VMS_ROLES_QUICK_REFERENCE.md](./VMS_ROLES_QUICK_REFERENCE.md)** - Quick lookup tables and comparisons

### Detailed Reading
3. **[VMS_ROLES_WORKFLOW_DOCUMENTATION.md](./VMS_ROLES_WORKFLOW_DOCUMENTATION.md)** - Complete detailed documentation
4. **[VMS_SYSTEM_DIAGRAMS.md](./VMS_SYSTEM_DIAGRAMS.md)** - Visual diagrams and system architecture

---

## The Four Roles at a Glance

| Role | Panel | Color | Primary Function |
|------|-------|-------|------------------|
| **Admin** | `/admin` | Blue | System oversight & user management |
| **Institute Authority** | `/authority` | Purple | Final approval & sticker issuance |
| **Guard** | `/guard` | Orange | Vehicle access control |
| **Student** | `/student` | Green | Vehicle registration |

---

## Complete Role Breakdown

### 1. ADMIN Role (`role = 'admin'`)
**Access**: `/admin`

**What They Do:**
- Create and manage all users (any role)
- Create and manage student profiles
- Register and manage all vehicles
- Verify vehicle registrations (optional step)
- View and manage all digital stickers
- Monitor all guard check-in logs
- Configure vehicle types
- Complete system administration

**Resources Available:**
- Users (CRUD)
- Students (CRUD)
- Vehicles (CRUD)
- Registrations (CRUD + Verify/Reject/Generate Sticker)
- Digital Stickers (View/Revoke/Download QR)
- Check-In Logs (View All)
- Vehicle Types (CRUD)

**Key Capabilities:**
- Can access all data in system
- Can verify pending registrations
- Can generate digital stickers (but Authority usually does this)
- Can revoke stickers if needed
- Full administrative control

---

### 2. INSTITUTE AUTHORITY Role (`role = 'institute_authority'`)
**Access**: `/authority`

**What They Do:**
- Review verified vehicle registrations
- Approve or reject registrations
- Issue digital stickers with validity dates
- Track approval history

**Resources Available:**
- Registrations (View + Approve/Reject/Issue Sticker)

**Key Capabilities:**
- View only verified/approved/rejected registrations
- Set sticker validity dates when approving
- Cannot create, edit, or delete registrations manually
- Reject with reason (student can request renewal)
- All approvals are tracked with timestamp and authority user ID

**Workflow:**
1. Views pending approval registrations
2. Opens "Approve & Issue Sticker" form
3. Sets validity start date (default: today)
4. Sets validity end date (default: +1 year)
5. Approves → DigitalSticker created → Student notified

---

### 3. GUARD Role (`role = 'guard'`)
**Access**: `/guard`

**What They Do:**
- Scan QR codes from vehicle stickers
- Lookup vehicles by plate number
- Grant or deny campus access
- View personal scan history

**Resources Available:**
- Scan/Lookup Page (Custom page with QR + Plate inputs)
- Scan History (View own check-in logs)

**Key Capabilities:**
- Two scanning methods: QR code or plate number lookup
- Receives real-time access decision (GRANTED/DENIED)
- Sees vehicle and student information
- All scans automatically logged with result
- Can only view their own scan history
- Cannot see other guards' scans

**Scanning Process:**
1. Guard scans QR code or enters plate number
2. System validates sticker (status='valid' & within date range)
3. System displays: vehicle info, student name, sticker status
4. System decides: ACCESS GRANTED or DENIED (with reason)
5. System logs: vehicle_id, sticker_id, guard_id, method, result
6. Guard sees: In-app notification with decision

---

### 4. STUDENT Role (`role = 'student'`)
**Access**: `/student` (Self-registration enabled)

**What They Do:**
- Create personal vehicle profiles
- Submit vehicles for registration
- Track registration approval status
- Download digital stickers
- Request sticker renewal
- View personal sticker on phone

**Resources Available:**
- My Vehicles (CRUD own vehicles)
- My Registrations (Create + View + Manage stickers)

**Key Capabilities:**
- Create vehicles with full details (type, plate, color, make, model, year, engine/chassis)
- Upload vehicle registration document (PDF/JPG, max 5MB)
- Submit registrations for approval
- Track status progression: pending → verified → approved → sticker issued
- Download sticker QR code as PNG image
- View sticker on public page (`/sticker/{token}`) to show to guard
- Request renewal when sticker expires
- Cannot see other students' vehicles or registrations
- Cannot approve or verify registrations
- Cannot delete approved registrations

**Registration Workflow:**
1. Create vehicle in "My Vehicles"
2. Create registration in "My Registrations" (select vehicle)
3. Wait for Admin to verify (if applicable)
4. Wait for Authority to approve
5. When approved: Download sticker QR
6. Show QR to guard at gate
7. After 1 year: Request renewal

---

## Key System Concepts

### Digital Sticker System
- **What**: UUID-based QR code token + PNG image
- **When Created**: When Authority approves registration
- **Validity**: Has start and end dates
- **Status**: Can be 'valid', 'expired', or 'revoked'
- **Scanning**: Guard scans QR → System validates → Logs access decision

### Registration Status Flow
```
pending (student submits)
  ↓ [Admin verifies - optional]
verified (or stays pending if skipped)
  ↓ [Authority reviews]
approved (sticker issued) OR rejected (with reason)
```

### Check-In Logs
- Created automatically every time guard scans
- Records: vehicle, sticker, guard, method, access granted/denied, reason
- Immutable (cannot be edited or deleted)
- Used for auditing and reporting

### Data Isolation
- **Admin**: Sees all data
- **Authority**: Only sees verified/approved/rejected registrations
- **Guard**: Only sees own scan history (filtered by guard_id)
- **Student**: Only sees own vehicles/registrations (filtered by student_id)

---

## Common Workflows

### Student Vehicle Registration
```
1. Student logs in → Dashboard
2. Go to "My Vehicles"
3. Click "Create Vehicle"
4. Fill: Type, Plate No., Color, Brand, Model, Year, Engine No., Chassis No.
5. Upload registration document (optional)
6. Save → Vehicle created
7. Go to "My Registrations"
8. Click "Create Registration"
9. Select vehicle from dropdown
10. Submit → Registration created (status=pending)
11. [Wait for Admin verification - optional]
12. [Wait for Authority approval]
13. Authority approves → Digital sticker created
14. Student clicks "Download QR"
15. Student shows sticker to guard at gate
16. Guard scans → Access decision made and logged
```

### Guard Access Control
```
1. Guard opens Scan/Lookup page
2. Either:
   a) Scans QR code from student's phone
   b) Enters vehicle plate number manually
3. System searches for vehicle and sticker
4. System validates sticker (status & date range)
5. System displays: Vehicle info, Student info, Sticker status
6. System shows: ACCESS GRANTED or DENIED
7. Guard receives notification
8. System logs check-in with result
9. Student granted/denied entry
```

### Authority Approval
```
1. Authority logs in → Registrations
2. Sees list of verified registrations awaiting approval
3. Clicks on registration
4. Reviews: Student name, matric, vehicle info, verification info
5. Clicks "Approve & Issue Sticker"
6. Form appears for validity dates
7. Sets: Valid From (default: today), Valid Until (default: +1 year)
8. Confirms → Registration approved, DigitalSticker created
9. Student receives notification
10. Student can download sticker
```

---

## Security & Data Privacy

### Role-Based Access Control
- Each role can only access their assigned panel
- User.canAccessPanel(Panel) enforces role checking
- is_active=false users cannot login (even if valid role)

### Data Filtering
- All queries filtered by role/user/student at database level
- Query modification prevents data leakage
- Student cannot see other students' data
- Guard cannot see other guards' scans

### Audit Trail
- Registration approvals tracked: verified_by, approved_by, timestamps
- All check-in scans logged: guard_id, scan_method, access_granted, denial_reason
- Student actions tracked: submission, download timestamps

---

## File Organization

### Application Code
```
app/
├── Filament/
│   ├── Admin/Resources/
│   │   ├── UserResource.php
│   │   ├── StudentResource.php
│   │   ├── VehicleResource.php
│   │   ├── RegistrationResource.php
│   │   ├── DigitalStickerResource.php
│   │   ├── VehicleTypeResource.php
│   │   └── CheckInLogResource.php
│   ├── Authority/Resources/
│   │   └── RegistrationResource.php
│   ├── Guard/Resources/
│   │   └── CheckInLogResource.php
│   ├── Guard/Pages/
│   │   └── ScanVehicle.php
│   └── Student/Resources/
│       ├── VehicleResource.php
│       └── RegistrationResource.php
├── Models/
│   ├── User.php
│   ├── Student.php
│   ├── Vehicle.php
│   ├── VehicleType.php
│   ├── Registration.php
│   ├── DigitalSticker.php
│   └── CheckInLog.php
└── Providers/
    └── Filament/
        ├── AdminPanelProvider.php
        ├── InstituteAuthorityPanelProvider.php
        ├── GuardPanelProvider.php
        └── StudentPanelProvider.php
```

### Database
```
database/
├── migrations/
│   ├── create_users_table.php
│   ├── create_students_table.php
│   ├── create_vehicles_table.php
│   ├── create_vehicle_types_table.php
│   ├── create_registrations_table.php
│   ├── create_digital_stickers_table.php
│   └── create_check_in_logs_table.php
```

---

## Frequently Asked Questions

**Q: How is role determined?**
A: Via the `role` column in the `users` table. Can be: 'admin', 'institute_authority', 'guard', or 'student'.

**Q: Can a user have multiple roles?**
A: No. Each user has exactly one role.

**Q: What happens if an inactive user tries to login?**
A: They cannot login. The `canAccessPanel()` method checks `is_active=true`.

**Q: Who creates user accounts?**
A: Admin creates them. Students can self-register (panel has registration enabled).

**Q: What happens when a sticker expires?**
A: Status changes to 'expired'. Student sees it and can click "Request Renewal" to submit new registration.

**Q: Can a Guard revoke a sticker?**
A: No. Only Admin can revoke stickers.

**Q: Can Authority see Check-In Logs?**
A: No. Authority only sees Registrations.

**Q: Can a Student delete a vehicle with approved registration?**
A: No. They can delete vehicles, but not their approval history.

**Q: What data is logged in Check-In?**
A: vehicle_id, digital_sticker_id, guard_id, scan_method (qr/plate), access_granted (true/false), denial_reason, scanner_ip, scanned_at timestamp.

**Q: Can Guard lookup a vehicle with no sticker?**
A: Yes. System will show "No valid sticker" and deny access.

**Q: How many times can a sticker be used?**
A: Unlimited times. Same sticker used for every gate entry until it expires.

---

## Testing Checklist

- [ ] New student can register account
- [ ] Student can create and edit vehicles
- [ ] Student can submit registration for approval
- [ ] Admin can verify registration
- [ ] Authority can approve and issue sticker
- [ ] Student can download sticker QR
- [ ] Guard can scan QR code
- [ ] Guard can lookup by plate number
- [ ] Guard receives correct access decision
- [ ] Check-in logged with correct data
- [ ] Student cannot see other students' data
- [ ] Guard cannot see other guards' scans
- [ ] Authority cannot access user management
- [ ] Sticker expires and student can request renewal
- [ ] Admin can revoke sticker
- [ ] Inactive user cannot login

---

## Documentation Files

1. **DOCUMENTATION_SUMMARY.txt** - Start here for overview
2. **VMS_ROLES_QUICK_REFERENCE.md** - Quick reference tables
3. **VMS_ROLES_WORKFLOW_DOCUMENTATION.md** - Complete detailed guide
4. **VMS_SYSTEM_DIAGRAMS.md** - Visual diagrams and architecture
5. **README_ROLES_AND_WORKFLOWS.md** - This file

---

## Version Info

- **Last Updated**: March 30, 2026
- **System**: Vehicle Monitoring System (VMS)
- **Framework**: Laravel with Filament
- **Roles**: 4 (Admin, Institute Authority, Guard, Student)
- **Models**: 7 (User, Student, Vehicle, VehicleType, Registration, DigitalSticker, CheckInLog)

---

For detailed information on any specific role or workflow, refer to the comprehensive documentation files listed above.

