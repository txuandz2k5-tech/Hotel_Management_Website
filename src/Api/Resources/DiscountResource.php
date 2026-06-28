<?php
namespace Api\Resources;

class DiscountResource {
    public static function collection($discounts) {
        return array_map([self::class, 'transform'], $discounts);
    }
    public static function transform($discount) {
        if (!$discount) return null;
        return [
            'id' => $discount['id'] ?? null,
            'code' => $discount['code'] ?? null,
            'description' => $discount['description'] ?? null,
            'discount_percent' => $discount['discount_percent'] ?? null,
            'valid_from' => $discount['valid_from'] ?? null,
            'valid_to' => $discount['valid_to'] ?? null,
            'created_at' => $discount['created_at'] ?? null,
            'updated_at' => $discount['updated_at'] ?? null,
        ];
    }
}
?>
