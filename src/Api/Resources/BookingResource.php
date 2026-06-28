<?php
namespace Api\Resources;
 
class BookingResource {
    
    /**
     * Transform single booking
     */
    public static function transform($booking) {
        if (!$booking) return null;
 
        return [
            'id' => $booking['MaDatPhong'] ?? null,
            'guest_id' => $booking['MaKhachHang'] ?? null,
            'room_id' => $booking['MaLoaiPhong'] ?? null,
            'check_in' => $booking['NgayNhanPhong'] ?? null,
            'check_out' => $booking['NgayTraPhong'] ?? null,
            'total_price' => $booking['SoTienDatPhong'] ?? 0,
            'status' => $booking['TrangThai'] ?? '',
            'note' => $booking['GhiChu'] ?? '',
            'created_at' => $booking['created_at'] ?? null,
            'updated_at' => $booking['updated_at'] ?? null,
        ];
    }
 
    /**
     * Transform collection of bookings
     */
    public static function collection($bookings) {
        if (!is_array($bookings)) return [];
        
        return array_map(function($booking) {
            return self::transform($booking);
        }, $bookings);
    }
}
?>