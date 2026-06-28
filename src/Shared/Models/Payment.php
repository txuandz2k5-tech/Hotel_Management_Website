<?php
namespace Shared\Models;

class Payment extends BaseModel {
    protected $table = 'payments_payment';

    /**
     * Tìm payments theo booking
     */
    public function getByBookingId($bookingId) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE booking_id = {$bookingId}";
            return $this->dbInstance->select($sql);
        } catch (\Exception $e) {
            throw new \Exception("Error fetching payments: " . $e->getMessage());
        }
    }
}
?>
