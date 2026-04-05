# Vehicle Monitoring System - User Roles and Workflows Documentation

## System Overview
The Vehicle Monitoring System (VMS) is a Filament-based Laravel application that manages vehicle registrations, digital stickers, and access control at an institute. The system has four distinct user roles with separate Filament panels, each with specific responsibilities and capabilities.

---

## ROLE 1: ADMIN

### 1. Role Identification
- **Identifier**: `role = 'admin'`
- **Filament Panel**: `admin` (default panel at `/admin`)
- **Panel Color**: Blue
- **Determined by**: The `canAccessPanel()` method in User model checks if `role === 'admin'`

### 2. Access & Navigation
- **Panel URL**: `/admin/`
- **Login Route**: `/admin/login`
- **Navigation Groups**:
  - User Management
  - Vehicle Management
  - Reports
  - Configuration

### 3. Main Features & Capabilities
- **System-wide user management**
- **Student and vehicle registration oversight**
- **Vehicle registration approval workflow management**
- **Digital sticker generation and management**
- **Check-in logs viewing and reporting**
- **Vehicle type configuration**
- **Complete system configuration**

### 4. Filament Resources & Navigation
| Resource | Capabilities | Icon |
|----------|-------------|------|
| **Users** | Create, Read, Update, Delete all users (all roles) | `heroicon-o-users` |
| **Students** | Create, Read, Update, Delete student profiles; View vehicle & registration counts | `heroicon-o-academic-cap` |
| **Vehicles** | Create, Read, Update, Delete vehicles; Associate with students | `heroicon-o-truck` |
| **Registrations** | Create, Read, Update, Delete registrations; Verify, Reject, Generate stickers | `heroicon-o-document-check` |
| **Digital Stickers** | View, Revoke stickers; Download QR codes; Monitor validity status | `heroicon-o-qr-code` |
| **Check-In Logs** | View-only access to all guard scan history across system | `heroicon-o-clipboard-document-list` |
| **Vehicle Types** | Create, Read, Update, Delete vehicle type categories | `heroicon-o-tag` |

### 5. Registration Workflow (Admin-Specific Actions)
```
Pending Registration
    ↓
Admin clicks "Verify" → Status = "Verified", verified_by = Admin ID, verified_at = now()
    ↓
Admin clicks "Reject" (optional) → Status = "Rejected", rejection_reason = [text], rejected_at = now()
    ↓
(If Verified) Admin clicks "Generate Sticker" → Creates DigitalSticker with QR code
    ↓
(If Rejected) Registration removed from approval queue
```

### 6. Sticker Management Actions
- **Generate Sticker**: Creates QR code when registration status = 'approved'
  - Requires: validity_start_date, validity_end_date
  - Creates: DigitalSticker record with UUID token and QR code image
- **Revoke Sticker**: Changes status from 'valid' to 'revoked'
- **Download QR**: Direct download of sticker QR code image
- **Monitor Status**: Track valid, expired, and revoked stickers

### 7. Related Models & Resources
- User (full CRUD control)
- Student (full CRUD control)
- Vehicle (full CRUD control)
- Registration (full CRUD + approval actions)
- DigitalSticker (read + manage actions)
- CheckInLog (read-only)
- VehicleType (full CRUD control)

### 8. Permissions & Constraints
- **Cannot**: View other users' personal panels
- **Cannot**: Access Guard or Student portals
- **Cannot**: Create stickers without approved registration
- **Cannot**: Approve own registrations (other admins should review)
- **Can**: Bypass all lower-level approvals
- **Can**: Manage all system configuration

---

## ROLE 2: INSTITUTE AUTHORITY

### 1. Role Identification
- **Identifier**: `role = 'institute_authority'`
- **Filament Panel**: `institute-authority` at `/authority/`
- **Panel Color**: Purple
- **Determined by**: The `canAccessPanel()` method in User model checks if `role === 'institute_authority'`

### 2. Access & Navigation
- **Panel URL**: `/authority/`
- **Login Route**: `/authority/login`
- **Navigation Groups**:
  - Registrations (primary focus)

### 3. Main Features & Capabilities
- **Final approval authority** for vehicle registrations
- **Issue digital stickers** during approval process
- **Reject registrations** with reasons
- **View registration details** in approval queue
- **Monitor verified registrations** awaiting approval
- **Track approval history** and audit trail

### 4. Filament Resources & Navigation
| Resource | Capabilities | Icon |
|----------|-------------|------|
| **Registrations** | Read-only view of verified/approved/rejected; Approve & Issue Sticker; Reject with reason | `heroicon-o-document-check` |

### 5. Registration Approval Workflow (Authority-Specific)
```
Verified Registration (from Admin)
    ↓
Authority views registration details with:
  - Student info (matric, name)
  - Vehicle info (plate, type, details)
  - Verification status & verified_by (guard/admin name)
    ↓
Authority clicks "Approve & Issue Sticker" → Modal opens for sticker validity dates
    ↓
Authority sets:
  - validity_start_date (default: today)
  - validity_end_date (default: today + 1 year)
    ↓
System performs:
  - Updates Registration: status = 'approved', approved_by = Authority ID, approved_at = now()
  - Calls QRCodeService::generateForRegistration()
  - Creates DigitalSticker with UUID token and QR code PNG image
    ↓
Authority receives confirmation notification
    ↓
Registration removed from approval queue
```

### 6. Rejection Workflow (Authority-Specific)
```
Verified or Pending Registration
    ↓
Authority clicks "Reject"
    ↓
Authority provides rejection_reason in form
    ↓
System updates:
  - status = 'rejected'
  - rejection_reason = [provided text]
  - rejected_at = now()
    ↓
Student is notified of rejection
Registration cannot proceed to approval
```

### 7. Related Models & Resources
- Registration (view + approval/rejection/sticker issuance)
  - Views student relationship
  - Views vehicle relationship
  - Tracks verified_by and verifiedBy relationship
  - Creates sticker relationship

### 8. Permissions & Constraints
- **Cannot**: Create registrations
- **Cannot**: View student or vehicle management resources
- **Cannot**: Access user management
- **Cannot**: View historical check-in logs
- **Cannot**: Verify registrations (admin/guard responsibility)
- **Can**: Only approve verified registrations
- **Can**: Issue and configure sticker validity dates
- **Can**: Reject any pending or verified registration

---

## ROLE 3: GUARD

### 1. Role Identification
- **Identifier**: `role = 'guard'`
- **Filament Panel**: `guard` at `/guard/`
- **Panel Color**: Orange
- **Determined by**: The `canAccessPanel()` method in User model checks if `role === 'guard'`

### 2. Access & Navigation
- **Panel URL**: `/guard/`
- **Login Route**: `/guard/login`
- **Navigation Groups**:
  - Primary: Scan/Lookup (custom page)
  - Secondary: Scan History (logs)

### 3. Main Features & Capabilities
- **Scan vehicles** using QR codes or plate numbers
- **Grant/deny access** based on digital sticker validity
- **Log all check-ins** (automatic)
- **View personal scan history**
- **Receive real-time notifications** for access decisions
- **Identify students and vehicles** on demand

### 4. Filament Resources & Navigation
| Resource | Capabilities | Icon |
|----------|-------------|------|
| **Scan/Lookup (Custom Page)** | Dual-mode scanning (QR code or plate lookup) | `heroicon-o-qr-code` |
| **Scan History (Check-In Logs)** | View-only list of own scans; Filter by access status & method | `heroicon-o-clipboard-document-list` |

### 5. Vehicle Scanning Workflow (Guard-Specific)

#### Option A: QR Code Scan
```
Guard opens "Scan/Lookup" page
    ↓
Guard scans QR code with device
    ↓
QR token extracted automatically (or pasted manually)
    ↓
System searches: DigitalSticker.where('qr_code_token', $token)
    ↓
Found: DigitalSticker exists
    ↓
System calls QRCodeService::verifyToken($token)
    ↓
System checks sticker validity:
  - IF sticker.status = 'valid' AND now() between validity_start_date and validity_end_date
      → ACCESS GRANTED ✓
  - ELSE IF sticker.status = 'revoked'
      → ACCESS DENIED (Sticker revoked)
  - ELSE IF now() after validity_end_date
      → ACCESS DENIED (Sticker expired)
    ↓
System displays:
  - Vehicle: registration_number, type, color, manufacturer, model
  - Student: name, matric_number
  - Sticker: status, valid_until date
  - Access: GRANTED or DENIED with reason
    ↓
System automatically logs CheckInLog:
  - vehicle_id, digital_sticker_id, guard_id, scan_method='qr'
  - access_granted=true/false, denial_reason=[if denied]
  - scanner_ip, scanned_at=now()
    ↓
Guard receives in-app notification: "Access Granted" or "Access Denied: [reason]"
```

#### Option B: Plate Number Lookup
```
Guard opens "Scan/Lookup" page
    ↓
Guard enters vehicle plate number (e.g., "ABC 1234")
    ↓
System searches: Vehicle.where('registration_number', strtoupper($plate))
    ↓
Found: Vehicle exists
    ↓
System retrieves: vehicle.latestSticker (valid sticker with latest created_at)
    ↓
No Sticker Found: ACCESS DENIED (No valid sticker)
    ↓
(If sticker exists) System verifies sticker using same logic as QR scan
    ↓
System displays vehicle and student information
    ↓
System logs CheckInLog:
  - vehicle_id, digital_sticker_id (if exists), guard_id, scan_method='plate'
  - access_granted=true/false, denial_reason
  - scanner_ip, scanned_at=now()
```

### 6. Scan History View
- **Data**: Personal scans only (filtered by `guard_id = auth()->id()`)
- **Columns**:
  - Plate No. (searchable)
  - Student Name (searchable)
  - Scan Method (QR or Plate badge)
  - Access Granted (boolean icon)
  - Denial Reason (toggle visible)
  - Scanned At (sortable, desc by default)
- **Filters**: By access status (Granted/Denied), scan method

### 7. Related Models & Resources
- CheckInLog (create + read own logs)
- Vehicle (read via relationship)
- DigitalSticker (verify validity via service)
- Student (read via relationship)
- User (own record)

### 8. Permissions & Constraints
- **Cannot**: Create/edit/delete any records
- **Cannot**: View registrations or approvals
- **Cannot**: View other guards' scan history
- **Cannot**: Manually create check-in logs
- **Cannot**: Issue stickers or approve registrations
- **Can**: Scan vehicles using QR or plate number
- **Can**: View only their own scan history
- **Can**: See real-time access decisions
- **Data Isolation**: All CheckInLog queries automatically filtered to current guard

---

## ROLE 4: STUDENT

### 1. Role Identification
- **Identifier**: `role = 'student'`
- **Filament Panel**: `student` at `/student/`
- **Panel Color**: Green
- **Determined by**: The `canAccessPanel()` method in User model checks if `role === 'student'`
- **Registration enabled**: Student panel allows self-registration

### 2. Access & Navigation
- **Panel URL**: `/student/`
- **Login Route**: `/student/login`
- **Registration Route**: Self-registration enabled (`.registration()` in panel config)
- **Navigation Groups**:
  - My Vehicles
  - My Registrations

### 3. Main Features & Capabilities
- **Register personal vehicles**
- **Submit vehicle registrations** for approval
- **Track registration status**
- **View and download digital stickers**
- **Renew expired stickers**
- **View public sticker display** (can show on phone to guard)
- **Manage vehicle information**

### 4. Filament Resources & Navigation
| Resource | Capabilities | Icon |
|----------|-------------|------|
| **My Vehicles** | Create, Read, Update, Delete own vehicles; Upload registration documents | `heroicon-o-truck` |
| **My Registrations** | Create, Read own registrations; View sticker status; Download QR; Request renewal | `heroicon-o-document-text` |

### 5. Vehicle Registration Workflow (Student-Specific)

#### Phase 1: Vehicle Creation
```
Student logs into portal → Dashboard
    ↓
Student clicks "My Vehicles" → "Create Vehicle"
    ↓
Student fills form:
  - Vehicle Type (dropdown: active types only)
  - Plate Number (unique, auto-uppercase)
  - Color
  - Manufacturer/Brand
  - Model
  - Year (1990-present+1)
  - Engine Number (optional)
  - Chassis Number (optional)
  - Registration Document (PDF/JPG, max 5MB, optional)
    ↓
Student clicks "Save"
    ↓
System creates Vehicle record linked to student.id
    ↓
Vehicle added to "My Vehicles" list
    ↓
Student can Edit or Delete their vehicles
```

#### Phase 2: Registration Submission
```
Student views "My Registrations" → "Create Registration"
    ↓
Student selects vehicle from dropdown (filtered to own vehicles only)
    ↓
Student clicks "Create"
    ↓
System creates Registration record:
  - student_id = auth()->user()->student->id
  - vehicle_id = [selected vehicle]
  - status = 'pending'
  - submitted_at = now()
  - Other fields = null initially
    ↓
Notification: "Registration submitted successfully"
    ↓
Registration appears in "My Registrations" list with:
  - Plate No.
  - Vehicle Type
  - Status badge: "pending" (warning/yellow)
  - Sticker Status: empty (no sticker yet)
  - Submission timestamp
```

#### Phase 3: Approval Process (Student perspective)
```
Student views registration in "My Registrations"
    ↓
Status progression visible:
  - pending → (Guard/Admin verifies via patrol/manual review)
  - verified → (Authority reviews for final approval)
  - approved → Digital sticker issued
  - rejected → Shows rejection reason if applicable
    ↓
(If rejected) Student can click "Request Renewal" to resubmit
    ↓
Registration record created with same vehicle & student, status='pending'
```

#### Phase 4: Sticker Management
```
Once registration status = 'approved' AND digitalSticker exists:
    ↓
Registration row displays:
  - Status badge: "approved" (green)
  - Sticker Expires: [validity_end_date]
  - Sticker Status: "valid" (green)
    ↓
Student can view sticker actions:
  - "View Sticker" button → Opens public display page
  - "Download QR" button → Downloads PNG image, updates downloaded_at
    ↓
Public Sticker Display (route: /sticker/{token})
    ↓
Displays vehicle and sticker info beautifully for showing to guard
```

#### Phase 5: Renewal Workflow
```
As validity_end_date approaches, sticker status changes to 'expired'
    ↓
Registration row shows:
  - Status badge: "approved" (green)
  - Sticker Status: "expired" (warning/yellow)
    ↓
Student clicks "Request Renewal"
    ↓
Confirmation required
    ↓
System creates new Registration:
  - student_id = original student
  - vehicle_id = original vehicle
  - status = 'pending'
  - submitted_at = now()
    ↓
Old registration remains in history (approved status, expired sticker)
New registration starts approval workflow again
    ↓
Notification: "Renewal request submitted successfully"
```

### 6. Data Isolation & Query Filtering
```
All queries automatically filtered:
  - Vehicle list: WHERE student_id = auth()->user()->student->id
  - Registration list: WHERE student_id = auth()->user()->student->id
  
Query modification happens in modifyQueryUsing() callback:
  - Returns empty result set if user has no linked Student record
  - Prevents data leakage across students
```

### 7. Related Models & Resources
- Student (one-to-one with User via hasOne relationship)
- Vehicle (many owned vehicles)
- Registration (many registrations for own vehicles)
- DigitalSticker (view-only, accessed via registration relationship)

### 8. Permissions & Constraints
- **Cannot**: View other students' vehicles or registrations
- **Cannot**: Delete approved registrations
- **Cannot**: Modify registration status manually
- **Cannot**: Access any admin/authority functions
- **Cannot**: See guard or scan history
- **Can**: Create and manage own vehicles
- **Can**: Submit registrations for approval
- **Can**: View own approval progress
- **Can**: Download and share own stickers
- **Can**: Request renewal of expired stickers
- **Data Visibility**: Automatically limited to own student record

---

## System Architecture & Data Flow

### Database Relationships
```
User (1) ──→ (1) Student ──→ (M) Vehicle ──→ (M) Registration ──→ (1) DigitalSticker
 │
 ├─ (M) CheckInLog (guard_id)
 ├─ (M) Registration (verified_by)
 └─ (M) Registration (approved_by)

Vehicle ──→ VehicleType
Registration ──→ CheckInLog (1:M)
DigitalSticker ──→ CheckInLog (1:M)
```

### Registration Status Lifecycle
```
┌─────────────────────────────────────────────────────────────────┐
│ Student Submits                                                  │
│ status='pending', submitted_at=now()                            │
└────────────┬────────────────────────────────────────────────────┘
             │
             ├─────────────────────────────────────────┐
             │ Admin Verifies (Optional Step)          │
             └────────────┬────────────────────────────┘
             │            │
             ▼            ▼
      ┌──────────────────────────────┐
      │ Status = 'verified'          │
      │ verified_by=admin_id         │
      │ verified_at=now()            │
      └────────────┬─────────────────┘
                   │
                   ▼
      ┌──────────────────────────────┐
      │ Authority Reviews            │
      │ (filtered in Authority panel)│
      └────────────┬─────────────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
        ▼                     ▼
   ┌─────────────┐    ┌──────────────┐
   │ Approved    │    │   Rejected   │
   │ + Sticker   │    │  + Reason    │
   │ issued      │    │              │
   └─────────────┘    └──────────────┘
        │
        ▼
   ┌─────────────────────┐
   │ DigitalSticker      │
   │ Valid or Expired    │
   └─────────────────────┘
        │
        ▼
   ┌─────────────────────┐
   │ Renewal Request     │
   │ New Registration    │
   │ status='pending'    │
   └─────────────────────┘
```

### Access Control Flow (Guard Check-In)
```
Guard Scan Input
        │
        ├─ QR Code: Extract token from scan
        │
        └─ Plate Number: User enters manually
             │
             ▼
    ┌─────────────────────────────┐
    │ Search Vehicle/Sticker      │
    │ in Database                 │
    └────────────┬────────────────┘
                 │
        ┌────────┴────────┐
        │                 │
        ▼                 ▼
    Not Found         Found
        │                 │
        ▼                 ▼
    DENY           Check Sticker
    Log             Validity
        │                 │
        │         ┌───────┴───────┐
        │         │               │
        │         ▼               ▼
        │      Valid         Not Valid
        │    (status='valid'  (revoked/
        │    AND between       expired)
        │    validity dates)   │
        │         │            ▼
        │         ▼           DENY
        │       GRANT         + Reason
        │         │
        └─────────┴───────────────┐
                  │               │
                  ▼               ▼
            ┌──────────┐    ┌──────────┐
            │ Grant    │    │  Deny    │
            │ Access   │    │ Access   │
            │ Log      │    │ Log      │
            └──────────┘    └──────────┘
```

---

## Key Services & Utilities

### QRCodeService
- **Method**: `verifyToken($token)` - Validates QR token from guard scan
- **Method**: `generateForRegistration($registration, $startDate, $endDate)` - Generates sticker with QR code
- **Used by**: Guard (verification), Admin (sticker generation), Authority (approval + sticker)

### Model Methods
- **User**:
  - `isAdmin()`, `isGuard()`, `isStudent()`, `isInstituteAuthority()`
  - `canAccessPanel(Panel $panel)` - Determines panel access

- **Registration**:
  - `isPending()`, `isVerified()`, `isApproved()`, `isRejected()`
  - `scopePending()`, `scopeApproved()`

- **DigitalSticker**:
  - `isValid()` - Checks status='valid' AND date range
  - `isExpired()` - Checks status='expired' OR past end date
  - `getStatusLabelAttribute()` - Returns: Valid/Revoked/Expired

- **Student**:
  - `activeRegistration()` - Latest approved registration

- **Vehicle**:
  - `activeRegistration()` - Latest approved registration
  - `latestSticker()` - Current valid sticker via relationship

---

## File Locations Summary

### Filament Panels
- `/app/Providers/Filament/AdminPanelProvider.php`
- `/app/Providers/Filament/GuardPanelProvider.php`
- `/app/Providers/Filament/StudentPanelProvider.php`
- `/app/Providers/Filament/InstituteAuthorityPanelProvider.php`

### Resources by Role
- Admin: `/app/Filament/Admin/Resources/`
- Guard: `/app/Filament/Guard/Resources/`
- Student: `/app/Filament/Student/Resources/`
- Authority: `/app/Filament/Authority/Resources/`

### Models
- `/app/Models/User.php`
- `/app/Models/Student.php`
- `/app/Models/Vehicle.php`
- `/app/Models/VehicleType.php`
- `/app/Models/Registration.php`
- `/app/Models/DigitalSticker.php`
- `/app/Models/CheckInLog.php`

### Migrations
- `/database/migrations/0001_01_01_000000_create_users_table.php`
- `/database/migrations/2026_03_30_070421_create_students_table.php`
- `/database/migrations/2026_03_30_070421_create_vehicles_table.php`
- `/database/migrations/2026_03_30_070421_create_registrations_table.php`
- `/database/migrations/2026_03_30_070422_create_digital_stickers_table.php`
- `/database/migrations/2026_03_30_070422_create_check_in_logs_table.php`

---

## Authentication & Multi-Panel Setup
- Each role has separate login page at `/[panel-id]/login`
- Users can only access their assigned role's panel
- Active status (`is_active=true`) required for all panel access
- Filament's `FilamentUser` contract enforces role-based access via `canAccessPanel()`

