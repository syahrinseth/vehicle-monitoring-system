# Ringkasan Teknikal Projek

Dokumen ini menerangkan secara ringkas seni bina dan reka bentuk perisian untuk **Vehicle Monitoring System (VMS)**. Ia ditulis untuk pembaca baru, termasuk pembangun yang belum biasa dengan Laravel.

## 1. Gambaran Projek

Vehicle Monitoring System ialah sistem web untuk mengurus pendaftaran kenderaan pelajar dan kawalan masuk ke kawasan institut. Pelajar mendaftar kenderaan, pihak pentadbiran menyemak permohonan, pihak berkuasa institut meluluskan permohonan, dan pengawal keselamatan mengimbas pelekat QR untuk menentukan sama ada kenderaan dibenarkan masuk.

Sistem ini dibina sebagai aplikasi web berasaskan Laravel. Antara fungsi utama ialah:

- Portal pelajar untuk daftar kenderaan dan lihat pelekat digital.
- Panel admin untuk urus pengguna, pelajar, kenderaan, jenis kenderaan, pendaftaran, sticker, dan log masuk.
- Panel pihak berkuasa institut untuk kelulusan akhir pendaftaran.
- Panel pengawal untuk imbas QR atau cari kenderaan melalui nombor plat.
- Penjanaan QR code untuk pelekat digital kenderaan.
- Rekod log setiap imbasan atau semakan kenderaan.

## 2. Teknologi Utama

| Komponen | Teknologi | Fungsi |
| --- | --- | --- |
| Backend | Laravel | Mengurus route, model, database, login, dan logik sistem |
| Admin UI | Filament | Membina panel dashboard dan borang CRUD dengan cepat |
| Database | Laravel Migrations | Menentukan struktur jadual database |
| Frontend assets | Vite, Tailwind CSS | Membina dan memuatkan CSS/JavaScript |
| QR Code | `chillerlan/php-qrcode` | Menjana imej QR untuk sticker digital |
| Testing | PHPUnit | Menjalankan ujian aplikasi |

## 3. Cara Fikir Seni Bina

Sistem ini menggunakan corak biasa Laravel:

1. **Route** menerima permintaan daripada browser.
2. **Panel Filament** menyediakan paparan dashboard, form, table, dan action.
3. **Model** mewakili jadual database dan hubungan antara data.
4. **Service** menyimpan logik khas yang tidak sesuai diletakkan terus dalam UI.
5. **Migration** menentukan struktur jadual database.
6. **View Blade** memaparkan halaman khas seperti sticker digital.

Secara mudah, Laravel bertindak sebagai "otak" sistem, Filament bertindak sebagai "antara muka pengurusan", dan database menyimpan semua rekod.

## 4. Struktur Folder Penting

| Folder atau Fail | Tujuan |
| --- | --- |
| `app/Models` | Model data seperti `User`, `Student`, `Vehicle`, `Registration`, dan `DigitalSticker` |
| `app/Filament` | Panel dan resource Filament untuk admin, pelajar, pengawal, dan pihak berkuasa |
| `app/Services` | Logik servis khas, contohnya `QRCodeService` |
| `app/Console/Commands` | Command terminal khas seperti jana semula QR code |
| `database/migrations` | Struktur jadual database |
| `database/seeders` | Data contoh untuk akaun ujian dan jenis kenderaan |
| `routes/web.php` | Route web awam seperti halaman utama dan paparan sticker |
| `resources/views` | Template Blade untuk paparan HTML |
| `resources/css` dan `resources/js` | Asset frontend yang dibina oleh Vite |
| `docs` | Dokumentasi projek |

## 5. Panel Pengguna

Sistem ini mempunyai empat panel utama. Setiap panel mempunyai URL dan peranan yang berbeza.

| Panel | URL | Pengguna |
| --- | --- | --- |
| Student Portal | `/student` | Pelajar |
| Admin Panel | `/admin` | Pentadbir sistem |
| Authority Panel | `/authority` | Pihak berkuasa institut |
| Guard Station | `/guard` | Pengawal keselamatan |

Akses ke panel dikawal dalam model `User`. Setiap pengguna mempunyai `role`, contohnya `admin`, `student`, `guard`, atau `institute_authority`. Jika role pengguna tidak sepadan dengan panel, pengguna tidak boleh masuk ke panel tersebut.

## 6. Model Data Utama

Model ialah kelas PHP yang mewakili data dalam database. Hubungan model dalam sistem ini adalah seperti berikut:

- `User` menyimpan akaun login dan role pengguna.
- `Student` menyimpan maklumat pelajar dan disambungkan kepada satu `User`.
- `Vehicle` menyimpan maklumat kenderaan dan dimiliki oleh seorang pelajar.
- `VehicleType` menyimpan kategori kenderaan seperti motor, kereta, van, atau lori.
- `Registration` menyimpan permohonan pendaftaran kenderaan.
- `DigitalSticker` menyimpan QR token, tempoh sah, status sticker, dan laluan imej QR.
- `CheckInLog` menyimpan rekod imbasan atau semakan oleh pengawal.

Hubungan mudah:

```text
User
  -> Student
      -> Vehicle
          -> Registration
              -> DigitalSticker

Guard User
  -> CheckInLog
```

## 7. Aliran Pendaftaran Kenderaan

Aliran kerja utama sistem:

1. Pelajar login ke `/student`.
2. Pelajar mendaftarkan maklumat kenderaan.
3. Pelajar menghantar permohonan pendaftaran.
4. Status permohonan bermula sebagai `pending`.
5. Admin menyemak dan menukar status kepada `verified`.
6. Pihak berkuasa institut meluluskan permohonan.
7. Status menjadi `approved`.
8. Sistem menjana `DigitalSticker` dan QR code.
9. Pelajar boleh melihat atau memuat turun sticker.
10. Pengawal mengimbas QR atau mencari nombor plat di pintu masuk.

Status pendaftaran yang digunakan:

| Status | Maksud |
| --- | --- |
| `pending` | Permohonan baru dihantar dan belum disemak |
| `verified` | Permohonan telah disemak oleh admin |
| `approved` | Permohonan telah diluluskan dan sticker boleh dikeluarkan |
| `rejected` | Permohonan ditolak |

## 8. Reka Bentuk QR Digital Sticker

QR code dijana oleh `QRCodeService`. Service ini membuat token unik menggunakan UUID, kemudian menjana imej QR dalam folder storage awam.

QR code tidak hanya menyimpan teks biasa. Ia mengandungi URL ke halaman sticker:

```text
/sticker/{token}
```

Apabila URL ini dibuka, sistem mencari rekod `DigitalSticker` berdasarkan token. Halaman sticker memaparkan:

- Status sah atau tidak sah.
- QR code.
- Nombor plat.
- Jenis kenderaan.
- Nama pelajar.
- Nombor matrik.
- Tarikh mula dan tamat sah.

Sticker dianggap sah jika:

- Status sticker ialah `valid`.
- Tarikh semasa berada antara tarikh mula dan tarikh tamat sah.

Jika sticker sudah tamat tempoh atau dibatalkan, sistem akan menganggap akses sebagai tidak sah.

## 9. Aliran Semakan Oleh Pengawal

Pengawal menggunakan panel `/guard`. Terdapat dua cara semakan:

1. **Imbas QR**
   - Pengawal imbas QR atau masukkan token.
   - Sistem mencari `DigitalSticker`.
   - Sistem semak status dan tempoh sah.
   - Akses diberi jika sticker masih sah.

2. **Cari nombor plat**
   - Pengawal masukkan nombor plat.
   - Sistem cari `Vehicle`.
   - Sistem cari sticker sah terkini.
   - Akses diberi jika sticker masih sah.

Setiap semakan yang berjaya dikaitkan dengan kenderaan akan disimpan dalam `CheckInLog`. Log ini menyimpan maklumat seperti pengawal yang membuat semakan, kaedah imbasan, status akses, sebab ditolak, IP scanner, dan masa imbasan.

## 10. Reka Bentuk Keselamatan Asas

Sistem ini menggunakan beberapa kawalan asas:

- Login dikendalikan oleh Laravel dan Filament.
- Setiap panel hanya boleh diakses oleh role tertentu.
- Pengguna tidak aktif tidak boleh masuk panel.
- Data pelajar di portal pelajar ditapis supaya pelajar hanya nampak rekod sendiri.
- QR token menggunakan UUID, jadi token sukar diteka.
- Status sticker boleh menjadi `valid`, `expired`, atau `revoked`.
- Rekod semakan disimpan dalam log untuk tujuan audit.

## 11. Data Awal dan Akaun Ujian

Fail `database/seeders/DatabaseSeeder.php` menyediakan data contoh:

- Jenis kenderaan seperti motor, kereta, van, dan lori.
- Akaun admin.
- Akaun pihak berkuasa institut.
- Akaun pengawal.
- Akaun pelajar dengan pendaftaran diluluskan.
- Akaun pelajar dengan pendaftaran masih pending.

Data ini membantu pembangun dan pengguna demo menguji aliran sistem tanpa perlu memasukkan semua data secara manual.

## 12. Ringkasan Reka Bentuk

Sistem ini direka secara modular. Setiap bahagian mempunyai tanggungjawab yang jelas:

- **Model** menjaga struktur dan hubungan data.
- **Filament Resource** menjaga paparan senarai, borang, dan tindakan pengguna.
- **Service** menjaga logik khusus seperti penjanaan QR.
- **Migration** menjaga reka bentuk database.
- **Blade View** menjaga halaman HTML khas seperti paparan sticker awam.

Untuk pembangun baru, cara paling mudah memahami projek ini ialah mula dari aliran berikut:

```text
Pelajar daftar kenderaan
-> Admin verify
-> Authority approve
-> QR sticker dijana
-> Guard scan
-> Check-in log disimpan
```

Dengan memahami aliran ini, struktur folder dan kod Laravel dalam projek akan lebih mudah diikuti.
