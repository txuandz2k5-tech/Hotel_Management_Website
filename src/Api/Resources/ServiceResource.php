<?php
namespace Api\Resources;

class ServiceResource {
    
    public static function collection($services) {
        return array_map([self::class, 'transform'], $services);
    }
    
    public static function transform($service) {
        if (!$service) return null;
        
        return [
            'id' => $service['MaDichVu'] ?? null,
            'name' => $service['TenDichVu'] ?? null,
            'description' => $service['MoTaDichVu'] ?? null,
            'price' => $service['ChiPhiDichVu'] ?? null,
            'created_at' => $service['created_at'] ?? null,
            'updated_at' => $service['updated_at'] ?? null,
        ];
    }
}
?>
