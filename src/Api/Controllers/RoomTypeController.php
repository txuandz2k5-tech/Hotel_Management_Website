<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\RoomTypeResource;

class RoomTypeController extends BaseApiController {
    
    public function index() {
        try {
            $page = $this->getPage();
            $perPage = $this->getPerPage();

            $roomTypeModel = $this->model('RoomType');
            $roomTypes = $roomTypeModel->paginate($page, $perPage);
            $total = $roomTypeModel->count();

            return $this->response->paginate(RoomTypeResource::collection($roomTypes), $total, $page, $perPage);
        } catch (\Exception $e) {
            $this->logError('Error in RoomTypeController::index', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function show() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $roomType = $this->model('RoomType')->find($id);
            if (!$roomType) return $this->response->notFound('Room type not found');

            return $this->response->success(RoomTypeResource::transform($roomType));
        } catch (\Exception $e) {
            $this->logError('Error in RoomTypeController::show', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function store() {
        try {
            $data = $this->request->all();

            if (!$this->validate($data, [
                'name' => ['required'],
                'price_per_night' => ['required', 'numeric'],
            ])) {
                return;
            }

            $roomType = $this->model('RoomType')->create($data);
            return $this->response->created(RoomTypeResource::transform($roomType));
        } catch (\Exception $e) {
            $this->logError('Error in RoomTypeController::store', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function update() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $roomTypeModel = $this->model('RoomType');
            if (!$roomTypeModel->find($id)) return $this->response->notFound('Room type not found');

            $data = $this->request->all();
            $roomType = $roomTypeModel->update($id, $data);

            return $this->response->success(RoomTypeResource::transform($roomType));
        } catch (\Exception $e) {
            $this->logError('Error in RoomTypeController::update', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function destroy() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $roomTypeModel = $this->model('RoomType');
            if (!$roomTypeModel->find($id)) return $this->response->notFound('Room type not found');

            $roomTypeModel->delete($id);
            return $this->response->success(null, 'Room type deleted successfully');
        } catch (\Exception $e) {
            $this->logError('Error in RoomTypeController::destroy', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
}
?>
