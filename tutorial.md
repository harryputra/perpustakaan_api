# Tutorial API Perpustakaan

## Pendahuluan
API Perpustakaan ini adalah REST API sederhana untuk mengelola data buku, mahasiswa, dan peminjaman buku. Dibangun menggunakan PHP native tanpa framework, dengan database MySQL.

## Struktur Proyek
```
perpustakaan_api/
├── index.php              # Entry point dan routing
├── config/
│   └── database.php       # Konfigurasi koneksi database
├── controllers/
│   ├── BukuController.php
│   ├── MahasiswaController.php
│   └── PeminjamanController.php
├── models/
│   ├── BukuModel.php
│   ├── MahasiswaModel.php
│   └── PeminjamanModel.php
├── routes/
│   └── api.php            # (Opsional, routing bisa di index.php)
└── utils/
    └── Response.php       # Utility untuk response JSON
```

## Cara Pembuatan dari Awal hingga Akhir

### 1. Setup Environment
- Install XAMPP (atau server PHP + MySQL lainnya)
- Pastikan PHP versi 7.4+ dan MySQL aktif

### 2. Buat Database
Buat database `perpustakaandb` di MySQL, lalu buat tabel:

```sql
CREATE DATABASE perpustakaandb;
USE perpustakaandb;

CREATE TABLE mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    no_telp VARCHAR(20),
    alamat TEXT
);

CREATE TABLE buku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    pengarang VARCHAR(100) NOT NULL,
    penerbit VARCHAR(100) NOT NULL,
    tahun_terbit INT,
    stok INT DEFAULT 0
);

CREATE TABLE peminjaman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mahasiswa_id INT NOT NULL,
    buku_id INT NOT NULL,
    tanggal_pinjam DATETIME DEFAULT CURRENT_TIMESTAMP,
    tanggal_kembali DATETIME NULL,
    status ENUM('dipinjam', 'dikembalikan') DEFAULT 'dipinjam',
    FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id),
    FOREIGN KEY (buku_id) REFERENCES buku(id)
);
```

### 3. Buat File Konfigurasi Database
Buat file `config/database.php`:

```php
<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'perpustakaandb';
    private $username = 'root';
    private $password = ''; // Sesuaikan dengan password MySQL Anda

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
```

### 4. Buat Utility Response
Buat file `utils/Response.php`:

```php
<?php
class Response {
    public static function json($status, $message, $data = null, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        $response = [
            'status' => $status,
            'message' => $message,
        ];
        if ($data !== null) {
            $response['data'] = $data;
        }
        echo json_encode($response);
        exit;
    }
}
?>
```

### 5. Buat Model
Buat model untuk setiap entitas. Contoh `models/BukuModel.php`:

```php
<?php
class BukuModel {
    private $conn;
    private $table = 'buku';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tambahkan method lainnya: getById, create, update, delete, kurangiStok, tambahStok
}
?>
```

Lakukan hal serupa untuk `MahasiswaModel.php` dan `PeminjamanModel.php`.

### 6. Buat Controller
Buat controller untuk menangani logic. Contoh `controllers/BukuController.php`:

```php
<?php
require_once 'models/BukuModel.php';

class BukuController {
    private $bukuModel;

    public function __construct($db) {
        $this->bukuModel = new BukuModel($db);
    }

    public function getAll() {
        $data = $this->bukuModel->getAll();
        Response::json('success', 'Data buku berhasil diambil', $data);
    }

    // Tambahkan method lainnya: getById, create, update, delete
}
?>
```

### 7. Buat Entry Point dan Routing
Buat file `index.php` sebagai entry point:

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
require_once 'utils/Response.php';

try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    Response::json('error', 'Database connection failed: ' . $e->getMessage(), null, 500);
}

$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = ltrim($path, '/');
$path_parts = explode('/', $path);

$method = $_SERVER['REQUEST_METHOD'];

$input = [];
if ($method == 'POST' || $method == 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $input = $_POST;
    }
}

try {
    if (isset($path_parts[0]) && $path_parts[0] == 'api') {
        $resource = isset($path_parts[1]) ? $path_parts[1] : '';
        $id = isset($path_parts[2]) ? $path_parts[2] : null;
        $subresource = isset($path_parts[3]) ? $path_parts[3] : null;

        switch ($resource) {
            case 'buku':
                require_once 'controllers/BukuController.php';
                $controller = new BukuController($db);
                if ($method == 'GET') {
                    if ($id) {
                        $controller->getById($id);
                    } else {
                        $controller->getAll();
                    }
                } elseif ($method == 'POST') {
                    $controller->create($input);
                } elseif ($method == 'PUT') {
                    if ($id) {
                        $controller->update($id, $input);
                    } else {
                        Response::json('error', 'ID diperlukan untuk update', null, 400);
                    }
                } elseif ($method == 'DELETE') {
                    if ($id) {
                        $controller->delete($id);
                    } else {
                        Response::json('error', 'ID diperlukan untuk delete', null, 400);
                    }
                } else {
                    Response::json('error', 'Method tidak diizinkan', null, 405);
                }
                break;

            // Tambahkan case untuk mahasiswa dan peminjaman
            default:
                Response::json('error', 'Resource tidak ditemukan', null, 404);
                break;
        }
    } else {
        Response::json('error', 'API endpoint tidak valid', null, 404);
    }
} catch (Exception $e) {
    Response::json('error', 'Internal server error: ' . $e->getMessage(), null, 500);
}
?>
```

## Panduan Instalasi

### 1. Install XAMPP
- Download dan install XAMPP dari https://www.apachefriends.org/
- Jalankan XAMPP Control Panel, start Apache dan MySQL

### 2. Setup Proyek
- Buat folder `perpustakaan_api` di `C:\xampp\htdocs\`
- Copy semua file proyek ke folder tersebut

### 3. Setup Database
- Buka phpMyAdmin (http://localhost/phpmyadmin)
- Buat database `perpustakaandb`
- Import atau jalankan SQL script untuk membuat tabel

### 4. Jalankan Server
- Buka Command Prompt
- Navigasi ke folder proyek: `cd C:\xampp\htdocs\perpustakaan_api`
- Jalankan server: `php -S localhost:8000`

## Konfigurasi

### Database
Edit `config/database.php` untuk mengubah kredensial database jika perlu.

### Error Reporting
Di `index.php`, error reporting sudah diaktifkan untuk development. Untuk production, nonaktifkan.

## Testing Semua Endpoint

Gunakan Postman atau curl untuk test endpoint. Server berjalan di `http://localhost:8000`.

### Buku Endpoints

#### GET /api/buku
- **Deskripsi**: Ambil semua buku
- **Method**: GET
- **Response**: List buku

```bash
curl -X GET http://localhost:8000/api/buku
```

#### GET /api/buku/{id}
- **Deskripsi**: Ambil buku berdasarkan ID
- **Method**: GET
- **Response**: Detail buku

```bash
curl -X GET http://localhost:8000/api/buku/1
```

#### POST /api/buku
- **Deskripsi**: Tambah buku baru
- **Method**: POST
- **Headers**: Content-Type: application/json
- **Body**:
```json
{
    "judul": "Judul Buku",
    "pengarang": "Nama Pengarang",
    "penerbit": "Nama Penerbit",
    "tahun_terbit": 2023,
    "stok": 10
}
```

```bash
curl -X POST http://localhost:8000/api/buku \
  -H "Content-Type: application/json" \
  -d '{"judul":"Judul Buku","pengarang":"Nama Pengarang","penerbit":"Nama Penerbit","tahun_terbit":2023,"stok":10}'
```

#### PUT /api/buku/{id}
- **Deskripsi**: Update buku
- **Method**: PUT
- **Headers**: Content-Type: application/json
- **Body**: Field yang ingin diupdate

```bash
curl -X PUT http://localhost:8000/api/buku/1 \
  -H "Content-Type: application/json" \
  -d '{"stok":15}'
```

#### DELETE /api/buku/{id}
- **Deskripsi**: Hapus buku
- **Method**: DELETE

```bash
curl -X DELETE http://localhost:8000/api/buku/1
```

### Mahasiswa Endpoints

#### GET /api/mahasiswa
- Ambil semua mahasiswa

```bash
curl -X GET http://localhost:8000/api/mahasiswa
```

#### GET /api/mahasiswa/{id}
- Ambil mahasiswa berdasarkan ID

```bash
curl -X GET http://localhost:8000/api/mahasiswa/1
```

#### POST /api/mahasiswa
- Tambah mahasiswa baru
- Body: {"nim":"12345","nama":"Nama Mahasiswa","no_telp":"08123456789","alamat":"Alamat"}

#### PUT /api/mahasiswa/{id}
- Update mahasiswa

#### DELETE /api/mahasiswa/{id}
- Hapus mahasiswa

### Peminjaman Endpoints

#### GET /api/peminjaman
- Ambil semua peminjaman

```bash
curl -X GET http://localhost:8000/api/peminjaman
```

#### GET /api/peminjaman/{id}
- Ambil peminjaman berdasarkan ID

#### GET /api/peminjaman/{mahasiswa_id}/riwayat
- Ambil riwayat peminjaman mahasiswa

```bash
curl -X GET http://localhost:8000/api/peminjaman/1/riwayat
```

#### POST /api/peminjaman/pinjam
- Pinjam buku
- Body: {"mahasiswa_id":1,"buku_id":1}

```bash
curl -X POST http://localhost:8000/api/peminjaman/pinjam \
  -H "Content-Type: application/json" \
  -d '{"mahasiswa_id":1,"buku_id":1}'
```

#### PUT /api/peminjaman/{id}/kembali
- Kembalikan buku

```bash
curl -X PUT http://localhost:8000/api/peminjaman/1/kembali
```

## Catatan
- Pastikan stok buku cukup sebelum pinjam
- Mahasiswa maksimal pinjam 3 buku aktif
- Buku yang sedang dipinjam tidak bisa dipinjam lagi oleh mahasiswa yang sama
- Semua response dalam format JSON dengan status success/error