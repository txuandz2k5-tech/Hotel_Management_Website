<?php
namespace Api\Resources;

class EmployeeResource {
    public static function collection($employees) {
        return array_map([self::class, 'transform'], $employees);
    }
    public static function transform($employee) {
        if (!$employee) return null;
        return [
            'id' => $employee['MaNhanVien'] ?? null,
            'name' => trim(($employee['HoNhanVien'] ?? '') . ' ' . ($employee['TenNhanVien'] ?? '')),
            'email' => $employee['EmailNhanVien'] ?? null,
            'phone' => $employee['SoDienThoaiNV'] ?? null,
            'address' => $employee['DiaChi'] ?? null,
            'id_card' => $employee['CMND_CCCD'] ?? null,
            'department_id' => $employee['MaBoPhan'] ?? null,
            'position' => $employee['ChucDanhNV'] ?? null,
            'created_at' => $employee['NgayVaoLam'] ?? null,
            'updated_at' => $employee['updated_at'] ?? null,
        ];
    }
}
?>
