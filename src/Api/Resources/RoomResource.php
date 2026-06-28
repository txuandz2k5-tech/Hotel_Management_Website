<?php
namespace Api\Resources;

class RoomResource {
    
    public static function collection($rooms) {
        return array_map([self::class, 'transform'], $rooms);
    }
    
    public static function transform($room) {
        if (!$room) return null;
        
        return [
            'id' => $room['MaPhong'] ?? null,
            'room_number' => $room['SoPhong'] ?? null,
            'room_type_id' => $room['MaLoaiPhong'] ?? null,
            'availability' => $room['KhaDung'] ?? null,
            'created_at' => $room['created_at'] ?? null,
            'updated_at' => $room['updated_at'] ?? null,
        ];
    }
}
?>
