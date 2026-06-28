<?php
namespace Api\Resources;

class AccountResource {
    public static function collection($accounts) {
        return array_map([self::class, 'transform'], $accounts);
    }
    public static function transform($account) {
        if (!$account) return null;
        return [
            'id' => $account['id'] ?? null,
            'name' => $account['name'] ?? null,
            'email' => $account['email'] ?? null,
            'phone' => $account['phone'] ?? null,
            'role' => $account['role'] ?? null,
            'created_at' => $account['created_at'] ?? null,
            'updated_at' => $account['updated_at'] ?? null,
        ];
    }
}
?>
