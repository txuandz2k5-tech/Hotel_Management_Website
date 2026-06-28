<?php
namespace Shared\Models;

class ServiceOrder extends BaseModel {
    protected $table = 'hotelservice_servicesused';
    protected $primaryKey = 'MaDichVuSuDung';
    
    protected $fieldMapping = [
        'id' => 'MaDichVuSuDung',
        'booking_id' => 'MaDatPhong',
        'service_id' => 'MaDichVu',
        'quantity' => 'SoLuong',
        'unit_price' => 'DonGia',
        'total_price' => 'ThanhTien',
        'date_used' => 'NgaySuDung',
    ];

    public function mapFields($data) {
        $mapped = [];
        foreach ($data as $key => $value) {
            if (isset($this->fieldMapping[$key])) {
                $mapped[$this->fieldMapping[$key]] = $value;
            } else {
                $mapped[$key] = $value;
            }
        }
        return $mapped;
    }

    public function reverseMapFields($data) {
        $mapped = [];
        $reverseMapping = array_flip($this->fieldMapping);
        foreach ($data as $key => $value) {
            if (isset($reverseMapping[$key])) {
                $mapped[$reverseMapping[$key]] = $value;
            } else {
                $mapped[$key] = $value;
            }
        }
        return $mapped;
    }

    /**
     * Get all services for a specific booking with service details
     */
    public function getByBooking($bookingId) {
        $sql = "SELECT su.*, s.TenDichVu, s.MoTaDichVu 
                FROM {$this->table} su
                LEFT JOIN hotelservice_services s ON su.MaDichVu = s.MaDichVu
                WHERE su.MaDatPhong = ?
                ORDER BY su.NgaySuDung DESC";
        
        return $this->dbInstance->select($sql, [$bookingId]);
    }

    /**
     * Get all service orders with service details
     */
    public function getAllOrders() {
        $sql = "SELECT su.*, s.TenDichVu, s.MoTaDichVu
                FROM {$this->table} su
                LEFT JOIN hotelservice_services s ON su.MaDichVu = s.MaDichVu
                ORDER BY su.NgaySuDung DESC";

        return $this->dbInstance->select($sql);
    }

    /**
     * Create a new service order (add service to booking)
     */
    public function findWithDetails($serviceUsedId) {
        $sql = "SELECT su.*, s.TenDichVu, s.MoTaDichVu 
                FROM {$this->table} su
                LEFT JOIN hotelservice_services s ON su.MaDichVu = s.MaDichVu
                WHERE su.MaDichVuSuDung = ?";

        return $this->dbInstance->selectOne($sql, [$serviceUsedId]);
    }

    public function addServiceToBooking($bookingId, $serviceId, $quantity = 1) {
        try {
            // Validate booking_id is required
            if (!$bookingId) {
                throw new \Exception('Booking ID is required to link service order with customer');
            }

            // Get service details to get price
            $serviceSql = "SELECT ChiPhiDichVu FROM hotelservice_services WHERE MaDichVu = ?";
            $service = $this->dbInstance->selectOne($serviceSql, [$serviceId]);
            
            if (!$service) {
                return false;
            }
            
            $unitPrice = $service['ChiPhiDichVu'];
            $totalPrice = $quantity * $unitPrice;
            $dateUsed = date('Y-m-d H:i:s');
            
            // Insert - let AUTO_INCREMENT handle the ID
            $sql = "INSERT INTO {$this->table} 
                    (MaDatPhong, MaDichVu, SoLuong, DonGia, ThanhTien, NgaySuDung)
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $result = $this->dbInstance->execute($sql, [
                (int)$bookingId, 
                $serviceId, 
                $quantity, 
                $unitPrice, 
                $totalPrice,
                $dateUsed
            ]);
            
            if ($result) {
                // Get the auto-incremented ID
                $newId = $this->dbInstance->insertId();
                return $this->findWithDetails($newId);
            }
            return false;
        } catch (\Exception $e) {
            error_log('Error adding service to booking: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total cost of all services for a booking
     */
    public function getTotalServiceCost($bookingId) {
        $sql = "SELECT SUM(ThanhTien) as total FROM {$this->table} WHERE MaDatPhong = ?";
        $result = $this->dbInstance->selectOne($sql, [$bookingId]);
        return $result['total'] ?? 0;
    }

    /**
     * Remove service from booking
     */
    public function removeServiceFromBooking($serviceUsedId) {
        $sql = "DELETE FROM {$this->table} WHERE MaDichVuSuDung = ?";
        return $this->dbInstance->execute($sql, [$serviceUsedId]);
    }
}
?>
