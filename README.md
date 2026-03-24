# API Perpustakaan

## Deskripsi Proyek

API Perpustakaan adalah REST API sederhana untuk mengelola sistem perpustakaan digital. API ini memungkinkan pengelolaan data buku, mahasiswa, dan transaksi peminjaman buku. Dibangun menggunakan PHP native tanpa framework eksternal, dengan database MySQL untuk penyimpanan data.

### Fitur Utama
- ✅ Manajemen Buku (CRUD: Create, Read, Update, Delete)
- ✅ Manajemen Mahasiswa (CRUD)
- ✅ Sistem Peminjaman Buku
- ✅ Riwayat Peminjaman per Mahasiswa
- ✅ Validasi Stok Buku
- ✅ Batas Peminjaman (maksimal 3 buku per mahasiswa)
- ✅ Pencegahan peminjaman buku yang sama oleh mahasiswa yang sama
- ✅ Response JSON standar
- ✅ Error handling yang robust

## Teknologi yang Digunakan

- **Bahasa Pemrograman**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Server**: Apache/Nginx atau PHP Built-in Server
- **Library**: PDO (untuk database connection)
- **Format Data**: JSON

## Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Server web (Apache/Nginx) atau PHP built-in server
- Composer (opsional, jika ingin menggunakan autoloader)

## Struktur Proyek

```
perpustakaan_api/
├── index.php                 # Entry point dan routing utama
├── tutorial.md               # Panduan lengkap pembuatan dan penggunaan
├── README.md                 # Dokumentasi proyek (file ini)
├── config/
│   └── database.php          # Konfigurasi koneksi database
├── controllers/
│   ├── BukuController.php    # Controller untuk buku
│   ├── MahasiswaController.php # Controller untuk mahasiswa
│   └── PeminjamanController.php # Controller untuk peminjaman
├── models/
│   ├── BukuModel.php         # Model untuk buku
│   ├── MahasiswaModel.php    # Model untuk mahasiswa
│   └── PeminjamanModel.php   # Model untuk peminjaman
├── routes/
│   └── api.php               # Definisi routing (opsional)
└── utils/
    └── Response.php          # Utility untuk response JSON
```

## Instalasi

### 1. Clone atau Download Proyek
```bash
# Jika menggunakan Git
git clone https://github.com/username/perpustakaan_api.git
cd perpustakaan_api

# Atau download ZIP dan ekstrak
```

### 2. Setup Environment
- **Opsi 1: XAMPP (Windows)**
  - Download dan install XAMPP dari [apachefriends.org](https://www.apachefriends.org/)
  - Jalankan XAMPP Control Panel
  - Start Apache dan MySQL

- **Opsi 2: Linux/Mac**
  - Pastikan PHP dan MySQL terinstall
  - Jalankan MySQL service

### 3. Setup Database
1. Buka phpMyAdmin (http://localhost/phpmyadmin) atau MySQL CLI
2. Buat database baru:
   ```sql
   CREATE DATABASE perpustakaandb;
   ```
3. Import tabel (jalankan query berikut):
   ```sql
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

### 4. Konfigurasi Database
Edit file `config/database.php`:
```php
private $host = 'localhost';
private $dbname = 'perpustakaandb';
private $username = 'root';  // Sesuaikan dengan username MySQL Anda
private $password = '';      // Sesuaikan dengan password MySQL Anda
```

### 5. Jalankan Server
- **Menggunakan PHP Built-in Server** (Direkomendasikan untuk development):
  ```bash
  cd path/to/perpustakaan_api
  php -S localhost:8000
  ```
  API akan tersedia di: http://localhost:8000

- **Menggunakan Apache/Nginx**:
  - Copy folder proyek ke `htdocs` (XAMPP) atau `/var/www/html`
  - Akses via: http://localhost/perpustakaan_api

## Penggunaan API

### Base URL
```
http://localhost:8000/api/
```

### Format Response
Semua response dalam format JSON:
```json
{
    "status": "success|error",
    "message": "Pesan response",
    "data": {} // Opsional, berisi data jika ada
}
```

### Endpoint API

#### 1. Buku Endpoints

##### GET /api/buku
Ambil semua buku
```bash
curl -X GET http://localhost:8000/api/buku
```

##### GET /api/buku/{id}
Ambil detail buku berdasarkan ID
```bash
curl -X GET http://localhost:8000/api/buku/1
```

##### POST /api/buku
Tambah buku baru
```bash
curl -X POST http://localhost:8000/api/buku \
  -H "Content-Type: application/json" \
  -d '{
    "judul": "Judul Buku Baru",
    "pengarang": "Nama Pengarang",
    "penerbit": "Nama Penerbit",
    "tahun_terbit": 2023,
    "stok": 5
  }'
```

##### PUT /api/buku/{id}
Update buku
```bash
curl -X PUT http://localhost:8000/api/buku/1 \
  -H "Content-Type: application/json" \
  -d '{
    "stok": 10
  }'
```

##### DELETE /api/buku/{id}
Hapus buku
```bash
curl -X DELETE http://localhost:8000/api/buku/1
```

#### 2. Mahasiswa Endpoints

##### GET /api/mahasiswa
Ambil semua mahasiswa
```bash
curl -X GET http://localhost:8000/api/mahasiswa
```

##### GET /api/mahasiswa/{id}
Ambil detail mahasiswa
```bash
curl -X GET http://localhost:8000/api/mahasiswa/1
```

##### POST /api/mahasiswa
Tambah mahasiswa baru
```bash
curl -X POST http://localhost:8000/api/mahasiswa \
  -H "Content-Type: application/json" \
  -d '{
    "nim": "12345678",
    "nama": "Nama Mahasiswa",
    "no_telp": "08123456789",
    "alamat": "Alamat Lengkap"
  }'
```

##### PUT /api/mahasiswa/{id}
Update mahasiswa
```bash
curl -X PUT http://localhost:8000/api/mahasiswa/1 \
  -H "Content-Type: application/json" \
  -d '{
    "no_telp": "08198765432"
  }'
```

##### DELETE /api/mahasiswa/{id}
Hapus mahasiswa
```bash
curl -X DELETE http://localhost:8000/api/mahasiswa/1
```

#### 3. Peminjaman Endpoints

##### GET /api/peminjaman
Ambil semua data peminjaman
```bash
curl -X GET http://localhost:8000/api/peminjaman
```

##### GET /api/peminjaman/{id}
Ambil detail peminjaman
```bash
curl -X GET http://localhost:8000/api/peminjaman/1
```

##### GET /api/peminjaman/{mahasiswa_id}/riwayat
Ambil riwayat peminjaman mahasiswa
```bash
curl -X GET http://localhost:8000/api/peminjaman/1/riwayat
```

##### POST /api/peminjaman/pinjam
Pinjam buku
```bash
curl -X POST http://localhost:8000/api/peminjaman/pinjam \
  -H "Content-Type: application/json" \
  -d '{
    "mahasiswa_id": 1,
    "buku_id": 1
  }'
```

##### PUT /api/peminjaman/{id}/kembali
Kembalikan buku
```bash
curl -X PUT http://localhost:8000/api/peminjaman/1/kembali
```

## Testing

### Menggunakan Postman
1. Import collection atau buat request manual
2. Set base URL: http://localhost:8000
3. Test setiap endpoint dengan method dan body yang sesuai

### Menggunakan cURL
Lihat contoh di bagian "Penggunaan API" di atas.

### Menggunakan Browser
Untuk endpoint GET, bisa langsung akses via browser:
- http://localhost:8000/api/buku
- http://localhost:8000/api/mahasiswa

## Aturan Bisnis

1. **Stok Buku**: Tidak bisa pinjam jika stok = 0
2. **Batas Peminjaman**: Mahasiswa maksimal pinjam 3 buku aktif
3. **Peminjaman Ganda**: Mahasiswa tidak bisa pinjam buku yang sama jika belum dikembalikan
4. **Validasi Input**: NIM mahasiswa harus unik, field wajib harus diisi

## Error Codes

- **200**: Success
- **400**: Bad Request (input tidak valid)
- **404**: Not Found (resource tidak ditemukan)
- **405**: Method Not Allowed
- **500**: Internal Server Error

## Development

### Menambah Endpoint Baru
1. Buat model di folder `models/`
2. Buat controller di folder `controllers/`
3. Tambahkan routing di `index.php`

### Debugging
- Error reporting sudah aktif di development
- Cek log PHP untuk error detail
- Gunakan `var_dump()` untuk debug variable

## Kontribusi

1. Fork repositori
2. Buat branch fitur baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## Lisensi

Proyek ini menggunakan lisensi MIT. Lihat file `LICENSE` untuk detail lebih lanjut.

## Kontak

- **Author**: [Nama Anda]
- **Email**: your.email@example.com
- **GitHub**: [https://github.com/username/perpustakaan_api](https://github.com/username/perpustakaan_api)

## Changelog

### v1.0.0 (2026-03-24)
- Initial release
- CRUD untuk Buku, Mahasiswa, Peminjaman
- Validasi dan error handling
- Dokumentasi lengkap

---

Untuk panduan lebih detail tentang pembuatan API ini dari awal, lihat file [tutorial.md](tutorial.md).