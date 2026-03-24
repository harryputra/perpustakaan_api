<?php
require_once 'models/PeminjamanModel.php';
require_once 'models/BukuModel.php';
require_once 'models/MahasiswaModel.php';
 
class PeminjamanController {
    private $peminjamanModel;
    private $bukuModel;
    private $mahasiswaModel;
 
    public function __construct($db) {
        $this->peminjamanModel = new PeminjamanModel($db);
        $this->bukuModel = new BukuModel($db);
        $this->mahasiswaModel = new MahasiswaModel($db);
    }
 
    public function getAll() {
        $data = $this->peminjamanModel->getAll();
        Response::json('success', 'Data peminjaman', $data);
    }
 
    public function getById($id) {
        if (!is_numeric($id)) {
            Response::json('error', 'ID tidak valid', null, 400);
        }
        $data = $this->peminjamanModel->getById($id);
        if ($data) {
            Response::json('success', 'Detail peminjaman', $data);
        } else {
            Response::json('error', 'Peminjaman tidak ditemukan', null, 404);
        }
    }
 
    public function pinjam($input) {
        // Validasi input
        if (empty($input['mahasiswa_id']) || empty($input['buku_id'])) {
            Response::json('error', 'mahasiswa_id dan buku_id wajib diisi', null, 400);
        }
        $mahasiswa_id = (int)$input['mahasiswa_id'];
        $buku_id = (int)$input['buku_id'];
 
        // Cek mahasiswa
        $mahasiswa = $this->mahasiswaModel->getById($mahasiswa_id);
        if (!$mahasiswa) {
            Response::json('error', 'Mahasiswa tidak ditemukan', null, 404);
        }
 
        // Cek buku
        $buku = $this->bukuModel->getById($buku_id);
        if (!$buku) {
            Response::json('error', 'Buku tidak ditemukan', null, 404);
        }
 
        // Cek stok
        if ($buku['stok'] <= 0) {
            Response::json('error', 'Stok buku tidak tersedia', null, 400);
        }
 
        // Cek batas pinjam (maksimal 3)
        $jumlahAktif = $this->mahasiswaModel->getJumlahPeminjamanAktif($mahasiswa_id);
        if ($jumlahAktif >= 3) {
            Response::json('error', 'Mahasiswa sudah meminjam 3 buku, tidak dapat meminjam lagi', null, 400);
        }
 
        // Cek apakah sudah meminjam buku yang sama (belum dikembalikan)
        if ($this->peminjamanModel->isBookBorrowedByMahasiswa($mahasiswa_id, $buku_id)) {
            Response::json('error', 'Mahasiswa sedang meminjam buku ini dan belum mengembalikan', null, 400);
        }
 
        // Kurangi stok buku
        if (!$this->bukuModel->kurangiStok($buku_id)) {
            Response::json('error', 'Gagal mengurangi stok buku', null, 500);
        }
 
        // Buat peminjaman
        $data = [
            'mahasiswa_id' => $mahasiswa_id,
            'buku_id' => $buku_id
        ];
        $id = $this->peminjamanModel->create($data);
        if ($id) {
            Response::json('success', 'Buku berhasil dipinjam', ['id_peminjaman' => $id], 201);
        } else {
            // Rollback? Untuk sederhana, kita tidak melakukan rollback, namun di production perlu transaksi.
            Response::json('error', 'Gagal mencatat peminjaman', null, 500);
        }
    }
 
    public function kembali($id) {
        if (!is_numeric($id)) {
            Response::json('error', 'ID tidak valid', null, 400);
        }
        // Cek peminjaman
        $peminjaman = $this->peminjamanModel->getById($id);
        if (!$peminjaman) {
            Response::json('error', 'Peminjaman tidak ditemukan', null, 404);
        }
        if ($peminjaman['status'] == 'dikembalikan') {
            Response::json('error', 'Buku sudah dikembalikan sebelumnya', null, 400);
        }
 
        // Tambah stok buku
        $buku_id = $peminjaman['buku_id'];
        if (!$this->bukuModel->tambahStok($buku_id)) {
            Response::json('error', 'Gagal menambah stok buku', null, 500);
        }
 
        // Update peminjaman
        if ($this->peminjamanModel->returnBook($id)) {
            Response::json('success', 'Buku berhasil dikembalikan');
        } else {
            Response::json('error', 'Gagal mengembalikan buku', null, 500);
        }
    }
 
    public function riwayatMahasiswa($mahasiswa_id) {
        if (!is_numeric($mahasiswa_id)) {
            Response::json('error', 'ID mahasiswa tidak valid', null, 400);
        }
        $riwayat = $this->peminjamanModel->getRiwayatByMahasiswa($mahasiswa_id);
        Response::json('success', 'Riwayat peminjaman mahasiswa', $riwayat);
    }
}
?>
