<?php
namespace Shared\Models;
 
class Booking extends BaseModel {
    protected $table = 'bookings_booking';
    protected $primaryKey = 'MaDatPhong';

    /**
     * Tìm bookings của guest
     */
    public function getByGuest($guestId, $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM {$this->table} WHERE MaKhachHang = ? LIMIT ? OFFSET ?";
            return $this->dbInstance->select($sql, [(int)$guestId, (int)$perPage, (int)$offset]);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching bookings by guest: " . $e->getMessage());
        }
    }

    public function paginateByGuest($guestId, $page = 1, $perPage = 10) {
        return $this->getByGuest($guestId, $page, $perPage);
    }

    /**
     * Tìm bookings theo status (và optional guest)
     */
    public function getByStatus($status, $page = 1, $perPage = 10, $guestId = null) {
        try {
            $offset = ($page - 1) * $perPage;
            if ($guestId) {
                $sql = "SELECT * FROM {$this->table} WHERE TrangThai = ? AND MaKhachHang = ? LIMIT ? OFFSET ?";
                return $this->dbInstance->select($sql, [$status, (int)$guestId, (int)$perPage, (int)$offset]);
            }
            $sql = "SELECT * FROM {$this->table} WHERE TrangThai = ? LIMIT ? OFFSET ?";
            return $this->dbInstance->select($sql, [$status, (int)$perPage, (int)$offset]);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching bookings by status: " . $e->getMessage());
        }
    }

    /**
     * Tìm bookings theo room type
     */
    public function getByRoomType($roomType, $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM {$this->table} WHERE MaLoaiPhong = ? LIMIT ? OFFSET ?";
            return $this->dbInstance->select($sql, [$roomType, (int)$perPage, (int)$offset]);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching bookings by room type: " . $e->getMessage());
        }
    }

    /**
     * Count bookings theo status (để hỗ trợ pagination)
     */
    public function countByStatus($status, $guestId = null) {
        try {
            if ($guestId) {
                $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE TrangThai = ? AND MaKhachHang = ?";
                $result = $this->dbInstance->selectOne($sql, [$status, (int)$guestId]);
            } else {
                $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE TrangThai = ?";
                $result = $this->dbInstance->selectOne($sql, [$status]);
            }
            return $result['total'] ?? 0;
        } catch (\Exception $e) {
            throw new \Exception("Error counting bookings: " . $e->getMessage());
        }
    }

    public function countByGuest($guestId) {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE MaKhachHang = ?";
            $result = $this->dbInstance->selectOne($sql, [(int)$guestId]);
            return $result['total'] ?? 0;
        } catch (\Exception $e) {
            throw new \Exception("Error counting bookings by guest: " . $e->getMessage());
        }
    }
}
?>