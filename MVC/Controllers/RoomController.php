<?php
class RoomController extends Controller {

    public function __construct() {
        // Các action dành cho khách (như create) thì không cần check hoặc check riêng
        // Nhưng nếu muốn bảo vệ trang quản lý index:
        
        // Lấy action hiện tại từ URL để loại trừ các trang public (nếu cần)
        $action = $_GET['action'] ?? 'index';
        
        // Ví dụ: Action 'create' là khách đặt phòng thì không check role admin/employee
        if ($action != 'create' && $action != 'handleCreate') {
             $this->requireRole(['admin', 'employee']);
        }
    }

    public function index() {
        $roomModel = $this->model("RoomModel");
        $roomTypeModel = $this->model("RoomTypeModel"); // Cần file này để lấy Dropdown

        $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';

        $this->view("Master", [
            "Page" => "RoomManage", // Tên file View ở Bước 3
            "rooms" => $roomModel->getAll($keyword),
            "roomTypes" => $roomTypeModel->getAll(), // Dữ liệu cho Combobox
            "page_tab" => "room"
            
        ]);
    }

    public function saveRoom() {
        if(isset($_POST['MaPhong'])) {
            $model = $this->model("RoomModel");
            
            $id = $_POST['MaPhong'];
            $soPhong = $_POST['SoPhong'];
            $maLoai = $_POST['MaLoaiPhong'];
            $khaDung = $_POST['KhaDung'];
            $isEdit = $_POST['isEdit']; // 0 là Thêm, 1 là Sửa

            if($isEdit == 0) {
                if(mysqli_num_rows($model->checkExists($id)) > 0){
                    echo "<script>alert('Mã phòng này đã tồn tại!'); window.history.back();</script>";
                } else {
                    $model->insert($id, $soPhong, $maLoai, $khaDung);
                }
            } else {
                $model->updateRoomInfo($id, $soPhong, $maLoai, $khaDung);
            }
            
            // Quay về trang chủ
            header("Location: ?controller=RoomController&action=index");
        }
    }

    public function deleteRoom() {
        if(isset($_GET['id'])) {
            $model = $this->model("RoomModel");
            $model->delete($_GET['id']);
            header("Location: ?controller=RoomController&action=index");
        }
    }

    

    public function exportExcel() {
    $libPath = dirname(__DIR__, 2) . "/Public/Classes/PHPExcel.php";
    
    if (file_exists($libPath)) {
        require_once $libPath;
    } else {
        die("Không tìm thấy thư viện PHPExcel!");
    }

    // 2. Khởi tạo đối tượng Excel mới
    $objPHPExcel = new PHPExcel();
    // Chọn sheet đầu tiên để thao tác
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();

    // 3. Đặt tiêu đề cột (Header) - Dòng 1
    // Set in đậm cho tiêu đề đẹp hơn
    $sheet->getStyle('A1:D1')->getFont()->setBold(true);
    
    $sheet->setCellValue('A1', 'Mã Phòng');
    $sheet->setCellValue('B1', 'Số Phòng');
    $sheet->setCellValue('C1', 'Loại Phòng');
    $sheet->setCellValue('D1', 'Trạng Thái');

    // 4. Lấy dữ liệu từ Database
    $roomModel = $this->model("RoomModel");
    $rooms = $roomModel->getAll(); // Lấy tất cả dữ liệu

    // 5. Đổ dữ liệu vào các dòng tiếp theo
    $row = 2; // Bắt đầu từ dòng 2 (vì dòng 1 là tiêu đề)
    
    if ($rooms) {
        while ($dbRow = mysqli_fetch_array($rooms)) {
            $statusText = ($dbRow['KhaDung'] == 'Yes') ? 'Sẵn sàng' : 'Bảo trì';
            
            $sheet->setCellValue('A' . $row, $dbRow['MaPhong']);
            $sheet->setCellValue('B' . $row, $dbRow['SoPhong']);
            $sheet->setCellValue('C' . $row, $dbRow['TenLoaiPhong']);
            $sheet->setCellValue('D' . $row, $statusText);
            
            $row++;
        }
    }

    // Tự động điều chỉnh độ rộng cột cho đẹp (Optional)
    foreach(range('A','D') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Xóa bộ đệm (buffer) để tránh lỗi file bị hỏng
    if(ob_get_contents()) ob_end_clean();

    // 6. Xuất file ra trình duyệt
    $filename = "Danh_Sach_Phong" . date('Ymd') . ".xls";

    // Header báo trình duyệt đây là file Excel5
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Dùng Writer để ghi dữ liệu
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit();
}

    public function importExcel() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
            $file = $_FILES['excel_file']['tmp_name'];
            $libPath = dirname(__DIR__, 2) . "/Public/Classes/PHPExcel.php";
            if (file_exists($libPath)) {
                require_once $libPath;
            } else {
                die("Không tìm thấy thư viện tại: " . $libPath);
            }
            try {
                $objPHPExcel = PHPExcel_IOFactory::load($file);
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();

                $model = $this->model("RoomModel");
                $successCount = 0;

                for ($row = 1; $row <= $highestRow; $row++) {
                    $id      = $sheet->getCellByColumnAndRow(0, $row)->getValue(); // Cột A: Mã Phòng
                    $soPhong = $sheet->getCellByColumnAndRow(1, $row)->getValue(); // Cột B: Số Phòng
                    $maLoai  = $sheet->getCellByColumnAndRow(2, $row)->getValue(); // Cột C: Mã Loại
                    $khaDung = $sheet->getCellByColumnAndRow(3, $row)->getValue(); // Cột D: Trạng thái

                    if (!empty($id)) {
                        $check = $model->checkExists($id);
                        if (mysqli_num_rows($check) == 0) {
                            
                            if(empty($khaDung)) $khaDung = 'Yes';
                            
                            $model->insert($id, $soPhong, $maLoai, $khaDung);
                            $successCount++;
                        }
                    }
                }
                
                echo "<script>
                    alert('Thành công! Đã thêm $successCount phòng mới.');
                    window.location.href='?controller=RoomController&action=index';
                </script>";

            } catch (Exception $e) {
                die("Lỗi đọc file Excel: " . $e->getMessage());
            }
        }
    }
}
?>