<?php
require_once 'models/MahasiswaModel.php';
 
class MahasiswaController {
    private $mahasiswaModel;
 
    public function __construct($db) {
        $this->mahasiswaModel = new MahasiswaModel($db);
    }
 
    public function getAll() {
        $data = $this->mahasiswaModel->getAll();
        Response::json('success', 'Data mahasiswa', $data);
    }
 
    public function getById($id) {
        if (!is_numeric($id)) {
            Response::json('error', 'ID tidak valid', null, 400);
        }
        $data = $this->mahasiswaModel->getById($id);
        if ($data) {
            Response::json('success', 'Detail mahasiswa', $data);
        } else {
            Response::json('error', 'Mahasiswa tidak ditemukan', null, 404);
        }
    }
 
    public function create($input) {
        if (empty($input['nim']) || empty($input['nama'])) {
            Response::json('error', 'NIM dan nama wajib diisi', null, 400);
        }
        $data = [
            'nim' => $input['nim'],
            'nama' => $input['nama'],
            'no_telp' => $input['no_telp'] ?? null,
            'alamat' => $input['alamat'] ?? null
        ];
        $id = $this->mahasiswaModel->create($data);
        if ($id) {
            Response::json('success', 'Mahasiswa berhasil ditambahkan', ['id' => $id], 201);
        } else {
            Response::json('error', 'Gagal menambahkan mahasiswa (mungkin NIM sudah ada)', null, 500);
        }
    }
 
    public function update($id, $input) {
        if (!is_numeric($id)) {
            Response::json('error', 'ID tidak valid', null, 400);
        }
        $existing = $this->mahasiswaModel->getById($id);
        if (!$existing) {
            Response::json('error', 'Mahasiswa tidak ditemukan', null, 404);
        }
        $data = [
            'nim' => $input['nim'] ?? $existing['nim'],
            'nama' => $input['nama'] ?? $existing['nama'],
            'no_telp' => $input['no_telp'] ?? $existing['no_telp'],
            'alamat' => $input['alamat'] ?? $existing['alamat']
        ];
        if ($this->mahasiswaModel->update($id, $data)) {
            Response::json('success', 'Mahasiswa berhasil diperbarui');
        } else {
            Response::json('error', 'Gagal memperbarui mahasiswa', null, 500);
        }
    }
 
    public function delete($id) {
        if (!is_numeric($id)) {
            Response::json('error', 'ID tidak valid', null, 400);
        }
        if ($this->mahasiswaModel->delete($id)) {
            Response::json('success', 'Mahasiswa berhasil dihapus');
        } else {
            Response::json('error', 'Gagal menghapus mahasiswa', null, 500);
        }
    }
}
?>
