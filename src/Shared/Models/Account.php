<?php
namespace Shared\Models;

class Account extends BaseModel {
    protected $table = 'authentication_admin';
    protected $primaryKey = 'MaAdmin';

    /**
     * Tìm account theo username
     */
    public function findByUsername($username) {
        return $this->firstWhere('TenDangNhap', $username);
    }

    /**
     * Tìm account theo email
     */
    public function findByEmail($email) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
            return $this->dbInstance->selectOne($sql, [$email]);
        } catch (\Exception $e) {
            throw new \Exception("Error finding account: " . $e->getMessage());
        }
    }

    /**
     * Tìm account nhân viên theo username
     */
    public function findLoginByUsername($username) {
        try {
            $sql = "SELECT * FROM authentication_login WHERE TenDangNhap = ? LIMIT 1";
            return $this->dbInstance->selectOne($sql, [$username]);
        } catch (\Exception $e) {
            throw new \Exception("Error finding login account: " . $e->getMessage());
        }
    }

    /**
     * Tìm account nhân viên theo ID
     */
    public function findLoginById($id) {
        try {
            $sql = "SELECT * FROM authentication_login WHERE MaDangNhap = ? LIMIT 1";
            return $this->dbInstance->selectOne($sql, [$id]);
        } catch (\Exception $e) {
            throw new \Exception("Error finding login account by ID: " . $e->getMessage());
        }
    }
}
?>
