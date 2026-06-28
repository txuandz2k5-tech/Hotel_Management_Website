<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\RoomResource;

class RoomController extends BaseApiController {
    
    public function index() {
        try {
            $page = $this->getPage();
            $perPage = $this->getPerPage();

            $roomModel = $this->model('Room');
            $rooms = $roomModel->paginate($page, $perPage);
            $total = $roomModel->count();

            return $this->response->paginate(RoomResource::collection($rooms), $total, $page, $perPage);
        } catch (\Exception $e) {
            $this->logError('Error in RoomController::index', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function show() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $room = $this->model('Room')->find($id);
            if (!$room) return $this->response->notFound('Room not found');

            return $this->response->success(RoomResource::transform($room));
        } catch (\Exception $e) {
            $this->logError('Error in RoomController::show', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function store() {
        try {
            $data = $this->request->all();

            if (empty($data['availability']) && empty($data['status'])) {
                $this->response->validationError(['availability' => 'Trường availability hoặc status là bắt buộc']);
                return;
            }

            if (!$this->validate($data, [
                'room_number' => ['required'],
                'room_type_id' => ['required'],
            ])) {
                return;
            }

            $availability = $this->normalizeAvailability($data['availability'] ?? $data['status']);
            if ($availability === null) {
                $this->response->validationError(['availability' => 'Giá trị availability/status phải là Yes/No hoặc available/unavailable']);
                return;
            }

            $roomId = $data['id'] ?? null;
            if (!$roomId) {
                $roomId = $this->generateRoomId($data['room_number']);
            }

            $roomData = [
                'MaPhong' => $roomId,
                'SoPhong' => $data['room_number'],
                'MaLoaiPhong' => $data['room_type_id'],
                'KhaDung' => $availability,
            ];

            $room = $this->model('Room')->create($roomData);
            return $this->response->created(RoomResource::transform($room));
        } catch (\Exception $e) {
            $this->logError('Error in RoomController::store', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function update() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $roomModel = $this->model('Room');
            if (!$roomModel->find($id)) return $this->response->notFound('Room not found');

            $data = $this->request->all();
            $roomData = [];
            if (isset($data['room_number'])) {
                $roomData['SoPhong'] = $data['room_number'];
            }
            if (isset($data['room_type_id'])) {
                $roomData['MaLoaiPhong'] = $data['room_type_id'];
            }

            $availability = null;
            if (isset($data['availability'])) {
                $availability = $this->normalizeAvailability($data['availability']);
            } elseif (isset($data['status'])) {
                $availability = $this->normalizeAvailability($data['status']);
            }

            if ($availability !== null) {
                $roomData['KhaDung'] = $availability;
            }

            if (empty($roomData)) {
                $this->response->validationError(['data' => 'Không có trường hợp lệ để cập nhật. Vui lòng gửi room_number, room_type_id, availability hoặc status.']);
                return;
            }

            $room = $roomModel->update($id, $roomData);

            return $this->response->success(RoomResource::transform($room));
        } catch (\Exception $e) {
            $this->logError('Error in RoomController::update', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function destroy() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $roomModel = $this->model('Room');
            if (!$roomModel->find($id)) return $this->response->notFound('Room not found');

            $roomModel->delete($id);
            return $this->response->success(null, 'Room deleted successfully');
        } catch (\Exception $e) {
            $this->logError('Error in RoomController::destroy', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    private function generateRoomId($roomNumber) {
        $number = preg_replace('/[^A-Za-z0-9]/', '', $roomNumber);
        if ($number === '') {
            $number = uniqid('R');
        }
        $candidate = strtoupper($number);
        if (stripos($candidate, 'P') !== 0) {
            $candidate = 'P' . $candidate;
        }

        $roomModel = $this->model('Room');
        $suffix = 1;
        $base = $candidate;
        while ($roomModel->find($candidate)) {
            $candidate = $base . '_' . $suffix;
            $suffix++;
        }

        return $candidate;
    }

    private function normalizeAvailability($value) {
        if ($value === null) {
            return null;
        }

        $value = trim(strtolower((string) $value));
        if (in_array($value, ['yes', 'y', 'true', 'available', 'open', '1', 'vacant', 'free'], true)) {
            return 'Yes';
        }

        if (in_array($value, ['no', 'n', 'false', 'unavailable', 'closed', '0', 'occupied', 'busy', 'booked'], true)) {
            return 'No';
        }

        return null;
    }
}
?>
