<?php
namespace Api\Controllers;
 
use Api\BaseApiController;
use Api\Resources\BookingResource;
use Shared\Auth\JWT;
 
class BookingController extends BaseApiController {
    public function index() {
        try {
            $page = $this->getPage();
            $perPage = $this->getPerPage();

            $currentUser = $_SESSION['api_user'] ?? JWT::validateToken();
            $bookingModel = $this->model('Booking');

            if ($currentUser && ($currentUser['role'] ?? '') === 'guest') {
                $bookings = $bookingModel->paginateByGuest($currentUser['id'], $page, $perPage);
                $total = $bookingModel->countByGuest($currentUser['id']);
            } else {
                $bookings = $bookingModel->paginate($page, $perPage);
                $total = $bookingModel->count();
            }

            return $this->response->paginate(
                BookingResource::collection($bookings),
                $total,
                $page,
                $perPage
            );
        } catch (\Exception $e) {
            $this->logError('Error in BookingController::index', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function show() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $booking = $this->model('Booking')->find($id);
            if (!$booking) return $this->response->notFound('Booking not found');

            $currentUser = $_SESSION['api_user'] ?? JWT::validateToken();
            if ($currentUser && ($currentUser['role'] ?? '') === 'guest' && $booking['MaKhachHang'] !== $currentUser['id']) {
                return $this->response->forbidden('You can only view your own bookings');
            }

            return $this->response->success(BookingResource::transform($booking));
        } catch (\Exception $e) {
            $this->logError('Error in BookingController::show', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function store() {
        try {
            $data = $this->request->all();
            $currentUser = $_SESSION['api_user'] ?? JWT::validateToken();
            if ($currentUser && ($currentUser['role'] ?? '') === 'guest') {
                $data['guest_id'] = $currentUser['id'];
            }

            if (!$this->validate($data, [
                'guest_id' => ['required', 'numeric'],
                'room_id' => ['required'],
                'check_in_date' => ['required'],
                'check_out_date' => ['required'],
            ])) {
                return;
            }

            $guest = $this->model('Guest')->find($data['guest_id']);
            if (!$guest) {
                return $this->response->error('Guest not found', 404);
            }

            $bookingData = [
                'MaKhachHang' => $data['guest_id'],
                'MaLoaiPhong' => $data['room_id'],
                'NgayNhanPhong' => $data['check_in_date'],
                'NgayTraPhong' => $data['check_out_date'],
                'SoTienDatPhong' => $data['total_price'] ?? 0,
                'TrangThai' => $data['status'] ?? 'Pending',
                'GhiChu' => $data['note'] ?? ''
            ];

            $booking = $this->model('Booking')->create($bookingData);
            return $this->response->created(BookingResource::transform($booking));
        } catch (\Exception $e) {
            $this->logError('Error in BookingController::store', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function update() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $bookingModel = $this->model('Booking');
            $booking = $bookingModel->find($id);
            if (!$booking) return $this->response->notFound('Booking not found');

            $currentUser = $_SESSION['api_user'] ?? JWT::validateToken();
            if ($currentUser && ($currentUser['role'] ?? '') === 'guest' && $booking['MaKhachHang'] !== $currentUser['id']) {
                return $this->response->forbidden('You can only update your own bookings');
            }

            $data = $this->request->all();
            $bookingData = [];
            if (isset($data['guest_id'])) {
                $bookingData['MaKhachHang'] = $data['guest_id'];
            }
            if (isset($data['room_id'])) {
                $bookingData['MaLoaiPhong'] = $data['room_id'];
            }
            if (isset($data['check_in'])) {
                $bookingData['NgayNhanPhong'] = $data['check_in'];
            }
            if (isset($data['check_in_date'])) {
                $bookingData['NgayNhanPhong'] = $data['check_in_date'];
            }
            if (isset($data['check_out'])) {
                $bookingData['NgayTraPhong'] = $data['check_out'];
            }
            if (isset($data['check_out_date'])) {
                $bookingData['NgayTraPhong'] = $data['check_out_date'];
            }
            if (isset($data['total_price'])) {
                $bookingData['SoTienDatPhong'] = $data['total_price'];
            }
            if (isset($data['status'])) {
                $bookingData['TrangThai'] = $data['status'];
            }
            if (isset($data['note'])) {
                $bookingData['GhiChu'] = $data['note'];
            }

            if (empty($bookingData)) {
                $this->response->validationError(['data' => 'Không có trường hợp lệ để cập nhật.']);
                return;
            }

            $booking = $bookingModel->update($id, $bookingData);
            return $this->response->success(BookingResource::transform($booking));
        } catch (\Exception $e) {
            $this->logError('Error in BookingController::update', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function destroy() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $bookingModel = $this->model('Booking');
            $booking = $bookingModel->find($id);
            if (!$booking) return $this->response->notFound('Booking not found');

            $currentUser = $_SESSION['api_user'] ?? JWT::validateToken();
            if ($currentUser && ($currentUser['role'] ?? '') === 'guest' && $booking['MaKhachHang'] !== $currentUser['id']) {
                return $this->response->forbidden('You can only delete your own bookings');
            }

            $bookingModel->delete($id);
            return $this->response->success(null, 'Booking deleted successfully');
        } catch (\Exception $e) {
            $this->logError('Error in BookingController::destroy', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
}
?>