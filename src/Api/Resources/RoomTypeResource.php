<?php
namespace Api\Resources;

class RoomTypeResource {
    
    public static function collection($roomTypes) {
        return array_map([self::class, 'transform'], $roomTypes);
    }
    
    public static function transform($roomType) {
        if (!$roomType) return null;
        
        return [
            'id' => $roomType['id'] ?? null,
            'name' => $roomType['name'] ?? null,
            'description' => $roomType['description'] ?? null,
            'price_per_night' => $roomType['price_per_night'] ?? null,
            'max_capacity' => $roomType['max_capacity'] ?? null,
            'created_at' => $roomType['created_at'] ?? null,
            'updated_at' => $roomType['updated_at'] ?? null,
        ];
    }
}
?>
