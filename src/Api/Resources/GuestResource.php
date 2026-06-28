<?php
namespace Api\Resources;

class GuestResource {
    
    /**
     * Transform collection
     */
    public static function collection($guests) {
        return array_map([self::class, 'transform'], $guests);
    }
    
    /**
     * Transform single guest
     */
    public static function transform($guest) {
        if (!$guest) return null;
        
        return [
            'id' => $guest['MaKhachHang'] ?? null,
            'name' => trim(($guest['HoKhachHang'] ?? '') . ' ' . ($guest['TenKhachHang'] ?? '')) ?: null,
            'email' => $guest['EmailKhachHang'] ?? null,
            'phone' => $guest['SoDienThoaiKhachHang'] ?? null,
            'address' => $guest['DiaChi'] ?? null,
            'id_card' => $guest['CMND_CCCDKhachHang'] ?? null,
            'created_at' => $guest['NgayTao'] ?? null,
            'updated_at' => $guest['NgayTao'] ?? null,
        ];
    }
}
?>
