<?php
namespace Shared\Models;

class Room extends BaseModel {
    protected $table = 'rooms_room';
    protected $primaryKey = 'MaPhong';

    /**
     * Tìm rooms theo type
     */
    public function getByRoomType($typeId, $page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM {$this->table} WHERE room_type_id = {$typeId} LIMIT {$perPage} OFFSET {$offset}";
            return $this->dbInstance->select($sql);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching rooms: " . $e->getMessage());
        }
    }

    /**
     * Tìm rooms theo status
     */
    public function getByStatus($status) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE status = '{$status}'";
            return $this->dbInstance->select($sql);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching rooms by status: " . $e->getMessage());
        }
    }
}
?>
