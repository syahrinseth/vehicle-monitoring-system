# Sistem Pemantauan Kenderaan (VMS)

Sistem Pemantauan Kenderaan (VMS) ialah aplikasi web berasaskan Laravel yang direka untuk menguruskan pendaftaran kenderaan pelajar di institusi pengajian tinggi. Sistem ini membolehkan pelajar mendaftar kenderaan, pihak berkuasa mengesahkan pendaftaran, dan pengawal memantau kenderaan yang memasuki kawasan kampus menggunakan imbasan kod QR atau nombor plat.

---

## Ciri-Ciri Utama

- Pendaftaran kenderaan pelajar dengan muat naik dokumen
- Aliran kelulusan berbilang peringkat (pelajar → pihak berkuasa → admin)
- Penjanaan pelekat digital (stiker QR) secara automatik selepas kelulusan
- Imbasan kenderaan oleh pengawal melalui kamera (kod QR) atau nombor plat
- Log masuk/keluar kenderaan secara masa nyata
- Papan pemuka statistik untuk setiap peranan pengguna

---

## Peranan Pengguna

| Peranan | Penerangan | Panel Akses |
|---------|-----------|-------------|
| **Admin** | Pengurusan penuh sistem, pengguna, dan data | `/admin` |
| **Pihak Berkuasa Institut** | Semak dan lulus/tolak pendaftaran kenderaan | `/authority` |
| **Pengawal** | Imbas kenderaan di pintu masuk/keluar | `/guard` |
| **Pelajar** | Daftar kenderaan dan muat turun pelekat digital | `/student` |

---

## Tindanan Teknologi

| Lapisan | Teknologi |
|---------|-----------|
| Bahasa | PHP 8.3 |
| Rangka Kerja Backend | Laravel 13 |
| Antara Muka Admin | Filament v5 (Livewire v3 + Alpine.js) |
| Pembinaan Frontend | Vite 8 + Tailwind CSS 4 |
| Pangkalan Data (lalai) | SQLite (boleh dikonfigurasi ke MySQL/PostgreSQL) |
| Penjanaan Kod QR | `chillerlan/php-qrcode` v5 |
| Pengimbasan Kod QR | `html5-qrcode` (berasaskan kamera) |
| Sesi/Giliran/Cache | Berasaskan pangkalan data |
| Ujian | PHPUnit 12 |

---

## Struktur Direktori

```
vehicle-monitoring-system/
├── app/
│   ├── Filament/           # Panel UI untuk setiap peranan (Admin, Authority, Guard, Student)
│   ├── Models/             # Model Eloquent (User, Student, Vehicle, Registration, dll.)
│   └── Services/           # Perkhidmatan kelas (QRCodeService)
├── database/
│   ├── migrations/         # 9 fail migrasi pangkalan data
│   └── seeders/            # Data ujian awal
├── resources/
│   ├── css/                # Tema Filament dan CSS aplikasi
│   ├── js/                 # JavaScript aplikasi
│   └── views/              # Templat Blade
├── routes/
│   └── web.php             # Definisi laluan
└── docs/
    └── USER_FLOW.md        # Dokumentasi aliran pengguna
```

---

## Akaun Ujian (Seeded)

Akaun-akaun berikut tersedia selepas menjalankan `php artisan migrate --seed`. Semua akaun menggunakan kata laluan: **`password`**

| Peranan | E-mel | Nama |
|---------|-------|------|
| Admin | `admin@vms.test` | System Admin |
| Pihak Berkuasa | `authority@vms.test` | Prof. Dr. Ahmad |
| Pengawal | `guard@vms.test` | Guard Hassan |
| Pelajar (diluluskan) | `student1@vms.test` | Ali bin Abu (CS2024001) |
| Pelajar (dalam proses) | `student2@vms.test` | Siti binti Rahman (CS2024002) |

---

## Persediaan Tempatan (Local Setup)

### Keperluan Sistem

Pastikan perisian berikut telah dipasang pada mesin anda:

- **PHP** >= 8.3 (dengan extension: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML)
- **Composer** (pengurus pakej PHP)
- **Node.js** >= 18 dan **npm**
- **Git**
- **Laravel Herd** (macOS) atau **XAMPP/Laragon** (Windows) — pilihan

### Langkah-Langkah Pemasangan

**1. Klon repositori**

```bash
git clone <url-repositori> vehicle-monitoring-system
cd vehicle-monitoring-system
```

**2. Pasang dependensi PHP**

```bash
composer install
```

**3. Sediakan fail konfigurasi persekitaran**

```bash
cp .env.example .env
php artisan key:generate
```

**4. Konfigurasi pangkalan data**

Buka fail `.env` dan pastikan tetapan pangkalan data adalah seperti berikut (SQLite lalai, tiada konfigurasi tambahan diperlukan):

```ini
DB_CONNECTION=sqlite
```

Jika anda ingin menggunakan MySQL, ubah kepada:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vms
DB_USERNAME=root
DB_PASSWORD=your_password
```

**5. Jalankan migrasi dan semai data ujian**

```bash
php artisan migrate --seed
```

**6. Buat pautan simbolik storage**

```bash
php artisan storage:link
```

**7. Pasang dependensi JavaScript dan bina aset**

```bash
npm install
npm run build
```

**8. Jalankan pelayan pembangunan**

Gunakan arahan berikut untuk memulakan semua perkhidmatan serentak (pelayan web, queue worker, log watcher, dan Vite HMR):

```bash
composer dev
```

Atau jalankan pelayan web sahaja:

```bash
php artisan serve
```

### URL Akses Panel

Selepas pelayan berjalan, buka pelayar dan layari:

| Panel | URL |
|-------|-----|
| Pelajar | `http://localhost:8000/student` |
| Admin | `http://localhost:8000/admin` |
| Pihak Berkuasa | `http://localhost:8000/authority` |
| Pengawal | `http://localhost:8000/guard` |

> **Nota:** Jika menggunakan Laravel Herd, URL lalai ialah `http://vehicle-monitoring-system.test`

---

## Deployment Awam (Production Setup)

### Keperluan Pelayan

- **Sistem Operasi:** Ubuntu 22.04 LTS atau lebih baru (disyorkan)
- **PHP** >= 8.3 dengan extension: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, PDO_MySQL, Tokenizer, XML, GD
- **Composer** >= 2.x
- **Node.js** >= 18 dan **npm**
- **Nginx** atau **Apache**
- **MySQL** >= 8.0 atau **PostgreSQL** >= 14
- **Supervisor** (untuk queue worker)

### Langkah-Langkah Deployment

**1. Klon repositori ke pelayan**

```bash
git clone <url-repositori> /var/www/vms
cd /var/www/vms
```

**2. Pasang dependensi PHP (mod pengeluaran)**

```bash
composer install --optimize-autoloader --no-dev
```

**3. Sediakan fail konfigurasi persekitaran**

```bash
cp .env.example .env
php artisan key:generate
```

**4. Konfigurasi `.env` untuk pengeluaran**

```ini
APP_NAME="Sistem Pemantauan Kenderaan"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vms_production
DB_USERNAME=vms_user
DB_PASSWORD=kata_laluan_selamat

QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=noreply@domain-anda.com
MAIL_PASSWORD=kata_laluan_mel
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@domain-anda.com
MAIL_FROM_NAME="Sistem Pemantauan Kenderaan"
```

**5. Cipta pangkalan data MySQL dan jalankan migrasi**

```bash
mysql -u root -p -e "CREATE DATABASE vms_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate --force
```

**6. Buat pautan simbolik storage**

```bash
php artisan storage:link
```

**7. Bina aset frontend**

```bash
npm install
npm run build
```

**8. Optimasi Laravel untuk pengeluaran**

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

**9. Tetapkan kebenaran folder**

```bash
chown -R www-data:www-data /var/www/vms
chmod -R 755 /var/www/vms/storage
chmod -R 755 /var/www/vms/bootstrap/cache
```

### Konfigurasi Nginx

Cipta fail konfigurasi Nginx baru:

```bash
sudo nano /etc/nginx/sites-available/vms
```

Masukkan konfigurasi berikut:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name domain-anda.com www.domain-anda.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name domain-anda.com www.domain-anda.com;

    root /var/www/vms/public;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/domain-anda.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/domain-anda.com/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan konfigurasi:

```bash
sudo ln -s /etc/nginx/sites-available/vms /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Konfigurasi Queue Worker (Supervisor)

Cipta fail konfigurasi Supervisor:

```bash
sudo nano /etc/supervisor/conf.d/vms-worker.conf
```

Masukkan konfigurasi berikut:

```ini
[program:vms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/vms/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/vms/storage/logs/worker.log
stopwaitsecs=3600
```

Aktifkan queue worker:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start vms-worker:*
```

### Konfigurasi Jadual Laravel (Cron)

Tambah entri cron untuk menjalankan jadual Laravel setiap minit:

```bash
sudo crontab -e -u www-data
```

Tambah baris berikut:

```
* * * * * cd /var/www/vms && php artisan schedule:run >> /dev/null 2>&1
```

### Sijil SSL (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d domain-anda.com -d www.domain-anda.com
```

---

## Lesen

Projek ini adalah perisian sumber terbuka yang dilesenkan di bawah [Lesen MIT](https://opensource.org/licenses/MIT).
