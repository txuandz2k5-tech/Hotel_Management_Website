<?php
namespace Shared\Models;

class Guest extends BaseModel {
    protected $table = 'hotels_guests';
    protected $primaryKey = 'MaKhachHang';

    /**
     * Tìm guests theo tên (search)
     */
    public function search($searchTerm, $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            $searchParam = "%{$searchTerm}%";
            $sql = "SELECT * FROM {$this->table} 
                    WHERE TenKhachHang LIKE '{$searchParam}' 
                    OR EmailKhachHang LIKE '{$searchParam}' 
                    OR SoDienThoaiKhachHang LIKE '{$searchParam}'
                    LIMIT {$perPage} OFFSET {$offset}";
            return $this->dbInstance->select($sql);
        } catch (\Exception $e) {
            throw new \Exception("Error searching guests: " . $e->getMessage());
        }
    }

    /**
     * Đếm guests từ search term
     */
    public function searchCount($searchTerm) {
        try {
            $searchParam = "%{$searchTerm}%";
            $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                    WHERE TenKhachHang LIKE '{$searchParam}' 
                    OR EmailKhachHang LIKE '{$searchParam}' 
                    OR SoDienThoaiKhachHang LIKE '{$searchParam}'";
            $result = $this->dbInstance->selectOne($sql);
            return $result['count'] ?? 0;
        } catch (\Exception $e) {
            throw new \Exception("Error counting search: " . $e->getMessage());
        }
    }

    /**
     * Tìm guest theo email
     */
    public function findByEmail($email) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE EmailKhachHang = ? LIMIT 1";
            return $this->dbInstance->selectOne($sql, [$email]);
        } catch (\Exception $e) {
            throw new \Exception("Error finding guest by email: " . $e->getMessage());
        }
    }

    /**
     * Tìm guest theo số điện thoại
     */
    public function findByPhone($phone) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE SoDienThoaiKhachHang = ? LIMIT 1";
            return $this->dbInstance->selectOne($sql, [$phone]);
        } catch (\Exception $e) {
            throw new \Exception("Error finding guest by phone: " . $e->getMessage());
        }
    }

    /**
     * Map API field names to database column names
     */
    protected function mapFields($data) {
        $mappedData = [];
        $fieldMap = [
            'name' => 'TenKhachHang',
            'email' => 'EmailKhachHang',
            'phone' => 'SoDienThoaiKhachHang',
            'address' => 'DiaChi',
            'id_card' => 'CMND_CCCDKhachHang',
            'password' => 'MatKhau',
        ];

        foreach ($data as $key => $value) {
            $dbColumn = $fieldMap[$key] ?? $key;
            $mappedData[$dbColumn] = $value;
        }

        return $mappedData;
    }

    /**
     * Override create to map fields from API format to database format
     */
    public function create($data) {
        $mappedData = $this->mapFields($data);
        return parent::create($mappedData);
    }

    /**
     * Override update to map fields from API format to database format
     */
    public function update($id, $data) {
        $mappedData = $this->mapFields($data);
        return parent::update($id, $mappedData);
    }
}
?>
