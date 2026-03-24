<?php
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
// Autoload sederhana (tidak menggunakan composer, kita require manual)
require_once 'config/database.php';
require_once 'utils/Response.php';

// Koneksi database
try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    Response::json('error', 'Database connection failed: ' . $e->getMessage(), null, 500);
}
 
// Mendapatkan URL path dan method
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];
 
// Untuk PHP built-in server, path adalah REQUEST_URI langsung
$path = parse_url($request_uri, PHP_URL_PATH);
$path = ltrim($path, '/');
$path_parts = explode('/', $path);
 
// Method HTTP
$method = $_SERVER['REQUEST_METHOD'];
 
// Mengambil input JSON jika method POST/PUT
$input = [];
if ($method == 'POST' || $method == 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // jika bukan JSON, coba ambil dari $_POST atau $_PUT (tapi kita sederhanakan)
        $input = $_POST;
    }
}
 
// Routing sederhana
// Format endpoint: /api/buku, /api/buku/1, /api/mahasiswa, /api/peminjaman, dll.
try {
    if (isset($path_parts[0]) && $path_parts[0] == 'api') {
        $resource = isset($path_parts[1]) ? $path_parts[1] : '';
        $id = isset($path_parts[2]) ? $path_parts[2] : null;
        $subresource = isset($path_parts[3]) ? $path_parts[3] : null;
 
        // Load controller sesuai resource
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
 
            case 'mahasiswa':
                require_once 'controllers/MahasiswaController.php';
                $controller = new MahasiswaController($db);
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
 
            case 'peminjaman':
                require_once 'controllers/PeminjamanController.php';
                $controller = new PeminjamanController($db);
                if ($method == 'GET') {
                    if ($id && $subresource == 'riwayat') {
                        // Endpoint /api/peminjaman/{mahasiswa_id}/riwayat
                        $controller->riwayatMahasiswa($id);
                    } elseif ($id) {
                        $controller->getById($id);
                    } else {
                        $controller->getAll();
                    }
                } elseif ($method == 'POST') {
                    // Untuk peminjaman, bisa menggunakan endpoint /api/peminjaman/pinjam
                    if (isset($path_parts[2]) && $path_parts[2] == 'pinjam') {
                        $controller->pinjam($input);
                    } else {
                        Response::json('error', 'Endpoint tidak dikenal', null, 404);
                    }
                } elseif ($method == 'PUT') {
                    if ($id && isset($path_parts[2]) && $path_parts[2] == 'kembali') {
                        $controller->kembali($id);
                    } else {
                        Response::json('error', 'Endpoint tidak dikenal', null, 404);
                    }
                } else {
                    Response::json('error', 'Method tidak diizinkan', null, 405);
                }
                break;
 
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
