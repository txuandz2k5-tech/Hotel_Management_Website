<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\GuestResource;

class GuestController extends BaseApiController {
    
    /**
     * GET /api/v1/guests
     * Lấy danh sách guests với pagination
     */
    public function index() {
        try {
            $page = $this->getPage();
            $perPage = $this->getPerPage();
            $search = $this->getSearch();

            $guestModel = $this->model('Guest');
            
            if ($search) {
                $guests = $guestModel->search($search, $page, $perPage);
                $total = $guestModel->searchCount($search);
            } else {
                $guests = $guestModel->paginate($page, $perPage);
                $total = $guestModel->count();
            }

            return $this->response->paginate(
                GuestResource::collection($guests),
                $total,
                $page,
                $perPage
            );
        } catch (\Exception $e) {
            $this->logError('Error in GuestController::index', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    /**
     * GET /api/v1/guests/:id
     * Lấy thông tin 1 guest
     */
    public function show() {
        try {
            $id = $this->getId();
            
            if (!$id) {
                return $this->response->error('ID is required', 400);
            }

            $guestModel = $this->model('Guest');
            $guest = $guestModel->find($id);

            if (!$guest) {
                return $this->response->notFound('Guest not found');
            }

            return $this->response->success(
                GuestResource::transform($guest),
                'Guest retrieved successfully'
            );
        } catch (\Exception $e) {
            $this->logError('Error in GuestController::show', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    /**
     * POST /api/v1/guests
     * Thêm guest mới
     */
    public function store() {
        try {
            $data = $this->request->all();

            // Validate
            if (!$this->validate($data, [
                'name' => ['required'],
                'email' => ['required', 'email'],
                'phone' => ['required'],
                'id_card' => ['required'],
                'password' => ['required', 'min:6'],
            ])) {
                return; // Response already sent by validate()
            }

            $guestModel = $this->model('Guest');
            
            // Check if email already exists
            $existingGuest = $guestModel->findByEmail($data['email']);
            if ($existingGuest) {
                return $this->response->error('Email already exists', 409);
            }

            // Check if phone already exists
            $existingPhone = $guestModel->findByPhone($data['phone']);
            if ($existingPhone) {
                return $this->response->error('Phone already exists', 409);
            }

            // Hash password
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

            $guest = $guestModel->create($data);

            return $this->response->created(
                GuestResource::transform($guest),
                'Guest created successfully'
            );
        } catch (\Exception $e) {
            $this->logError('Error in GuestController::store', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    /**
     * PUT /api/v1/guests/:id
     * Cập nhật guest
     */
    public function update() {
        try {
            $id = $this->getId();
            
            if (!$id) {
                return $this->response->error('ID is required', 400);
            }

            $data = $this->request->all();
            unset($data['id']); // Remove route param from update data

            $guestModel = $this->model('Guest');
            $guest = $guestModel->find($id);

            if (!$guest) {
                return $this->response->notFound('Guest not found');
            }

            $guestModel->update($id, $data);
            $guest = $guestModel->find($id);

            return $this->response->success(
                GuestResource::transform($guest),
                'Guest updated successfully'
            );
        } catch (\Exception $e) {
            $this->logError('Error in GuestController::update', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    /**
     * DELETE /api/v1/guests/:id
     * Xóa guest
     */
    public function destroy() {
        try {
            $id = $this->getId();
            
            if (!$id) {
                return $this->response->error('ID is required', 400);
            }

            $guestModel = $this->model('Guest');
            if (!$guestModel->find($id)) {
                return $this->response->notFound('Guest not found');
            }

            $guestModel->delete($id);

            return $this->response->success(
                null,
                'Guest deleted successfully'
            );
        } catch (\Exception $e) {
            $this->logError('Error in GuestController::destroy', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
}
?>
