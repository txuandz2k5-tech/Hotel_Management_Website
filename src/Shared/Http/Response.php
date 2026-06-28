<?php
namespace Shared\Http;

class Response {
    private $status = 200;
    private $data = [];

    public function __construct() {
        $this->setHeaders();
    }

    /**
     * Trả về response thành công
     */
    public function json($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    /**
     * Trả về danh sách dữ liệu có pagination
     */
    public function paginate($items, $total, $page, $perPage, $status = 200) {
        return $this->json([
            'status' => 'success',
            'data' => $items,
            'meta' => [
                'total' => $total,
                'per_page' => $perPage,
                'page' => (int)$page,
                'last_page' => ceil($total / $perPage),
            ]
        ], $status);
    }

    /**
     * Trả về lỗi
     */
    public function error($message, $status = 400, $errors = null) {
        return $this->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    /**
     * Trả về 404
     */
    public function notFound($message = 'Resource not found') {
        return $this->error($message, 404);
    }

    /**
     * Trả về 401 Unauthorized
     */
    public function unauthorized($message = 'Unauthorized') {
        return $this->error($message, 401);
    }

    /**
     * Trả về 403 Forbidden
     */
    public function forbidden($message = 'Forbidden') {
        return $this->error($message, 403);
    }

    /**
     * Trả về 422 Validation failed
     */
    public function validationError($errors) {
        return $this->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $errors,
        ], 422);
    }

    /**
     * Trả về 500 Internal server error
     */
    public function internalError($message = 'Internal server error') {
        return $this->error($message, 500);
    }

    /**
     * Trả về created response
     */
    public function created($data, $message = 'Resource created successfully') {
        return $this->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], 201);
    }

    /**
     * Trả về success response
     */
    public function success($data, $message = 'Success') {
        return $this->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], 200);
    }

    // ============ Private Methods ============

    private function setHeaders() {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit();
        }
    }
}
?>
