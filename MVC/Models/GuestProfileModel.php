<?php
class GuestProfileModel extends connectDB {
    
    public function getProfile($id) {
        $sql = "SELECT * FROM hotels_guests WHERE MaKhachHang = '$id'";
        return $this->selectOne($sql);
    }

    public function hasBookings($id) {
        $sql = "SELECT COUNT(*) as total FROM bookings_booking WHERE MaKhachHang = '$id'";
        $result = $this->selectOne($sql);
        return ($result['total'] > 0);
    }

    public function checkPhoneUnique($phone, $myId) {
        $sql = "SELECT * FROM hotels_guests WHERE SoDienThoaiKhachHang = '$phone' AND MaKhachHang != '$myId'";
        $result = $this->select($sql);
        return !empty($result); 
    }

    public function checkEmailUnique($email, $myId) {
        $sql = "SELECT * FROM hotels_guests WHERE EmailKhachHang = '$email' AND MaKhachHang != '$myId'";
        $result = $this->select($sql);
        return !empty($result); 
    }


    public function updateProfile($id, $ho, $ten, $email, $sdt, $cmnd, $diachi, $newPassword = null) {
        if (empty($newPassword)) {
            $sql = "UPDATE hotels_guests SET 
                    HoKhachHang = '$ho',
                    TenKhachHang = '$ten',
                    EmailKhachHang = '$email',
                    SoDienThoaiKhachHang = '$sdt',
                    CMND_CCCDKhachHang = '$cmnd',
                    DiaChi = '$diachi'
                    WHERE MaKhachHang = '$id'";
        } else {
            $sql = "UPDATE hotels_guests SET 
                    HoKhachHang = '$ho',
                    TenKhachHang = '$ten',
                    EmailKhachHang = '$email',
                    SoDienThoaiKhachHang = '$sdt',
                    CMND_CCCDKhachHang = '$cmnd',
                    DiaChi = '$diachi',
                    MatKhau = '$newPassword'
                    WHERE MaKhachHang = '$id'";
        }
        return $this->execute($sql);
    }
}
?>