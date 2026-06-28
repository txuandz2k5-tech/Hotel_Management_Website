<?php
namespace Api\Resources;

class DepartmentResource {
    public static function collection($departments) {
        return array_map([self::class, 'transform'], $departments);
    }
    public static function transform($department) {
        if (!$department) return null;
        return [
            'id' => $department['id'] ?? null,
            'name' => $department['name'] ?? null,
            'description' => $department['description'] ?? null,
            'created_at' => $department['created_at'] ?? null,
            'updated_at' => $department['updated_at'] ?? null,
        ];
    }
}
?>
