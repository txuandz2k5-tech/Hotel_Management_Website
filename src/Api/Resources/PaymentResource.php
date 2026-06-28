<?php
namespace Api\Resources;

class PaymentResource {
    public static function collection($payments) {
        return array_map([self::class, 'transform'], $payments);
    }
    public static function transform($payment) {
        if (!$payment) return null;
        return [
            'id' => $payment['id'] ?? null,
            'booking_id' => $payment['booking_id'] ?? null,
            'amount' => $payment['amount'] ?? null,
            'payment_method' => $payment['payment_method'] ?? null,
            'status' => $payment['status'] ?? null,
            'paid_at' => $payment['paid_at'] ?? null,
            'created_at' => $payment['created_at'] ?? null,
            'updated_at' => $payment['updated_at'] ?? null,
        ];
    }
}
?>
