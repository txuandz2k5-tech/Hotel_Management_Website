<?php
namespace Shared\Models;

class Employee extends BaseModel {
    protected $table = 'hotels_employees';
    protected $primaryKey = 'MaNhanVien';

    /**
     * Tìm employees theo department
     */
    public function getByDepartment($deptId) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE department_id = {$deptId}";
            return $this->dbInstance->select($sql);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching employees: " . $e->getMessage());
        }
    }
}
?>
