<?php
namespace Api\Resources;

class ServiceOrderResource {
    
    public static function collection($orders) {
        return array_map([self::class, 'transform'], $orders);
    }
    
    public static function transform($order) {
        if (!$order) return null;
        
        return [
            'id' => $order['MaDichVuSuDung'] ?? null,
            'booking_id' => $order['MaDatPhong'] ?? null,
            'service_id' => $order['MaDichVu'] ?? null,
            'service_name' => $order['TenDichVu'] ?? null,
            'service_description' => $order['MoTaDichVu'] ?? null,
            'quantity' => (int)($order['SoLuong'] ?? 0),
            'unit_price' => (float)($order['DonGia'] ?? 0),
            'total_price' => (float)($order['ThanhTien'] ?? 0),
            'date_used' => $order['NgaySuDung'] ?? null,
        ];
    }
}
?>
