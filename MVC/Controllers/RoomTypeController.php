<?php
class RoomTypeController extends Controller {

    public function index() {
        $model = $this->model("RoomTypeModel");
        
        // Dùng toán tử 3 ngôi để lấy từ khóa (ngắn gọn hơn if-else)
        // Nên dùng GET thay vì POST cho tìm kiếm để dễ chia sẻ link
        $keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
        
        $this->view("Master", [
            "Page" => "RoomTypeManage",
            "page_tab" => "roomtype",
            "roomTypes" => $model->getAll($keyword), // Gọi 1 hàm duy nhất
            "keyword" => $keyword
        ]);
    }

    public function saveRoomType() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model("RoomTypeModel");
            
            $id = $_POST['MaLoaiPhong'];
            $ten = $_POST['TenLoaiPhong'];
            $gia = $_POST['GiaPhong'];
            $mota = $_POST['MoTaPhong'];
            $isEdit = $_POST['isEdit']; // 0: Thêm, 1: Sửa

            // Validate cơ bản
            if(empty($id) || empty($ten) || empty($gia)) {
                echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.history.back();</script>";
                return;
            }

            if ($isEdit == 0) {
                // --- CHỨC NĂNG THÊM MỚI ---
                // Kiểm tra trùng mã
                $check = $model->getById($id);
                if ($check) {
                    echo "<script>alert('Mã loại phòng này đã tồn tại!'); window.history.back();</script>";
                } else {
                    $model->insert($id, $ten, $gia, $mota);
                    echo "<script>alert('Thêm mới thành công!'); window.location.href='?controller=RoomTypeController&action=index';</script>";
                }
            } else {
                // --- CHỨC NĂNG CẬP NHẬT ---
                $model->update($id, $ten, $gia, $mota);
                echo "<script>alert('Cập nhật thành công!'); window.location.href='?controller=RoomTypeController&action=index';</script>";
            }
        }
    }

    // 3. Xóa loại phòng
    public function deleteRoomType() {
        if (isset($_GET['id'])) {
            $model = $this->model("RoomTypeModel");
            $model->delete($_GET['id']);
            header("Location: ?controller=RoomTypeController&action=index");
        }
    }
    



    public function exportExcel() {
        $libPath = dirname(__DIR__, 2) . "/Public/Classes/PHPExcel.php";

        if (file_exists($libPath)) {
            require_once $libPath;
        } else {
            die("Lỗi: Không tìm thấy thư viện PHPExcel tại đường dẫn: " . $libPath);
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->setCellValue('A1', 'Mã Loại');
        $sheet->setCellValue('B1', 'Tên Loại Phòng');
        $sheet->setCellValue('C1', 'Giá Phòng (VNĐ)'); 
        $sheet->setCellValue('D1', 'Mô Tả Tiện Nghi');

        $model = $this->model("RoomTypeModel");
        $data = $model->getAll(); 

        $row = 2;
        if ($data) {
            while ($dbRow = mysqli_fetch_array($data)) {
                
                // Cột A: Mã loại (Dùng setCellValueExplicit để giữ nguyên định dạng chuỗi nếu mã là số)
                $sheet->setCellValueExplicit('A' . $row, $dbRow['MaLoaiPhong'], PHPExcel_Cell_DataType::TYPE_STRING);
                
                $sheet->setCellValue('B' . $row, $dbRow['TenLoaiPhong']);
                
                $sheet->setCellValue('C' . $row, $dbRow['GiaPhong']);
                $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0');

                $sheet->setCellValue('D' . $row, $dbRow['MoTaPhong']);
                
                $row++;
            }
        }

        foreach(range('A','D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        if(ob_get_contents()) ob_end_clean();

        $filename = "Danh_Sach_Loai_Phong_" . date('Ymd_His') . ".xls";

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        // 7. Lưu file và xuất ra trình duyệt
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit();
    }

    
    public function importExcel() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
            $file = $_FILES['excel_file']['tmp_name'];
            
            // Đường dẫn tuyệt đối đến thư viện
            $libPath = dirname(__DIR__, 2) . "/Public/Classes/PHPExcel.php";

            if (file_exists($libPath)) {
                require_once $libPath;
            } else {
                die("Không tìm thấy thư viện tại: " . $libPath);
            }

            try {
                // Tự động nhận diện file (.xls hoặc .xlsx)
                $objPHPExcel = PHPExcel_IOFactory::load($file);
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();

                $model = $this->model("RoomTypeModel");
                $countSuccess = 0;

                for ($row = 1; $row <= $highestRow; $row++) {
                    // Cột A: Mã, B: Tên, C: Giá, D: Mô tả
                    $id   = $sheet->getCellByColumnAndRow(0, $row)->getValue();
                    $ten  = $sheet->getCellByColumnAndRow(1, $row)->getValue();
                    $gia  = $sheet->getCellByColumnAndRow(2, $row)->getValue();
                    $mota = $sheet->getCellByColumnAndRow(3, $row)->getValue();

                    if (!empty($id) && !empty($ten)) {
                        // Kiểm tra trùng
                        $check = $model->getById($id);
                        if (!$check) {
                            $model->insert($id, $ten, $gia, $mota);
                            $countSuccess++;
                        }
                    }
                }
                
                echo "<script>
                    alert('Đã nhập thành công $countSuccess loại phòng!'); 
                    window.location.href='?controller=RoomTypeController&action=index';
                </script>";

            } catch (Exception $e) {
                echo "<script>
                    alert('Lỗi đọc file: " . $e->getMessage() . "');
                    window.history.back();
                </script>";
            }
        }
    }
}
?>