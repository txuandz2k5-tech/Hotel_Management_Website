<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Shared\Auth\JWT;
use Shared\Models\Account;
use Shared\Models\Guest;

class AuthController extends BaseApiController {
    /**
     * Đăng nhập và trả về JWT token
     */
    public function login() {
        try {
            $data = $this->request->all();

            if (!$this->validate($data, [
                'username' => ['required'],
                'password' => ['required']
            ])) {
                return;
            }

            $accountModel = new Account();
            $account = $accountModel->findByUsername($data['username']);
            $accountType = 'admin';

            if (!$account) {
                $account = $accountModel->findLoginByUsername($data['username']);
                $accountType = $account ? 'staff' : null;
            }

            if (!$account) {
                $guestModel = new Guest();
                $account = $guestModel->findByPhone($data['username']);
                $accountType = $account ? 'guest' : null;
            }

            if (!$account) {
                $this->response->unauthorized('Tên đăng nhập hoặc mật khẩu không đúng');
                return;
            }

            $isValidPassword = password_verify($data['password'], $account['MatKhau']) || $data['password'] === $account['MatKhau'];
            if (!$isValidPassword) {
                $this->response->unauthorized('Tên đăng nhập hoặc mật khẩu không đúng');
                return;
            }

            $payload = [];
            $user = [];

            if ($accountType === 'admin') {
                $payload = [
                    'id' => $account['MaAdmin'],
                    'username' => $account['TenDangNhap'],
                    'role' => 'admin'
                ];
                $user = [
                    'id' => $account['MaAdmin'],
                    'username' => $account['TenDangNhap'],
                    'role' => 'admin'
                ];
            } elseif ($accountType === 'staff') {
                $payload = [
                    'id' => $account['MaDangNhap'],
                    'username' => $account['TenDangNhap'],
                    'role' => 'staff'
                ];
                $user = [
                    'id' => $account['MaDangNhap'],
                    'username' => $account['TenDangNhap'],
                    'role' => 'staff'
                ];
            } else {
                $payload = [
                    'id' => $account['MaKhachHang'],
                    'username' => $account['SoDienThoaiKhachHang'],
                    'role' => 'guest'
                ];
                $user = [
                    'id' => $account['MaKhachHang'],
                    'username' => $account['SoDienThoaiKhachHang'],
                    'role' => 'guest',
                    'name' => trim(($account['HoKhachHang'] ?? '') . ' ' . ($account['TenKhachHang'] ?? ''))
                ];
            }

            $token = JWT::encode($payload);

            $this->response->success([
                'token' => $token,
                'user' => $user
            ], 'Đăng nhập thành công');

        } catch (\Exception $e) {
            $this->logError('Login error', $e);
            $this->response->error('Lỗi đăng nhập: ' . $e->getMessage());
        }
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function me() {
        try {
            $user = JWT::validateToken();
            if (!$user) {
                $this->response->unauthorized('Token không hợp lệ');
                return;
            }

            $accountType = $user['role'] ?? 'admin';
            $account = null;

            if ($accountType === 'admin') {
                $accountModel = new Account();
                $account = $accountModel->find($user['id']);
                $responseUser = [
                    'id' => $account['MaAdmin'],
                    'username' => $account['TenDangNhap'],
                    'role' => 'admin'
                ];
            } elseif ($accountType === 'staff') {
                $accountModel = new Account();
                $account = $accountModel->findLoginById($user['id']);
                $responseUser = [
                    'id' => $account['MaDangNhap'],
                    'username' => $account['TenDangNhap'],
                    'role' => 'staff'
                ];
            } else {
                $guestModel = new Guest();
                $account = $guestModel->find($user['id']);
                $responseUser = [
                    'id' => $account['MaKhachHang'],
                    'username' => $account['SoDienThoaiKhachHang'],
                    'role' => 'guest',
                    'name' => trim(($account['HoKhachHang'] ?? '') . ' ' . ($account['TenKhachHang'] ?? ''))
                ];
            }

            if (!$account) {
                $this->response->notFound('Không tìm thấy tài khoản');
                return;
            }

            $this->response->success(['user' => $responseUser]);

        } catch (\Exception $e) {
            $this->logError('Get user info error', $e);
            $this->response->error('Lỗi lấy thông tin user: ' . $e->getMessage());
        }
    }

    /**
     * Đăng xuất và blacklist token
     */
    public function logout() {
        try {
            $token = JWT::getTokenFromHeader();
            if (!$token) {
                $this->response->unauthorized('Token không hợp lệ hoặc không tìm thấy');
                return;
            }

            if (!JWT::blacklistToken($token)) {
                $this->response->unauthorized('Không thể đăng xuất với token này');
                return;
            }

            $this->response->success([], 'Đăng xuất thành công');
        } catch (\Exception $e) {
            $this->logError('Logout error', $e);
            $this->response->error('Lỗi đăng xuất: ' . $e->getMessage());
        }
    }
}