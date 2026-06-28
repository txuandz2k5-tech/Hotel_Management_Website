<?php
class GuestController extends controller {
    
    public function register() {
        ob_start();
        $this->view("Pages/GuestRegister");
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content]);
    }
    
    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model("GuestModel");
            
            $data = [
                'TenKhachHang' => $_POST['ten'],
                'HoKhachHang' => $_POST['ho'],
                'EmailKhachHang' => $_POST['email'],
                'SoDienThoaiKhachHang' => $_POST['sdt'],
                'CMND_CCCDKhachHang' => $_POST['cmnd'],
                'DiaChi' => $_POST['diachi'],
                'MatKhau' => $_POST['password']
            ];
            
            if ($_POST['password'] !== $_POST['confirm_password']) {
                echo "<script>alert('Mật khẩu xác nhận không khớp!'); window.history.back();</script>";
                return;
            }
            
            if ($model->checkPhoneExists($data['SoDienThoaiKhachHang'])) {
                echo "<script>alert('Số điện thoại đã được đăng ký!'); window.history.back();</script>";
                return;
            }
            
            if ($model->createGuest($data)) {
                echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.'); window.location.href='?controller=AuthController&action=login';</script>";
            } else {
                echo "<script>alert('Đăng ký thất bại!'); window.history.back();</script>";
            }
        }
    }
    
    public function home() {
        session_start();
        $this->requireRole(['customer']);

        if (!isset($_SESSION['guest_id'])) {
            header("Location: ?controller=AuthController&action=login");
            exit();
        }
        
        $model = $this->model("GuestModel");
        $roomTypeModel = $this->model("RoomTypeModel");
        
        $guest = $model->getGuestById($_SESSION['guest_id']);
        $roomTypes = $roomTypeModel->getAllWithAvailability();
        
        ob_start();
        $this->view("Pages/GuestHome", [
            "guest" => $guest,
            "roomTypes" => $roomTypes
        ]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content]);
    }
    

    public function index() {
        $model = $this->model("GuestModel");
        
        // Xử lý tìm kiếm
        if (isset($_POST['search'])) {
            $guests = $model->search($_POST['keyword']);
        } else {
            $guests = $model->getAll();
        }
        
        ob_start();
        $this->view("Pages/GuestList", ["guests" => $guests]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content, "page_tab" => "guest"]);
    }

    public function myBookings() {
        session_start();
        $this->requireRole(['customer']);

        if (!isset($_SESSION['guest_id'])) {
            header("Location: ?controller=AuthController&action=login");
            exit();
        }
        
        $bookingModel = $this->model("BookingModel");
        $serviceModel = $this->model("ServiceModel");        
        // Lấy tất cả booking của khách này
        $db = new connectDB();
        $sql = "SELECT b.*, 
                (SELECT GROUP_CONCAT(r.SoPhong SEPARATOR ', ') 
                FROM rooms_roombooked rb 
                JOIN rooms_room r ON rb.MaPhong = r.MaPhong 
                WHERE rb.MaDatPhong = b.MaDatPhong) as SoPhong
                FROM bookings_booking b
                WHERE b.MaKhachHang = '{$_SESSION['guest_id']}'
                ORDER BY b.NgayTao DESC";
        $bookings = $bookingModel->getByGuestId($_SESSION['guest_id']);
        
        ob_start();
        $this->view("Pages/GuestMyBookings", ["bookings" => $bookings]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content]);
    }

    public function orderService() {
        session_start();
        $this->requireRole(['customer']);

        if (!isset($_SESSION['guest_id'])) {
            header("Location: ?controller=AuthController&action=login");
            exit();
        }
        
        if (!isset($_GET['booking_id'])) {
            echo "<script>alert('Không tìm thấy booking!'); window.history.back();</script>";
            return;
        }
        
        $bookingModel = $this->model("BookingModel");
        $serviceModel = $this->model("ServiceModel");
        $guestModel = $this->model("GuestModel");
        
        $maDatPhong = $_GET['booking_id'];
        $booking = $bookingModel->getById($maDatPhong);
        
        // Kiểm tra quyền
        if ($booking['MaKhachHang'] != $_SESSION['guest_id']) {
            echo "<script>alert('Bạn không có quyền truy cập!'); window.history.back();</script>";
            return;
        }
        
        // Lấy thông tin khách và dịch vụ
        $guest = $guestModel->getGuestById($_SESSION['guest_id']);
        $services = $serviceModel->getAll();
        $usedServices = $serviceModel->getServicesByBooking($maDatPhong);
        
        ob_start();
        $this->view("Pages/GuestServiceOrder", [
            "booking" => array_merge($booking, $guest),
            "services" => $services,
            "usedServices" => $usedServices
        ]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content]);
    }

    
    public function viewMyServices() {
        session_start();
        $this->requireRole(['customer']);

        if (!isset($_SESSION['guest_id']) || !isset($_GET['booking_id'])) {
            header("Location: ?controller=GuestController&action=home");
            exit();
        }
        
        $bookingModel = $this->model("BookingModel");
        $serviceModel = $this->model("ServiceModel");
        
        $maDatPhong = $_GET['booking_id'];
        $booking = $bookingModel->getById($maDatPhong);
        
        if ($booking['MaKhachHang'] != $_SESSION['guest_id']) {
            echo "<script>alert('Bạn không có quyền truy cập!'); window.history.back();</script>";
            return;
        }
        
        $services = $serviceModel->getServicesByBooking($maDatPhong);
        $totalServiceCost = $serviceModel->getTotalServiceCost($maDatPhong);
        
        ob_start();
        $this->view("Pages/GuestViewServices", [
            "booking" => $booking,
            "services" => $services,
            "totalCost" => $totalServiceCost
        ]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content]);
    }
    


    public function saveGuest() {
        
        $this->requireRole(['admin', 'staff']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $guestModel = $this->model("GuestModel");
            
            $id = $_POST['ma_khach_hang'] ?? ''; //Nếu có ID là Sửa, không có là Thêm
            $isEdit = !empty($id);
            
            $data = [
                'HoKhachHang' => $_POST['ho'],
                'TenKhachHang' => $_POST['ten'],
                'SoDienThoaiKhachHang' => $_POST['sdt'],
                'EmailKhachHang' => $_POST['email'],
                'CMND_CCCDKhachHang' => $_POST['cmnd'],
                'DiaChi' => $_POST['diachi']
            ];

            if (empty($data['SoDienThoaiKhachHang']) || empty($data['TenKhachHang'])) {
                echo "<script>alert('Vui lòng điền đủ Họ tên và SĐT!'); window.history.back();</script>";
                return;
            }

            if ($isEdit) {
                if ($guestModel->checkPhoneUpdate($data['SoDienThoaiKhachHang'], $id)) {
                    echo "<script>alert('Số điện thoại này đã thuộc về khách hàng khác!'); window.history.back();</script>";
                    return;
                }
                
                if ($guestModel->update($id, $data)) {
                    echo "<script>alert('Cập nhật thông tin thành công!'); window.location.href='?controller=GuestController&action=index';</script>";
                } else {
                    echo "<script>alert('Lỗi khi cập nhật!'); window.history.back();</script>";
                }

            } else {
                if ($guestModel->checkPhoneExists($data['SoDienThoaiKhachHang'])) {
                    echo "<script>alert('Số điện thoại này đã được đăng ký!'); window.history.back();</script>";
                    return;
                }

                $data['MatKhau'] = $data['SoDienThoaiKhachHang']; 

                if ($guestModel->createGuest($data)) {
                    echo "<script>alert('Thêm khách hàng thành công! Mật khẩu mặc định là Số điện thoại.'); window.location.href='?controller=GuestController&action=index';</script>";
                } else {
                    echo "<script>alert('Thêm thất bại!'); window.history.back();</script>";
                }
            }
        }
    }

    public function deleteGuest() {
        $this->requireRole(['admin', 'staff']);

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $bookingModel = $this->model("BookingModel");
            $guestModel = $this->model("GuestModel");

            if ($bookingModel->checkBookingExist($id)) {
                echo "<script>
                    alert('KHÔNG THỂ XÓA! Khách hàng này đã có lịch sử đặt phòng. Việc xóa sẽ làm mất dữ liệu doanh thu.'); 
                    window.location.href='?controller=GuestController&action=index';
                </script>";
            } else {
                if ($guestModel->delete($id)) {
                    echo "<script>alert('Đã xóa khách hàng!'); window.location.href='?controller=GuestController&action=index';</script>";
                } else {
                    echo "<script>alert('Lỗi khi xóa!'); window.location.href='?controller=GuestController&action=index';</script>";
                }
            }
        }
    }

    public function exportExcel() {
        $this->requireRole(['admin', 'staff']);

        $model = $this->model("GuestModel");
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        
        if (!empty($keyword)) {
            $guests = $model->search($keyword);
        } else {
            $guests = $model->getAll();
        }

        $libPath = dirname(__DIR__, 2) . "/Public/Classes/PHPExcel.php";
        
        if (!file_exists($libPath)) {
            die("Lỗi: Không tìm thấy thư viện PHPExcel tại: " . $libPath);
        }
        require_once $libPath;

        // 3. Khởi tạo đối tượng Excel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        $sheet->setCellValue('A1', 'Mã KH');
        $sheet->setCellValue('B1', 'Họ Đệm');
        $sheet->setCellValue('C1', 'Tên');
        $sheet->setCellValue('D1', 'Số điện thoại');
        $sheet->setCellValue('E1', 'Email');
        $sheet->setCellValue('F1', 'CMND/CCCD');
        $sheet->setCellValue('G1', 'Địa chỉ');
        $sheet->setCellValue('H1', 'Ngày tạo');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '2ecc71']]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        $row = 2; 
        foreach ($guests as $data) {
            $sheet->setCellValue('A' . $row, $data['MaKhachHang']);
            $sheet->setCellValue('B' . $row, $data['HoKhachHang']);
            $sheet->setCellValue('C' . $row, $data['TenKhachHang']);
            
            $sheet->setCellValueExplicit('D' . $row, $data['SoDienThoaiKhachHang'], PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('E' . $row, $data['EmailKhachHang']);
            $sheet->setCellValueExplicit('F' . $row, $data['CMND_CCCDKhachHang'], PHPExcel_Cell_DataType::TYPE_STRING);
            
            $sheet->setCellValue('G' . $row, $data['DiaChi']);
            $sheet->setCellValue('H' . $row, date('d/m/Y', strtotime($data['NgayTao'])));
            
            $row++;
        }

        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = "DS_KhachHang_" . date('Y-m-d_H-i') . ".xls"; 
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function importExcel() {
        $this->requireRole(['admin', 'staff']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
            $file = $_FILES['excel_file']['tmp_name'];

            $libPath = dirname(__DIR__, 2) . "/Public/Classes/PHPExcel.php";
            if (!file_exists($libPath)) {
                die("Thiếu thư viện PHPExcel!");
            }
            require_once $libPath;

            try {
                // Tự động nhận diện kiểu file (xls hay xlsx đều đọc được)
                $objPHPExcel = PHPExcel_IOFactory::load($file);
                $sheet = $objPHPExcel->getSheet(0); 
                $highestRow = $sheet->getHighestRow();

                $model = $this->model("GuestModel");
                $countSuccess = 0;
                $countFail = 0;

                for ($row = 2; $row <= $highestRow; $row++) {
                    $ho     = trim($sheet->getCellByColumnAndRow(0, $row)->getValue()); // Cột A
                    $ten    = trim($sheet->getCellByColumnAndRow(1, $row)->getValue()); // Cột B
                    $sdt    = trim($sheet->getCellByColumnAndRow(2, $row)->getValue()); // Cột C
                    $email  = trim($sheet->getCellByColumnAndRow(3, $row)->getValue()); // Cột D
                    $cmnd   = trim($sheet->getCellByColumnAndRow(4, $row)->getValue()); // Cột E
                    $diachi = trim($sheet->getCellByColumnAndRow(5, $row)->getValue()); // Cột F

                    if (empty($sdt) || empty($ten)) {
                        continue;
                    }

                    if (!$model->checkPhoneExists($sdt)) {
                        $data = [
                            'HoKhachHang' => $ho,
                            'TenKhachHang' => $ten,
                            'SoDienThoaiKhachHang' => $sdt,
                            'EmailKhachHang' => $email,
                            'CMND_CCCDKhachHang' => $cmnd,
                            'DiaChi' => $diachi,
                            'MatKhau' => $sdt 
                        ];
                        if($model->createGuest($data)){
                            $countSuccess++;
                        }
                    } else {
                        $countFail++;
                    }
                }

                echo "<script>
                    alert('Đã nhập xong!\\n- Thành công: $countSuccess\\n- Trùng lặp (bỏ qua): $countFail');
                    window.location.href='?controller=GuestController&action=index';
                </script>";

            } catch (Exception $e) {
                echo "Lỗi khi đọc file Excel: " . $e->getMessage();
            }
        }
    }
}
?>
