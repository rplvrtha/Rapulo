# Rapulo Framework

Rapulo adalah framework PHP yang menggabungkan kemudahan Laravel (MVC, ORM, routing) dengan pendekatan komponen UI terinspirasi React, tanpa pemisahan frontend dan backend. Rapulo menggunakan struktur berbasis fitur untuk mengelompokkan kode berdasarkan fungsionalitas (misalnya, Auth, Dashboard), membuat pengembangan lebih terorganisir dan skalabel.

## Fitur Utama

- **Komponen UI Reaktif**: Bangun UI dengan komponen PHP yang mendukung props dan state.
- **Routing dan Middleware**: Sistem routing sederhana dengan dukungan middleware untuk autentikasi dan validasi.
- **ORM Sederhana**: Interaksi database menggunakan kelas Database yang terinspirasi Eloquent.
- **CLI Terintegrasi**: Alat baris perintah untuk scaffolding komponen, controller, model, dan proyek baru.
- **Struktur Berbasis Fitur**: Semua kode (komponen, controller, model, view) dikelompokkan berdasarkan fitur.

## Prasyarat

- PHP 7.4 atau lebih tinggi (disarankan PHP 8.x).
- Composer untuk manajemen dependensi.
- MySQL atau SQLite (opsional, untuk fitur database).
- Git (opsional, untuk kontrol versi).

## Instalasi

1. **Buat Proyek Baru**: Jalankan perintah berikut untuk membuat proyek Rapulo baru:

   ```bash
   php rapulo create:project my-app
   ```
Ini akan menghasilkan direktori my-app dengan struktur proyek lengkap.

2. **Masuk ke Direktori Proyek**:
``` bash
cd my-app
```

3. **Instal Dependensi**:
```bash
composer install
```


4. **Konfigurasi Database (Opsional)**:

- Buat database (misalnya, di MySQL):
```sql
CREATE DATABASE my_app;
```


- Edit `app/Config/database.php` untuk menyesuaikan kredensial:
```php
return [
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'my_app',
    'username' => 'your_username',
    'password' => 'your_password',
    'charset' => 'utf8mb4',
];
```




5. **Jalankan Server**: Gunakan CLI untuk menjalankan server pengembangan:
```bash
php rapulo serve
```

Atau dengan host/port khusus:
```bash
php rapulo serve 127.0.0.1:8080
```

Buka `http://localhost:8000/login` di browser untuk melihat halaman login default.


## Struktur Direktori
```text
my-app/
├── app/
│   ├── Features/
│   │   ├── Auth/
│   │   │   ├── LoginComponent.php  # Komponen UI untuk login
│   │   │   ├── AuthController.php  # Logika autentikasi
│   │   │   ├── UserModel.php       # Model untuk user
│   │   │   ├── Login.view.php      # Template untuk login
│   ├── Core/
│   │   ├── Component.php           # Base class untuk komponen
│   │   ├── Router.php              # Sistem routing
│   │   ├── Database.php            # Koneksi dan ORM
│   ├── Config/
│   │   ├── app.php                # Konfigurasi aplikasi
│   │   ├── database.php           # Konfigurasi database
├── public/
│   ├── index.php                  # Entry point aplikasi
│   ├── assets/                    # CSS, gambar, dll.
├── resources/
│   ├── views/                     # Template global
│   ├── styles/                    # File CSS
├── routes/
│   ├── web.php                    # Web routes
│   ├── api.php                    # API routes
├── composer.json                  # Manajemen dependensi
├── rapulo                         # CLI untuk scaffolding
```
## Penggunaan CLI
Rapulo menyediakan alat baris perintah untuk mempercepat pengembangan:

- **Membuat Proyek Baru**:
```bash
php rapulo create:project <nama-proyek>
```

- **Membuat Fitur Baru**:
```bash
php rapulo make:feature <nama-fitur>
```

Contoh: `php rapulo make:feature Dashboard`

- **Membuat Komponen**:
```bash
php rapulo make:component <nama-komponen> <nama-fitur>
```

Contoh: `php rapulo make:component Dashboard Dashboard`

- **Membuat Controller**:
```bash
php rapulo make:controller <nama-controller> <nama-fitur>
```

Contoh: `php rapulo make:controller Dashboard Dashboard`

- **Membuat Model**:
```bash
php rapulo make:model <nama-model> <nama-fitur>
```

Contoh: `php rapulo make:model Data Dashboard`

- **Menjalankan Server**:
```bash
php rapulo serve
```



## Debugging

- **Cek Rute**: Akses `http://localhost:8000/?debug_routes=1` untuk melihat daftar rute yang terdaftar.

- **Aktifkan Error Reporting**: Tambahkan di `public/index.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```



## Contoh Pengembangan

1. Buat fitur baru:
```bash
php rapulo make:feature Dashboard
```

2. Buat komponen, controller, dan model:
```bash
php rapulo make:component Dashboard Dashboard
php rapulo make:controller Dashboard Dashboard
php rapulo make:model Data Dashboard
```

3. Tambahkan rute di `routes/web.php`:
```php
$router->get('/dashboard', [Rapulo\Features\Dashboard\DashboardController::class, 'index']);
```

4. Jalankan server dan uji:
```bash
php rapulo serve
```
Akses `http://localhost:8000/dashboard`.


## Deployment
Untuk produksi:

1. Konfigurasi web server (Apache/Nginx) untuk mengarahkan permintaan ke `public/index.php`.

2. Contoh konfigurasi Apache:
```text
<VirtualHost *:80>
    DocumentRoot /path/to/my-app/public
    ServerName my-app.local
    <Directory /path/to/my-app/public>
        AllowOverride All
        Require all granted
        FallbackResource /index.php
    </Directory>
</VirtualHost>
```

3. Atur `debug => false` di `app/Config/app.php`.

4. Optimalkan autoload:
```bash
composer dump-autoload --optimize
```



## Lisensi
Rapulo dilisensikan di bawah MIT License.

#### Copyright by Rapulo Team.
