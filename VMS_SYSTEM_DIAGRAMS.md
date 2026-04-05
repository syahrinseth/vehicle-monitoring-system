# Vehicle Monitoring System - Visual Diagrams

## System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────────────┐
│                     Vehicle Monitoring System (Filament)                 │
└─────────────────────────────────────────────────────────────────────────┘

┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│ ADMIN PANEL  │  │ AUTHORITY    │  │ GUARD PANEL  │  │ STUDENT      │
│   (Blue)     │  │ (Purple)     │  │  (Orange)    │  │ (Green)      │
│    /admin    │  │ /authority   │  │   /guard     │  │ /student     │
└──────────────┘  └──────────────┘  └──────────────┘  └──────────────┘
       │                │                   │                │
       └────────────────┴───────────────────┴────────────────┘
                        │
                ┌───────▼────────┐
                │   User Model   │
                │  role='admin'  │
                │  is_active     │
                └────────────────┘
```

## Database Entity Relationship Diagram

```
┌──────────────┐
│   USERS      │
├──────────────┤
│ id (PK)      │
│ name         │
│ email        │
│ phone        │
│ role    ◄────┼─── 'admin'
│ password     │    'institute_authority'
│ is_active    │    'guard'
└──────────────┘    'student'
      │                │
      │ 1:1            │ 1:M
      ▼                ▼
  ┌──────────────┐  ┌──────────────────┐
  │  STUDENTS    │  │  CHECK_IN_LOGS   │
  ├──────────────┤  ├──────────────────┤
  │ id (PK)      │  │ id (PK)          │
  │ user_id (FK) │  │ vehicle_id (FK)  │
  │ matric_no    │  │ digital_sticker_id
  │ ic_number    │  │ guard_id (FK)    │
  │ phone        │  │ scan_method      │
  │ dob          │  │ access_granted   │
  │ gender       │  │ denial_reason    │
  │ address      │  │ scanned_at       │
  └──────────────┘  └──────────────────┘
      │
      │ 1:M
      ▼
  ┌──────────────┐
  │  VEHICLES    │
  ├──────────────┤
  │ id (PK)      │
  │ student_id   │
  │ vehicle_type │
  │ plate_no     │
  │ color        │
  │ manufacturer │
  │ model        │
  │ year         │
  │ engine_no    │
  │ chassis_no   │
  │ doc_path     │
  └──────────────┘
      │
      │ 1:M
      ▼
  ┌──────────────────┐
  │  REGISTRATIONS   │
  ├──────────────────┤
  │ id (PK)          │
  │ student_id (FK)  │
  │ vehicle_id (FK)  │
  │ status ◄─────────┼──── 'pending'
  │ submitted_at     │     'verified'
  │ verified_by (FK) │     'approved'
  │ verified_at      │     'rejected'
  │ approved_by (FK) │
  │ approved_at      │
  │ rejection_reason │
  └──────────────────┘
      │
      │ 1:1
      ▼
  ┌──────────────────┐
  │ DIGITAL_STICKERS │
  ├──────────────────┤
  │ id (PK)          │
  │ registration_id  │
  │ qr_code_token    │
  │ qr_code_image    │
  │ validity_start   │
  │ validity_end     │
  │ status ◄─────────┼──── 'valid'
  │ generated_at     │     'expired'
  │ downloaded_at    │     'revoked'
  └──────────────────┘
```

## Role-Based Panel Access Flow

```
      User Login
         │
         ▼
    ┌─────────────────────┐
    │ Check Role          │
    │ (canAccessPanel)    │
    └────┬────┬────┬──────┘
         │    │    │
         │    │    └──────────────────────┐
         │    │                           │
    ┌────▼──┐ │                    ┌──────▼──┐
    │ADMIN  │ │               ┌────► GUARD   │
    │PANEL  │ │               │    │ PANEL   │
    └───────┘ │               │    └─────────┘
              │         ┌─────┤
         ┌────▼────┐   │     └────────┐
         │AUTHORITY│   │              │
         │PANEL    │   │         ┌────▼──────┐
         └─────────┘   │         │ STUDENT   │
                       │         │ PANEL     │
                       │         └───────────┘
                   ┌───▼────┐
                   │is_active│ = true (required)
                   └────────┘
```

## Registration Approval State Machine

```
START
  │
  ▼
┌─────────────────────────────────────────┐
│ PENDING                                  │
│ (Student submitted registration)         │
│ - submitted_at = now                    │
│ - verified_by = null                    │
│ - approved_by = null                    │
└──────────┬──────────┬────────────────────┘
           │          │
    ┌──────▼──────┐   │
    │   ADMIN     │   │
    │  Verifies   │   │
    │  (optional) │   │
    └──────┬──────┘   │
           │          │
    ┌──────▼──────┐   │
    │  VERIFIED   │   │
    │ verified_by │   │
    │ verified_at │   │
    └──────┬──────┘   │
           │          │
           │          │
     ┌─────▼──────────▼──┐
     │   AUTHORITY       │
     │   Approves        │
     │   or Rejects      │
     └────┬────────┬─────┘
          │        │
    ┌─────▼──┐ ┌──▼──────────┐
    │APPROVED│ │ REJECTED    │
    │Status: │ │ rejected_at │
    │approved│ │ reason=text │
    │ by+date│ └─────────────┘
    └─────┬──┘       │
          │          │
    ┌─────▼──────┐   │
    │ STICKER    │   │
    │ ISSUED     │   │
    │ (QR Code)  │   │
    └────────────┘   │
          │          │
          │    ┌─────▼────────┐
          │    │ Can request  │
          │    │ renewal after│
          │    │ resubmission │
          │    └──────────────┘
          ▼
        END
```

## Guard Access Control Decision Tree

```
Guard scans vehicle (QR or Plate)
        │
        ├─ QR Code Scan ─┐
        │                │
        │ Plate Lookup ──┤
        │                │
        └────────┬───────┘
                 ▼
    ┌──────────────────────┐
    │ Find Vehicle         │
    │ in System            │
    └────┬────────────┬────┘
         │ Found      │ Not Found
    ┌────▼──────┐ ┌──▼────────────┐
    │ Get Latest │ │ ACCESS DENIED │
    │ Sticker    │ │ "Vehicle not  │
    │            │ │ found"        │
    └────┬───────┘ └───────────────┘
         │
    ┌────▼──────────────────┐
    │ Sticker exists?       │
    └────┬──────────────┬───┘
         │ No           │ Yes
    ┌────▼────────┐ ┌──▼────────────┐
    │ ACCESS DENY │ │ Check validity│
    │ "No valid   │ │ status='valid'│
    │  sticker"   │ │ & date range  │
    └─────────────┘ └┬────────┬─────┘
                     │ Valid  │ Expired/Revoked
              ┌──────▼──┐ ┌───▼──────────────┐
              │ GRANTED │ │ ACCESS DENIED    │
              │ ✓✓✓     │ │ [reason: expired]│
              └─────────┘ └──────────────────┘
                 │                │
                 └────────┬────────┘
                          ▼
            ┌─────────────────────────┐
            │ Log CheckInLog          │
            │ - vehicle_id           │
            │ - digital_sticker_id   │
            │ - guard_id             │
            │ - scan_method          │
            │ - access_granted       │
            │ - denial_reason        │
            │ - scanned_at           │
            └─────────────────────────┘
```

## Student Workflow - End to End

```
STUDENT JOURNEY
───────────────

Step 1: Vehicle Registration
┌──────────────────────────────┐
│ Student logs in → Dashboard  │
│         ↓                    │
│  Click "My Vehicles"         │
│         ↓                    │
│  Click "Create Vehicle"      │
│         ↓                    │
│  Fill form:                  │
│  - Type (dropdown)           │
│  - Plate Number              │
│  - Color, Brand, Model, Year │
│  - Engine/Chassis (optional) │
│  - Upload Document           │
│         ↓                    │
│  Save → Vehicle created      │
└──────────────────────────────┘

Step 2: Submit Registration
┌──────────────────────────────┐
│ Student → My Registrations   │
│         ↓                    │
│  Click "Create Registration" │
│         ↓                    │
│  Select Vehicle (dropdown)   │
│         ↓                    │
│  Submit for approval         │
│         ↓                    │
│ Status: PENDING              │
│ Waiting for Admin/Authority  │
└──────────────────────────────┘

Step 3: Approval Process
┌──────────────────────────────┐
│ (Background)                 │
│ Admin may verify details     │
│         ↓                    │
│ Status → VERIFIED (optional) │
│         ↓                    │
│ Authority reviews & approves │
│         ↓                    │
│ Status → APPROVED            │
│ Sticker → ISSUED (QR Code)   │
└──────────────────────────────┘

Step 4: Sticker Usage
┌──────────────────────────────┐
│ Student sees:                │
│ - Status: APPROVED (green)   │
│ - Sticker Status: VALID      │
│ - Valid Until: [date]        │
│         ↓                    │
│ Click "View Sticker"         │
│ Click "Download QR"          │
│         ↓                    │
│ Student can show on phone    │
│ to Guard at gate             │
└──────────────────────────────┘

Step 5: Access at Gate
┌──────────────────────────────┐
│ Guard scans QR code          │
│         ↓                    │
│ System verifies sticker      │
│         ↓                    │
│ ACCESS GRANTED ✓             │
│         ↓                    │
│ CheckInLog recorded          │
│ Student enters campus        │
└──────────────────────────────┘

Step 6: Renewal (After expiry)
┌──────────────────────────────┐
│ Student sees:                │
│ - Sticker Status: EXPIRED    │
│         ↓                    │
│ Click "Request Renewal"      │
│         ↓                    │
│ New Registration created     │
│ Status: PENDING              │
│         ↓                    │
│ Workflow repeats from Step 3 │
└──────────────────────────────┘
```

## Admin Management Dashboard

```
ADMIN PANEL - Main Navigation
┌─────────────────────────────────────────┐
│                                         │
│  User Management                        │
│  ├─ Users (CRUD all roles)             │
│  └─ Students (CRUD + counts)           │
│                                         │
│  Vehicle Management                    │
│  ├─ Vehicles (CRUD all)                │
│  ├─ Registrations (CRUD + Actions)     │
│  │   ├─ Verify (pending→verified)      │
│  │   ├─ Reject (with reason)           │
│  │   └─ Generate Sticker               │
│  └─ Digital Stickers (View + Actions)  │
│      ├─ View details                   │
│      ├─ Revoke (valid→revoked)        │
│      └─ Download QR                    │
│                                         │
│  Reports                               │
│  └─ Check-In Logs (view all)           │
│     ├─ All scans (by all guards)       │
│     ├─ Filter by status                │
│     ├─ Filter by method                │
│     └─ Export capability               │
│                                         │
│  Configuration                         │
│  └─ Vehicle Types (CRUD)               │
│     ├─ Name (e.g., Car, Bike)          │
│     ├─ Code (e.g., CAR, BIKE)          │
│     └─ Active flag                     │
│                                         │
└─────────────────────────────────────────┘
```

## Authority Approval Interface

```
AUTHORITY PANEL - Registrations
┌──────────────────────────────────────────────┐
│                                              │
│  Registrations Awaiting Approval             │
│  ┌──────────────────────────────────────┐   │
│  │ Registration #123                    │   │
│  │ Student: John Doe (A12345)          │   │
│  │ Vehicle: ABC 1234 (Car)             │   │
│  │ Status: VERIFIED (by Admin)         │   │
│  │ Submitted: 2 days ago               │   │
│  │                                      │   │
│  │ Actions:                             │   │
│  │ ┌──────────────────────────────┐   │   │
│  │ │ "Approve & Issue Sticker"    │   │   │
│  │ │ (Opens modal for dates)      │   │   │
│  │ │ - Valid From: [Today]        │   │   │
│  │ │ - Valid Until: [+1 Year]     │   │   │
│  │ └──────────────────────────────┘   │   │
│  │ ┌──────────────────────────────┐   │   │
│  │ │ "Reject"                     │   │   │
│  │ │ (Opens modal for reason)     │   │   │
│  │ │ - Rejection Reason: [text]   │   │   │
│  │ └──────────────────────────────┘   │   │
│  │ ┌──────────────────────────────┐   │   │
│  │ │ "View Details"               │   │   │
│  │ └──────────────────────────────┘   │   │
│  └──────────────────────────────────────┘   │
│                                              │
└──────────────────────────────────────────────┘
```

## Guard Scanning Interface

```
GUARD PANEL - Scan/Lookup
┌──────────────────────────────────────────┐
│                                          │
│  Scan Vehicle                            │
│                                          │
│  Tab 1: Scan QR Code                     │
│  ┌────────────────────────────────────┐  │
│  │ [QR Token Input Field]             │  │
│  │ (Auto-focus on load)               │  │
│  │ Placeholder: "Scan QR code here"   │  │
│  │ ┌──────────────┐                   │  │
│  │ │ Scan QR Code │ [Button]          │  │
│  │ └──────────────┘                   │  │
│  └────────────────────────────────────┘  │
│                                          │
│  Tab 2: Lookup by Plate                  │
│  ┌────────────────────────────────────┐  │
│  │ [Plate Number Input]               │  │
│  │ Placeholder: "e.g. ABC 1234"       │  │
│  │ ┌──────────────┐                   │  │
│  │ │ Search Plate │ [Button]          │  │
│  │ └──────────────┘                   │  │
│  └────────────────────────────────────┘  │
│                                          │
│  Result Display (if scan performed):     │
│  ┌────────────────────────────────────┐  │
│  │                                    │  │
│  │ Vehicle: ABC 1234                  │  │
│  │ Type: Car                          │  │
│  │ Color: Blue, Brand: Toyota         │  │
│  │                                    │  │
│  │ Student: John Doe                  │  │
│  │ Matric: A12345                     │  │
│  │                                    │  │
│  │ Sticker Status: VALID              │  │
│  │ Valid Until: 15 Jan 2025           │  │
│  │                                    │  │
│  │ ╔════════════════════════════════╗ │  │
│  │ ║ ✓ ACCESS GRANTED              ║ │  │
│  │ ║ (Green box, success icon)       ║ │  │
│  │ ╚════════════════════════════════╝ │  │
│  │                                    │  │
│  │ ┌──────────────┐                   │  │
│  │ │ Clear Result │ [Button]          │  │
│  │ └──────────────┘                   │  │
│  └────────────────────────────────────┘  │
│                                          │
│  OR (if denied):                         │
│  ┌────────────────────────────────────┐  │
│  │                                    │  │
│  │ ╔════════════════════════════════╗ │  │
│  │ ║ ✗ ACCESS DENIED                ║ │  │
│  │ ║ Reason: Sticker Expired        ║ │  │
│  │ ║ (Red box, error icon)           ║ │  │
│  │ ╚════════════════════════════════╝ │  │
│  │                                    │  │
│  └────────────────────────────────────┘  │
│                                          │
└──────────────────────────────────────────┘
```

## Data Flow: QR Code Scan to Access Decision

```
Guard Scans QR Code
        │
        ▼
┌─────────────────────────────────────────┐
│ Extract Token from QR                   │
│ (e.g., a47f8c3b-2d9e-11eb-adc1-0242ac)│
└──────────────┬────────────────────────────┘
               │
               ▼
      ┌────────────────────────────┐
      │ Query Database:            │
      │ DigitalSticker             │
      │ where qr_code_token = [..] │
      └──────────┬──────────────────┘
                 │
         ┌───────┴───────┐
         │               │
         ▼               ▼
    ┌────────┐      ┌──────────┐
    │ Found  │      │ Not Found │
    └────┬───┘      └────┬──────┘
         │                │
         ▼                ▼
   ┌──────────────┐   ┌──────────────────┐
   │ Check Status │   │ LOG DENIED       │
   │ = 'valid'?   │   │ Reason: Not found│
   └──┬────────┬──┘   └──────────────────┘
      │ Yes    │ No
  ┌───▼─┐  ┌───▼────────────────────┐
  │YES  │  │ LOG DENIED             │
  └─┬──┘  │ Reason: Revoked/Expired│
    │     └────────────────────────┘
    ▼
  ┌────────────────────────────────┐
  │ Check Date Range:              │
  │ today between                  │
  │ validity_start_date AND        │
  │ validity_end_date?             │
  └────┬─────────────────────────┬──┘
       │ Yes (within range)       │ No (expired)
   ┌───▼──┐                   ┌───▼────────────┐
   │ ALLOW│                   │ LOG DENIED     │
   │ ✓    │                   │ Reason: Expired│
   └───┬──┘                   └────────────────┘
       │
       ▼
  ┌──────────────────────────────┐
  │ LOG ALLOWED (CheckInLog)     │
  │ access_granted = true        │
  │ denial_reason = null         │
  │ scanned_at = now()           │
  └──────────────────────────────┘
       │
       ▼
  ┌──────────────────────────────┐
  │ Display to Guard:            │
  │ "✓ ACCESS GRANTED"          │
  │ [Green notification]         │
  └──────────────────────────────┘
```

## Data Access Control - Role Isolation

```
User Database View Based on Role
═════════════════════════════════════════════════════════════

┌──────────────────────────────────────┐
│ ADMIN Views                          │
│ ┌────────────────────────────────┐  │
│ │ All Users (all roles)          │  │
│ │ All Students (complete data)   │  │
│ │ All Vehicles (all students)    │  │
│ │ All Registrations (all status) │  │
│ │ All Digital Stickers (all)     │  │
│ │ All Check-In Logs (all guards) │  │
│ └────────────────────────────────┘  │
│ No query filtering                  │
└──────────────────────────────────────┘

┌──────────────────────────────────────┐
│ AUTHORITY Views                      │
│ ┌────────────────────────────────┐  │
│ │ Registrations WHERE            │  │
│ │ status IN [verified,approved,  │  │
│ │           rejected]            │  │
│ │                                │  │
│ │ NO access to:                  │  │
│ │ - Users                        │  │
│ │ - Students                     │  │
│ │ - Vehicles                     │  │
│ │ - Check-In Logs                │  │
│ └────────────────────────────────┘  │
│ Filtered: status filtering          │
└──────────────────────────────────────┘

┌──────────────────────────────────────┐
│ GUARD Views                          │
│ ┌────────────────────────────────┐  │
│ │ Check-In Logs WHERE            │  │
│ │ guard_id = auth()->id()        │  │
│ │                                │  │
│ │ NO access to:                  │  │
│ │ - Registrations                │  │
│ │ - Student profiles             │  │
│ │ - User management              │  │
│ │ - Vehicle inventory            │  │
│ │ - Sticker details              │  │
│ └────────────────────────────────┘  │
│ Filtered: only own guard_id         │
└──────────────────────────────────────┘

┌──────────────────────────────────────┐
│ STUDENT Views                        │
│ ┌────────────────────────────────┐  │
│ │ Vehicles WHERE                 │  │
│ │ student_id = auth()->user()    │  │
│ │            ->student->id()     │  │
│ │                                │  │
│ │ Registrations WHERE            │  │
│ │ student_id = [same]            │  │
│ │                                │  │
│ │ NO access to:                  │  │
│ │ - Other students' data         │  │
│ │ - User management              │  │
│ │ - Registrations                │  │
│ │ - Check-In Logs                │  │
│ └────────────────────────────────┘  │
│ Filtered: only own student_id       │
└──────────────────────────────────────┘
```

