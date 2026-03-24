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
