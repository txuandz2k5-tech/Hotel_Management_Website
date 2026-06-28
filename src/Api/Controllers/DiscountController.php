<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\DiscountResource;

class DiscountController extends BaseApiController {
    public function index() {
        try {
            $page = $this->getPage();
            $perPage = $this->getPerPage();
            $discModel = $this->model('Discount');
            $discs = $discModel->paginate($page, $perPage);
            $total = $discModel->count();
            return $this->response->paginate(DiscountResource::collection($discs), $total, $page, $perPage);
        } catch (\Exception $e) {
            $this->logError('Error in DiscountController::index', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function show() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $disc = $this->model('Discount')->find($id);
            if (!$disc) return $this->response->notFound('Discount not found');
            return $this->response->success(DiscountResource::transform($disc));
        } catch (\Exception $e) {
            $this->logError('Error in DiscountController::show', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function store() {
        try {
            $data = $this->request->all();
            if (!$this->validate($data, ['code' => ['required'], 'discount_percent' => ['required', 'numeric']])) return;
            $disc = $this->model('Discount')->create($data);
            return $this->response->created(DiscountResource::transform($disc));
        } catch (\Exception $e) {
            $this->logError('Error in DiscountController::store', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function update() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $discModel = $this->model('Discount');
            if (!$discModel->find($id)) return $this->response->notFound('Discount not found');
            $data = $this->request->all();
            $disc = $discModel->update($id, $data);
            return $this->response->success(DiscountResource::transform($disc));
        } catch (\Exception $e) {
            $this->logError('Error in DiscountController::update', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function destroy() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $discModel = $this->model('Discount');
            if (!$discModel->find($id)) return $this->response->notFound('Discount not found');
            $discModel->delete($id);
            return $this->response->success(null, 'Discount deleted successfully');
        } catch (\Exception $e) {
            $this->logError('Error in DiscountController::destroy', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
}
?>
