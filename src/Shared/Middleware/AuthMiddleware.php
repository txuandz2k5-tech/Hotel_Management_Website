<?php
namespace Shared\Middleware;

use Shared\Auth\JWT;
use Shared\Http\Response;

class AuthMiddleware {
    private $response;

    public function __construct() {
        $this->response = new Response();
    }

    /**
     * Kiểm tra authentication
     */
    public function handle() {
        $user = JWT::validateToken();
        if (!$user) {
            $this->response->unauthorized('Token không hợp lệ hoặc đã hết hạn');
            return false;
        }

        // Lưu thông tin user vào global để controller sử dụng
        $_SESSION['api_user'] = $user;
        return true;
    }

    /**
     * Kiểm tra role
     */
    public function checkRole($requiredRole) {
        if (!isset($_SESSION['api_user'])) {
            $this->response->unauthorized('Chưa đăng nhập');
            return false;
        }

        $userRole = $_SESSION['api_user']['role'];
        if ($userRole !== $requiredRole && $userRole !== 'admin') {
            $this->response->forbidden('Không có quyền truy cập');
            return false;
        }

        return true;
    }
}