<?php
class GuestProfileController extends Controller {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['guest_id'])) {
            header("Location: ?controller=AuthController&action=login");
            exit();
        }

        $myId = $_SESSION['guest_id'];
        $model = $this->model("GuestProfileModel");

        $guest = $model->getProfile($myId);
        $hasBooking = $model->hasBookings($myId);
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ho = $_POST['ho'];
            $ten = $_POST['ten'];
            $email = $_POST['email'];
            $sdt = $_POST['sdt'];
            $cmnd = isset($_POST['cmnd']) ? $_POST['cmnd'] : '';
            $diachi = $_POST['diachi'];
            
            $currentPassInput = $_POST['current_password'];
            $newPass = $_POST['password'];       
            $confirmPass = $_POST['confirm_password'];

            $currentData = $model->getProfile($myId);


            // --- VALIDATION ---
            
            if (empty($ho) || empty($ten) || empty($sdt)) {
                echo "<script>alert('Vui lòng điền đầy đủ Họ tên và SĐT!');</script>";
            }
            else if (!empty($email) && $model->checkEmailUnique($email, $myId)) {
                echo "<script>alert('Email này đã được sử dụng bởi tài khoản khác!');</script>";
            }
            else if ($model->checkPhoneUnique($sdt, $myId)) {
                echo "<script>alert('Số điện thoại này đã tồn tại!');</script>";
            }
            else if (!empty($newPass)) {
                if (empty($currentPassInput)) {
                    echo "<script>alert('Vui lòng nhập Mật khẩu hiện tại để xác thực thay đổi!');</script>";
                } 
                else if ($currentData['MatKhau'] !== $currentPassInput) {
                    echo "<script>alert('Mật khẩu hiện tại không đúng!');</script>";
                }
                else if ($newPass !== $confirmPass) {
                    echo "<script>alert('Mật khẩu xác nhận không khớp!');</script>";
                }
                else {
                    if ($model->updateProfile($myId, $ho, $ten, $email, $sdt, $cmnd, $diachi, $newPass)) {
                        echo "<script>alert('Đổi mật khẩu và cập nhật hồ sơ thành công!'); window.location.href='?controller=GuestProfileController&action=index';</script>";
                    } else {
                        echo "<script>alert('Lỗi hệ thống!');</script>";
                    }
                }
            }
            else {
                // Nếu CMND bị readonly (không gửi lên POST), ta giữ nguyên giá trị cũ trong DB
                if (empty($cmnd)) $cmnd = $currentData['CMND_CCCDKhachHang'];

                if ($model->updateProfile($myId, $ho, $ten, $email, $sdt, $cmnd, $diachi, null)) {
                    echo "<script>alert('Cập nhật hồ sơ thành công!'); window.location.href='?controller=GuestProfileController&action=index';</script>";
                } else {
                    echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại!');</script>";
                }
            }
        }

        

        ob_start();
        $this->view("Pages/GuestProfile", [
            "guest" => $guest, 
            "hasBooking" => $hasBooking
        ]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content]);
    }
}
?>