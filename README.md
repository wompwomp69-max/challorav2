# Challora Recruitment Platform

Platform rekrutmen dengan role User (pelamar) dan HR (pembuat lowongan). Dokumen ini menjelaskan arsitektur dan alur kerja aplikasi.

---

## Daftar Isi

1. [Entry Point](#1-entry-point--dari-mana-semuanya-dimulai)
2. [Routing & URL](#2-bagaimana-url-sampai-ke-indexphp)
3. [Config & Autoload](#3-config--autoload)
4. [Alur Data (MVC)](#4-bagaimana-data-diambil--hubungan-model-controller-view)
5. [Navigasi Antar Halaman](#5-bagaimana-satu-page-melempar-ke-page-lain)
6. [Sistem View](#6-bagaimana-view-ditampilkan)
7. [Diagram Alur Lengkap](#7-diagram-alur-lengkap)
8. [Ringkasan Koneksi](#8-ringkasan-koneksi)

---

## 1. Entry Point – Dari mana semuanya dimulai

### `index.php` (di root proyek)

```php
header('Location: /challorav2/public/', true, 302);
exit;
```

File ini hanya mengalihkan semua request ke folder `public/`, karena semua file yang diakses user berada di sana.

---

### `public/index.php` – Front Controller (inti routing)

Ini adalah **otak aplikasi**. Setiap request melewati file ini. Alurnya:

1. **Load config** via `require config/app.php` (autoload, session, database, helpers)
2. **Ambil URL & method** dari `$_GET['url']` (diisi oleh .htaccess) dan `$_SERVER['REQUEST_METHOD']`
3. **Cocokkan dengan routing** – array `$routes` memetakan kombinasi (method, path) ke pasangan `[Controller, method]`
4. **Instantiasi controller** dan panggil method-nya
5. Jika tidak cocok → 404

---

## 2. Bagaimana URL sampai ke index.php

### `.htaccess` di `public/`

```apache
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

- Semua request yang **bukan file/direktori fisik** akan diarahkan ke `index.php?url=...`
- Contoh: `/challorav2/public/jobs` → `index.php?url=jobs`
- Contoh: `/challorav2/public/auth/login` → `index.php?url=auth/login`

Jadi semua URL “cantik” tetap diproses oleh satu file `index.php`.

---

## 3. Config & Autoload

### `config/app.php`

Di-load di awal dan melakukan:

- Define konstanta path (BASE_PATH, APP_PATH, BASE_URL)
- Composer autoload
- Custom autoloader untuk class `core/`, `controllers/`, `models/`
- Load database config & helpers

### Autoloader

Ketika PHP menemukan `new JobController()`, ia akan otomatis mencari file `JobController.php` di folder controllers, lalu `require` file tersebut. Begitu pula untuk Model dan class di `core/`.

---

## 4. Bagaimana Data Diambil – Hubungan Model, Controller, View

### Database (`core/Database.php` + `config/database.php`)

- Menggunakan PDO dengan pola singleton (satu koneksi untuk seluruh request)
- Fungsi `getDB()` mengembalikan objek PDO

### Model – Pengambil Data dari Database

Model bertugas **query ke database** dan mengembalikan data. Contoh `Job` model:

```php
// Job.php
public function all(): array {
    $stmt = $this->db->query('SELECT j.*, u.name AS created_by_name FROM jobs j ...');
    return $stmt->fetchAll();  // array of rows
}

public function findById(int $id): ?array {
    $stmt = $this->db->prepare('SELECT ... WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}
```

### Controller – Penghubung Request, Model, dan View

Controller:

1. Menerima request (GET/POST, `$_GET`, `$_POST`, dll)
2. Memanggil **Model** untuk ambil/simpan data
3. Memanggil **View** dengan data tersebut via `render_view()`
4. Atau melakukan **redirect** ke URL lain

**Contoh: JobController::index()**

```php
public function index(): void {
    requireLogin();                         // Cek login
    $jobs = $this->jobModel->all();         // Ambil data dari Model
    render_view('user/jobs/index', [        // Kirim ke View
        'jobs' => $jobs,
        'appliedJobIds' => $appliedJobIds,
        'pageTitle' => 'Lowongan',
    ]);
}
```

**Contoh: AuthController::login() (saat POST)**

```php
$user = $this->userModel->findByEmail($email);
if ($user && password_verify(...)) {
    $_SESSION['user_id'] = $user['id'];
    redirect('/jobs');  // Lempar ke halaman lain
}
```

---

## 5. Bagaimana Satu Page "Melempar" ke Page Lain

### Redirect

```php
redirect('/jobs');           // Ke halaman lowongan
redirect('/auth/login');     // Ke halaman login
redirect('/jobs/show?id=5'); // Ke detail job id 5
```

Helper `redirect()` akan mengubah path seperti `/jobs` menjadi URL penuh, misal: `BASE_URL . '/index.php?url=jobs'`, lalu kirim header `Location` dan `exit`.

### Link biasa (dari view)

```html
<a href="<?= BASE_URL ?>/jobs">Lowongan</a>
<a href="<?= BASE_URL ?>/jobs/show?id=<?= $job['id'] ?>">Detail</a>
```

Saat diklik, browser akan request URL tersebut, lalu .htaccess + index.php memproses lagi.

### Form POST

```html
<form method="post" action="<?= BASE_URL ?>/auth/login">
```

Saat submit, data POST dikirim ke route yang sama. Controller `AuthController::login()` memproses `$_POST`, validasi, login, lalu `redirect()` ke halaman tujuan.

---

## 6. Bagaimana View Ditampilkan

### Fungsi `render_view()` (di `core/helpers.php`)

```php
function render_view(string $view, array $data = []): void {
    extract($data);                              // $jobs, $appliedJobIds jadi variabel
    ob_start();
    require APP_PATH . '/views/' . $view . '.php'; // Load user/jobs/index.php
    $content = ob_get_clean();                    // Output view disimpan di $content
    require APP_PATH . '/views/layouts/user.php';  // Layout membungkus $content
}
```

1. `$data` di-`extract` menjadi variabel (misal: `$jobs`, `$pageTitle`)
2. View (misal `user/jobs/index.php`) di-include dan output-nya ditangkap ke `$content`
3. Layout (header, navbar, footer) di-include, dengan `$content` di tengah

### Struktur View

```
app/views/
├── layouts/
│   ├── header.php
│   ├── footer.php
│   ├── user.php    (layout user biasa)
│   └── hr.php      (layout HR dengan sidebar)
├── auth/
│   ├── login.php
│   └── register.php
├── user/
│   ├── jobs/
│   │   ├── index.php   (daftar lowongan)
│   │   └── show.php    (detail lowongan)
│   └── applications/
│       └── index.php
└── hr/
    └── ...
```

Layout dipilih otomatis: view yang path-nya dimulai `hr/` pakai layout `hr`, sisanya pakai layout `user`.

---

## 7. Diagram Alur Lengkap

```
Browser: GET /challorav2/public/jobs
    │
    ▼
.htaccess → index.php?url=jobs
    │
    ▼
public/index.php
    ├─ config/app.php (autoload, session, DB)
    ├─ $url = 'jobs', $method = 'GET'
    ├─ $routes['GET']['jobs'] = [JobController::class, 'index']
    └─ new JobController(); $controller->index();
    │
    ▼
JobController::index()
    ├─ requireLogin()
    ├─ $jobs = $this->jobModel->all()     ← Model query ke DB
    └─ render_view('user/jobs/index', ['jobs' => $jobs, ...])
    │
    ▼
render_view()
    ├─ extract($data)
    ├─ require views/user/jobs/index.php  ← View akses $jobs
    ├─ $content = output
    └─ require layouts/user.php           ← Layout bungkus $content
    │
    ▼
HTML terkirim ke browser
```

---

## 8. Ringkasan Koneksi

| Komponen | Peran |
|----------|-------|
| `index.php` (root) | Redirect ke `public/` |
| `public/.htaccess` | Semua URL → `index.php?url=...` |
| `public/index.php` | **Routing**: URL + method → Controller + method |
| `config/app.php` | Load autoload, database, helpers |
| `config/database.php` | Kredensial DB, fungsi `getDB()` |
| `core/Database.php` | Koneksi PDO (singleton) |
| **Model** | Query ke DB, return array |
| **Controller** | Terima request → panggil Model → `render_view()` atau `redirect()` |
| **View** | Tampilkan data dari Controller |
| `render_view()` | Load view + layout, output HTML |
| `redirect()` | Lempar user ke URL lain (HTTP 302) |

---

## Struktur Routing Utama

| Method | URL | Controller | Method |
|--------|-----|------------|--------|
| GET | auth/login | AuthController | login |
| GET | auth/register | AuthController | register |
| GET | jobs | JobController | index |
| GET | jobs/show | JobController | show |
| POST | jobs/apply | ApplicationController | apply |
| GET | applications | ApplicationController | index |
| GET | user/settings | UserController | profile |
| GET | hr/jobs | HrJobController | index |
| POST | hr/jobs/store | HrJobController | create |
| ... | ... | ... | ... |

Lihat `public/index.php` untuk daftar lengkap route.
