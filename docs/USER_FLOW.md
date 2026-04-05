# Vehicle Monitoring System — User Flow
# *Sistem Pemantauan Kenderaan — Aliran Pengguna*

**Version / Versi:** 1.0
**Date / Tarikh:** April 2026
**Prepared for / Disediakan untuk:** Client Reference / Rujukan Klien

---

## Table of Contents / *Jadual Kandungan*

1. [System Overview](#1-system-overview)
2. [End-to-End Workflow Summary](#2-end-to-end-workflow-summary)
3. [Role 1 — Student / Pelajar](#3-role-1--student--pelajar)
4. [Role 2 — Admin / Pentadbir](#4-role-2--admin--pentadbir)
5. [Role 3 — Institute Authority / Pihak Berkuasa Institut](#5-role-3--institute-authority--pihak-berkuasa-institut)
6. [Role 4 — Guard / Pengawal Keselamatan](#6-role-4--guard--pengawal-keselamatan)
7. [Registration Status Lifecycle](#7-registration-status-lifecycle)
8. [Access URLs](#8-access-urls)

---

## 1. System Overview
### *Gambaran Keseluruhan Sistem*

The Vehicle Monitoring System (VMS) is a digital platform that manages student vehicle registration and campus access control. Students register their vehicles online, go through a two-step approval process by staff, and receive a digital QR sticker to enter campus. Guards at the gate scan or look up the sticker to grant or deny access.

*Sistem Pemantauan Kenderaan (VMS) ialah platform digital yang menguruskan pendaftaran kenderaan pelajar dan kawalan akses kampus. Pelajar mendaftarkan kenderaan mereka secara dalam talian, melalui proses kelulusan dua peringkat oleh kakitangan, dan menerima pelekat QR digital untuk memasuki kampus. Pengawal di pintu masuk mengimbas atau menyemak pelekat tersebut untuk membenarkan atau menolak akses.*

---

### Roles & Responsibilities / *Peranan & Tanggungjawab*

| Role / *Peranan* | Responsibility / *Tanggungjawab* | Panel |
|------------------|----------------------------------|-------|
| **Student** / *Pelajar* | Register vehicles, track approval status, use digital sticker | `/student` |
| **Admin** / *Pentadbir* | Verify registrations, manage users & data, oversee the system | `/admin` |
| **Institute Authority** / *Pihak Berkuasa Institut* | Give final approval, issue digital stickers | `/authority` |
| **Guard** / *Pengawal Keselamatan* | Scan QR codes or look up plate numbers at the gate | `/guard` |

---

## 2. End-to-End Workflow Summary
### *Ringkasan Aliran Kerja Penuh*

The following is the complete chain of events from vehicle registration to campus entry.

*Berikut adalah rantaian peristiwa lengkap dari pendaftaran kenderaan hingga kemasukan ke kampus.*

1. **Student** creates an account and logs in.
   *Pelajar membuat akaun dan log masuk.*

2. **Student** adds their vehicle details to the system.
   *Pelajar menambah maklumat kenderaan mereka ke dalam sistem.*

3. **Student** submits a registration request — status becomes **Pending**.
   *Pelajar menghantar permohonan pendaftaran — status menjadi **Menunggu**.*

4. **Admin** reviews the submission and verifies it — status becomes **Verified**.
   *Pentadbir menyemak dan mengesahkan permohonan — status menjadi **Disahkan**.*

5. **Institute Authority** reviews the verified registration and approves it — status becomes **Approved**, and a digital QR sticker is automatically issued.
   *Pihak Berkuasa Institut menyemak pendaftaran yang telah disahkan dan meluluskannya — status menjadi **Diluluskan**, dan pelekat QR digital dikeluarkan secara automatik.*

6. **Student** views and downloads the QR sticker from their portal.
   *Pelajar melihat dan memuat turun pelekat QR dari portal mereka.*

7. **Guard** scans the student's QR code or searches by plate number at the gate — access is **Granted** or **Denied** based on sticker validity.
   *Pengawal mengimbas kod QR pelajar atau mencari menggunakan nombor plat di pintu masuk — akses **Dibenarkan** atau **Ditolak** berdasarkan kesahihan pelekat.*

---

## 3. Role 1 — Student / *Pelajar*

### Purpose / *Tujuan*

Students use the portal to register their vehicles and obtain a digital sticker that allows them to enter campus.

*Pelajar menggunakan portal untuk mendaftarkan kenderaan mereka dan mendapatkan pelekat digital yang membolehkan mereka memasuki kampus.*

---

### 3.1 Create Account / *Buat Akaun*

1. Go to the Student Portal at `/student/register`.
   *Pergi ke Portal Pelajar di `/student/register`.*
2. Fill in your name, email address, and password.
   *Isi nama, alamat e-mel, dan kata laluan anda.*
3. Click **Register**.
   *Klik **Daftar**.*
4. You will be redirected to your dashboard automatically.
   *Anda akan diarahkan ke papan pemuka secara automatik.*

---

### 3.2 Add a Vehicle / *Tambah Kenderaan*

1. From the navigation menu, click **My Vehicles**.
   *Dari menu navigasi, klik **Kenderaan Saya**.*
2. Click **New Vehicle**.
   *Klik **Kenderaan Baharu**.*
3. Fill in the vehicle details:
   *Isi maklumat kenderaan:*
   - Vehicle Type / *Jenis Kenderaan* (e.g. Car, Motorcycle)
   - Plate Number / *Nombor Plat* (e.g. `ABC 1234`)
   - Color / *Warna*
   - Brand / *Jenama*
   - Model
   - Year / *Tahun*
   - Upload registration document (Grant/Geran) — *optional / pilihan*
4. Click **Create**.
   *Klik **Cipta**.*
5. The plate number will be saved in uppercase automatically.
   *Nombor plat akan disimpan dalam huruf besar secara automatik.*

> **Note / Nota:** You can add more than one vehicle to your account.
> *Anda boleh menambah lebih daripada satu kenderaan ke akaun anda.*

---

### 3.3 Submit a Registration Request / *Hantar Permohonan Pendaftaran*

1. From the navigation menu, click **My Registration**.
   *Dari menu navigasi, klik **Pendaftaran Saya**.*
2. Click **New Registration**.
   *Klik **Pendaftaran Baharu**.*
3. Select the vehicle you wish to register from the dropdown.
   *Pilih kenderaan yang ingin anda daftarkan dari senarai juntai bawah.*
4. Click **Create**.
   *Klik **Cipta**.*
5. Your registration will appear in the list with status **Pending**.
   *Pendaftaran anda akan muncul dalam senarai dengan status **Menunggu**.*

---

### 3.4 Track Registration Status / *Pantau Status Pendaftaran*

1. Go to **My Registration** to view your registration list.
   *Pergi ke **Pendaftaran Saya** untuk melihat senarai pendaftaran anda.*
2. Check the **Status** badge on your registration row.
   *Semak lencana **Status** pada baris pendaftaran anda.*

| Status | Meaning / *Maksud* |
|--------|---------------------|
| 🟡 **Pending** / *Menunggu* | Submitted, waiting for admin to review / *Dihantar, menunggu semakan pentadbir* |
| 🔵 **Verified** / *Disahkan* | Admin verified, waiting for authority approval / *Disahkan pentadbir, menunggu kelulusan pihak berkuasa* |
| 🟢 **Approved** / *Diluluskan* | Approved, sticker has been issued / *Diluluskan, pelekat telah dikeluarkan* |
| 🔴 **Rejected** / *Ditolak* | Registration was rejected / *Pendaftaran telah ditolak* |

---

### 3.5 View & Download Digital Sticker / *Lihat & Muat Turun Pelekat Digital*

> Pre-condition: Registration status must be **Approved** with a **Valid** sticker.
> *Prasyarat: Status pendaftaran mesti **Diluluskan** dengan pelekat **Sah**.*

1. Go to **My Registration**.
   *Pergi ke **Pendaftaran Saya**.*
2. Click **View Sticker** to open your digital sticker in a new tab.
   *Klik **Lihat Pelekat** untuk membuka pelekat digital anda dalam tab baharu.*
3. The sticker page shows a **green "LULUS"** banner if your sticker is valid.
   *Halaman pelekat menunjukkan sepanduk **hijau "LULUS"** jika pelekat anda sah.*
4. Click **Download QR** to download the QR code image.
   *Klik **Muat Turun QR** untuk memuat turun imej kod QR.*
5. Present this QR code to the guard when entering campus.
   *Tunjukkan kod QR ini kepada pengawal semasa memasuki kampus.*

> **Important / Penting:** The sticker is valid for **1 year** from the date of approval. Keep track of the expiry date shown on your sticker.
> *Pelekat sah selama **1 tahun** dari tarikh kelulusan. Pantau tarikh tamat tempoh yang ditunjukkan pada pelekat anda.*

---

### 3.6 Request Sticker Renewal / *Mohon Pembaharuan Pelekat*

> Pre-condition: Your sticker status must be **Expired**.
> *Prasyarat: Status pelekat anda mesti **Tamat Tempoh**.*

1. Go to **My Registration**.
   *Pergi ke **Pendaftaran Saya**.*
2. Click **Request Renewal** on the expired registration row.
   *Klik **Mohon Pembaharuan** pada baris pendaftaran yang tamat tempoh.*
3. Confirm the action in the dialog that appears.
   *Sahkan tindakan dalam dialog yang muncul.*
4. A new registration request is automatically created with status **Pending**.
   *Permohonan pendaftaran baharu dicipta secara automatik dengan status **Menunggu**.*
5. The approval process repeats from Step 3.3.
   *Proses kelulusan berulang dari Langkah 3.3.*

---

## 4. Role 2 — Admin / *Pentadbir*

### Purpose / *Tujuan*

The Admin manages the overall system — verifying registrations, managing user accounts, vehicle data, and monitoring all activity.

*Pentadbir menguruskan keseluruhan sistem — mengesahkan pendaftaran, menguruskan akaun pengguna, data kenderaan, dan memantau semua aktiviti.*

---

### 4.1 Log In / *Log Masuk*

1. Go to `/admin/login`.
2. Enter your email and password.
   *Masukkan e-mel dan kata laluan anda.*
3. Click **Sign in**.
   *Klik **Log Masuk**.*
4. You will see the Admin Dashboard with system statistics.
   *Anda akan melihat Papan Pemuka Pentadbir dengan statistik sistem.*

---

### 4.2 Verify a Pending Registration / *Sahkan Pendaftaran Menunggu*

1. From the navigation menu, go to **Registrations**.
   *Dari menu navigasi, pergi ke **Pendaftaran**.*
2. Filter by **Status = Pending** to find new submissions.
   *Tapis mengikut **Status = Menunggu** untuk mencari permohonan baharu.*
3. Click **View** on a registration to review the student and vehicle details.
   *Klik **Lihat** pada pendaftaran untuk menyemak maklumat pelajar dan kenderaan.*
4. If everything is in order, click **Verify** and confirm.
   *Jika semua maklumat betul, klik **Sahkan** dan konfirmkan.*
5. The status changes to **Verified** and is forwarded to the Institute Authority.
   *Status berubah kepada **Disahkan** dan dikemukakan kepada Pihak Berkuasa Institut.*

---

### 4.3 Reject a Registration / *Tolak Pendaftaran*

1. Click **Reject** on a `Pending` or `Verified` registration.
   *Klik **Tolak** pada pendaftaran yang berstatus `Menunggu` atau `Disahkan`.*
2. Enter a clear reason for rejection in the form.
   *Masukkan sebab penolakan yang jelas dalam borang.*
3. Click **Reject** to confirm.
   *Klik **Tolak** untuk mengesahkan.*
4. The registration status changes to **Rejected**.
   *Status pendaftaran berubah kepada **Ditolak**.*

---

### 4.4 Manage Users / *Urus Pengguna*

1. Go to **Users** in the navigation menu.
   *Pergi ke **Pengguna** dalam menu navigasi.*
2. Click **New User** to create an account for admin, authority, or guard staff.
   *Klik **Pengguna Baharu** untuk membuat akaun bagi kakitangan pentadbir, pihak berkuasa, atau pengawal.*
3. Fill in the name, email, role, and password.
   *Isi nama, e-mel, peranan, dan kata laluan.*
4. Toggle **Active** off to deactivate a user without deleting them.
   *Togol **Aktif** kepada mati untuk menyahaktifkan pengguna tanpa memadamnya.*

---

### 4.5 Manage Students / *Urus Pelajar*

1. Go to **Students** in the navigation menu.
   *Pergi ke **Pelajar** dalam menu navigasi.*
2. View, create, edit, or delete student profiles.
   *Lihat, cipta, edit, atau padam profil pelajar.*
3. Each student profile is linked to a user account with the `student` role.
   *Setiap profil pelajar dikaitkan dengan akaun pengguna yang mempunyai peranan `pelajar`.*

---

### 4.6 Manage Vehicle Types / *Urus Jenis Kenderaan*

1. Go to **Vehicle Types** under the Configuration group.
   *Pergi ke **Jenis Kenderaan** di bawah kumpulan Konfigurasi.*
2. Add or edit vehicle types (e.g. Car, Motorcycle, Van, Truck).
   *Tambah atau edit jenis kenderaan (cth. Kereta, Motosikal, Van, Lori).*
3. Set a type as **Inactive** to prevent students from selecting it in new registrations.
   *Tetapkan jenis sebagai **Tidak Aktif** untuk menghalang pelajar memilihnya dalam pendaftaran baharu.*

---

### 4.7 Manage Digital Stickers / *Urus Pelekat Digital*

1. Go to **Digital Stickers** in the navigation menu.
   *Pergi ke **Pelekat Digital** dalam menu navigasi.*
2. View all issued stickers with validity dates and statuses.
   *Lihat semua pelekat yang dikeluarkan beserta tarikh kesahihan dan status.*
3. Click **Revoke** to immediately cancel a valid sticker.
   *Klik **Batalkan** untuk segera membatalkan pelekat yang sah.*
4. Click **Download QR** to download a copy of the QR image.
   *Klik **Muat Turun QR** untuk memuat turun salinan imej QR.*

---

### 4.8 View Check-In Logs / *Lihat Log Daftar Masuk*

1. Go to **Check-In Logs** under the Reports group.
   *Pergi ke **Log Daftar Masuk** di bawah kumpulan Laporan.*
2. View all scans performed by all guards across the system.
   *Lihat semua imbasan yang dilakukan oleh semua pengawal dalam sistem.*
3. Filter by **Access** (Granted / Denied) or **Scan Method** (QR / Plate Number).
   *Tapis mengikut **Akses** (Dibenarkan / Ditolak) atau **Kaedah Imbasan** (QR / Nombor Plat).*

---

## 5. Role 3 — Institute Authority / *Pihak Berkuasa Institut*

### Purpose / *Tujuan*

The Institute Authority is the final approver. They review registrations that have already been verified by the Admin and decide whether to approve or reject them. Approval automatically issues a digital sticker to the student.

*Pihak Berkuasa Institut adalah pelulus akhir. Mereka menyemak pendaftaran yang telah disahkan oleh Pentadbir dan memutuskan sama ada untuk meluluskan atau menolaknya. Kelulusan secara automatik mengeluarkan pelekat digital kepada pelajar.*

---

### 5.1 Log In / *Log Masuk*

1. Go to `/authority/login`.
2. Enter your email and password.
   *Masukkan e-mel dan kata laluan anda.*
3. Click **Sign in**.
   *Klik **Log Masuk**.*
4. The dashboard shows the number of registrations awaiting your approval.
   *Papan pemuka menunjukkan bilangan pendaftaran yang menunggu kelulusan anda.*

---

### 5.2 Review a Registration / *Semak Pendaftaran*

1. From the navigation menu, go to **Registrations**.
   *Dari menu navigasi, pergi ke **Pendaftaran**.*
2. Only registrations with status **Verified** are shown for approval.
   *Hanya pendaftaran berstatus **Disahkan** ditunjukkan untuk kelulusan.*
3. Click **View** to review the full details of the student and vehicle.
   *Klik **Lihat** untuk menyemak maklumat lengkap pelajar dan kenderaan.*

---

### 5.3 Approve & Issue Digital Sticker / *Luluskan & Keluarkan Pelekat Digital*

1. On the registration row, click **Approve & Issue Sticker**.
   *Pada baris pendaftaran, klik **Luluskan & Keluarkan Pelekat**.*
2. A form appears — set the sticker validity dates (default: today to today + 1 year).
   *Borang muncul — tetapkan tarikh kesahihan pelekat (lalai: hari ini hingga hari ini + 1 tahun).*
3. Click **Approve & Issue Sticker** and confirm.
   *Klik **Luluskan & Keluarkan Pelekat** dan sahkan.*
4. The system automatically:
   *Sistem secara automatik:*
   - Changes registration status to **Approved** / *Menukar status pendaftaran kepada **Diluluskan***
   - Generates a unique QR code for the student / *Menjana kod QR unik untuk pelajar*
   - Makes the sticker available in the student's portal / *Menjadikan pelekat tersedia dalam portal pelajar*
5. The student can now view and download their sticker.
   *Pelajar kini boleh melihat dan memuat turun pelekat mereka.*

---

### 5.4 Reject a Registration / *Tolak Pendaftaran*

1. Click **Reject** on a `Verified` registration row.
   *Klik **Tolak** pada baris pendaftaran berstatus `Disahkan`.*
2. Enter a reason for rejection.
   *Masukkan sebab penolakan.*
3. Click **Reject** to confirm.
   *Klik **Tolak** untuk mengesahkan.*
4. The status changes to **Rejected**.
   *Status berubah kepada **Ditolak**.*

> **Note / Nota:** The Authority can only see **Verified** registrations — registrations that are still **Pending** are not visible until the Admin verifies them first.
> *Pihak Berkuasa hanya boleh melihat pendaftaran yang **Disahkan** — pendaftaran yang masih **Menunggu** tidak kelihatan sehingga Pentadbir mengesahkannya terlebih dahulu.*

---

## 6. Role 4 — Guard / *Pengawal Keselamatan*

### Purpose / *Tujuan*

Guards control vehicle access at the campus gate. They verify a vehicle's entry eligibility by scanning its QR sticker or looking up its plate number. The system instantly shows whether access should be granted or denied.

*Pengawal mengawal akses kenderaan di pintu masuk kampus. Mereka mengesahkan kelayakan masuk kenderaan dengan mengimbas pelekat QR atau mencari nombor platnya. Sistem serta-merta menunjukkan sama ada akses harus dibenarkan atau ditolak.*

---

### 6.1 Log In / *Log Masuk*

1. Go to `/guard/login`.
2. Enter your email and password.
   *Masukkan e-mel dan kata laluan anda.*
3. Click **Sign in**.
   *Klik **Log Masuk**.*

---

### 6.2 Scan a Vehicle by QR Code / *Imbas Kenderaan dengan Kod QR*

1. From the navigation, go to **Scan / Lookup**.
   *Dari navigasi, pergi ke **Imbas / Carian**.*
2. In the **QR Code Token** field, scan or type the QR code value from the student's sticker.
   *Dalam medan **Token Kod QR**, imbas atau taip nilai kod QR dari pelekat pelajar.*
3. Click **Verify QR Code**.
   *Klik **Sahkan Kod QR**.*
4. The system immediately shows one of the following results:
   *Sistem segera menunjukkan salah satu daripada keputusan berikut:*

| Result / *Keputusan* | Banner | Meaning / *Maksud* |
|----------------------|--------|---------------------|
| ✅ Access Granted / *Akses Dibenarkan* | 🟢 Green / *Hijau* | Sticker is valid, allow entry / *Pelekat sah, benarkan masuk* |
| ❌ Access Denied / *Akses Ditolak* | 🔴 Red / *Merah* | Sticker expired, revoked, or not found / *Pelekat tamat tempoh, dibatalkan, atau tidak dijumpai* |

5. If access is denied, the reason is displayed on screen (e.g. "Sticker expired", "Sticker revoked").
   *Jika akses ditolak, sebab ditunjukkan pada skrin (cth. "Pelekat tamat tempoh", "Pelekat dibatalkan").*
6. Every scan is automatically logged with timestamp, guard name, and access result.
   *Setiap imbasan dilog secara automatik dengan cap masa, nama pengawal, dan keputusan akses.*

---

### 6.3 Search a Vehicle by Plate Number / *Cari Kenderaan dengan Nombor Plat*

> Use this method when the student does not have their QR code available.
> *Gunakan kaedah ini apabila pelajar tidak mempunyai kod QR mereka.*

1. In the **Vehicle Plate Number** field, type the vehicle's plate number (e.g. `ABC 1234`).
   *Dalam medan **Nombor Plat Kenderaan**, taip nombor plat kenderaan (cth. `ABC 1234`).*
2. Click **Search Vehicle**.
   *Klik **Cari Kenderaan**.*
3. The system looks up the vehicle and checks its sticker status.
   *Sistem mencari kenderaan dan menyemak status pelekatnya.*
4. The result shows the same **Access Granted** or **Access Denied** banner as the QR scan.
   *Keputusan menunjukkan sepanduk **Akses Dibenarkan** atau **Akses Ditolak** yang sama seperti imbasan QR.*
5. Vehicle details (plate, type, student name, matric number, sticker validity) are shown for confirmation.
   *Maklumat kenderaan (plat, jenis, nama pelajar, nombor matrik, kesahihan pelekat) ditunjukkan untuk pengesahan.*

---

### 6.4 Clear the Result / *Kosongkan Keputusan*

1. After completing a check, click **Clear** to reset the page for the next vehicle.
   *Selepas menyelesaikan semakan, klik **Kosong** untuk menetapkan semula halaman bagi kenderaan seterusnya.*

---

### 6.5 View Scan History / *Lihat Sejarah Imbasan*

1. From the navigation, go to **Scan History**.
   *Dari navigasi, pergi ke **Sejarah Imbasan**.*
2. View a log of all scans you have performed, sorted by most recent.
   *Lihat log semua imbasan yang telah anda lakukan, disusun mengikut yang terbaharu.*
3. Each record shows: Plate Number, Student, Scan Method, Access Result, Reason (if denied), and Time.
   *Setiap rekod menunjukkan: Nombor Plat, Pelajar, Kaedah Imbasan, Keputusan Akses, Sebab (jika ditolak), dan Masa.*

---

## 7. Registration Status Lifecycle
### *Kitaran Hayat Status Pendaftaran*

The following shows how a registration moves through the system from submission to expiry.

*Berikut menunjukkan bagaimana pendaftaran bergerak melalui sistem dari penghantaran hingga tamat tempoh.*

```
[Student Submits / Pelajar Menghantar]
            │
            ▼
        🟡 PENDING
        (Menunggu)
            │
     ┌──────┴──────┐
     │             │
     ▼             ▼
🔵 VERIFIED     🔴 REJECTED
(Disahkan)      (Ditolak)
  by Admin         by Admin
     │
     │
  ┌──┴──────┐
  │         │
  ▼         ▼
🟢 APPROVED  🔴 REJECTED
(Diluluskan)  (Ditolak)
by Authority   by Authority
  │
  ▼
[Sticker Issued / Pelekat Dikeluarkan]
  │
  ▼
🟢 VALID (Sah)
  │
  ├──► Expires on validity_end_date
  │         │
  │         ▼
  │    🟡 EXPIRED (Tamat Tempoh)
  │         │
  │         ▼
  │    [Student requests renewal]
  │    [Pelajar mohon pembaharuan]
  │         │
  │         └──► Back to PENDING
  │
  └──► Admin revokes sticker
            │
            ▼
       🔴 REVOKED (Dibatalkan)
```

### Sticker Status Summary / *Ringkasan Status Pelekat*

| Status | Guard Result / *Keputusan Pengawal* | Student Action / *Tindakan Pelajar* |
|--------|--------------------------------------|--------------------------------------|
| 🟢 **Valid** / *Sah* | ACCESS GRANTED / *Akses Dibenarkan* | View & download QR |
| 🟡 **Expired** / *Tamat Tempoh* | ACCESS DENIED / *Akses Ditolak* | Request Renewal / *Mohon Pembaharuan* |
| 🔴 **Revoked** / *Dibatalkan* | ACCESS DENIED / *Akses Ditolak* | Contact Admin / *Hubungi Pentadbir* |

---

## 8. Access URLs
### *URL Akses*

| Role / *Peranan* | Login URL | Dashboard URL |
|------------------|-----------|---------------|
| Student / *Pelajar* | `/student/login` | `/student` |
| Admin / *Pentadbir* | `/admin/login` | `/admin` |
| Institute Authority / *Pihak Berkuasa Institut* | `/authority/login` | `/authority` |
| Guard / *Pengawal Keselamatan* | `/guard/login` | `/guard` |

> Each role can only access their own panel. Logging into another role's panel will be denied.
> *Setiap peranan hanya boleh mengakses panel mereka sendiri. Log masuk ke panel peranan lain akan ditolak.*

---

*End of Document / Akhir Dokumen*
