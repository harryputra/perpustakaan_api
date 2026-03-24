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
 
    public function getById($id) {
        if (!is_numeric($id)) {
            Response::json('error', 'ID tidak valid', null, 400);
        }
        $data = $this->bukuModel->getById($id);
        if ($data) {
            Response::json('success', 'Detail buku', $data);
        } else {
            Response::json('error', 'Buku tidak ditemukan', null, 404);
        }
    }
 
    public function create($input) {
        // Validasi input sederhana
        if (empty($input['judul']) || empty($input['pengarang']) || empty($input['penerbit'])) {
            Response::json('error', 'Judul, pengarang, dan penerbit wajib diisi', null, 400);
        }
        $stok = isset($input['stok']) ? (int)$input['stok'] : 0;
        $data = [
            'judul' => $input['judul'],
            'pengarang' => $input['pengarang'],
            'penerbit' => $input['penerbit'],
            'tahun_terbit' => $input['tahun_terbit'] ?? null,
            'stok' => $stok
        ];
        $id = $this->bukuModel->create($data);
        if ($id) {
            Response::json('success', 'Buku berhasil ditambahkan', ['id' => $id], 201);
        } else {
            Response::json('error', 'Gagal menambahkan buku', null, 500);
        }
    }
 
    public function update($id, $input) {
        if (!is_numeric($id)) {
            Response::json('error', 'ID tidak valid', null, 400);
        }
        $existing = $this->bukuModel->getById($id);
        if (!$existing) {
            Response::json('error', 'Buku tidak ditemukan', null, 404);
        }
        $data = [
            'judul' => $input['judul'] ?? $existing['judul'],
            'pengarang' => $input['pengarang'] ?? $existing['pengarang'],
            'penerbit' => $input['penerbit'] ?? $existing['penerbit'],
            'tahun_terbit' => $input['tahun_terbit'] ?? $existing['tahun_terbit'],
            'stok' => isset($input['stok']) ? (int)$input['stok'] : $existing['stok']
        ];
        if ($this->bukuModel->update($id, $data)) {
            Response::json('success', 'Buku berhasil diperbarui');
        } else {
            Response::json('error', 'Gagal memperbarui buku', null, 500);
        }
    }
 
    public function delete($id) {
        if (!is_numeric($id)) {
            Response::json('error', 'ID tidak valid', null, 400);
        }
        if ($this->bukuModel->delete($id)) {
            Response::json('success', 'Buku berhasil dihapus');
        } else {
            Response::json('error', 'Gagal menghapus buku', null, 500);
        }
    }
}
?>
