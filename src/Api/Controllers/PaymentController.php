<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\PaymentResource;

class PaymentController extends BaseApiController {
    public function index() {
        try {
            $page = $this->getPage();
            $perPage = $this->getPerPage();
            $payModel = $this->model('Payment');
            $pays = $payModel->paginate($page, $perPage);
            $total = $payModel->count();
            return $this->response->paginate(PaymentResource::collection($pays), $total, $page, $perPage);
        } catch (\Exception $e) {
            $this->logError('Error in PaymentController::index', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function show() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $pay = $this->model('Payment')->find($id);
            if (!$pay) return $this->response->notFound('Payment not found');
            return $this->response->success(PaymentResource::transform($pay));
        } catch (\Exception $e) {
            $this->logError('Error in PaymentController::show', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function store() {
        try {
            $data = $this->request->all();
            if (!$this->validate($data, ['booking_id' => ['required'], 'amount' => ['required', 'numeric']])) return;
            $pay = $this->model('Payment')->create($data);
            return $this->response->created(PaymentResource::transform($pay));
        } catch (\Exception $e) {
            $this->logError('Error in PaymentController::store', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function update() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $payModel = $this->model('Payment');
            if (!$payModel->find($id)) return $this->response->notFound('Payment not found');
            $data = $this->request->all();
            $pay = $payModel->update($id, $data);
            return $this->response->success(PaymentResource::transform($pay));
        } catch (\Exception $e) {
            $this->logError('Error in PaymentController::update', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function destroy() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $payModel = $this->model('Payment');
            if (!$payModel->find($id)) return $this->response->notFound('Payment not found');
            $payModel->delete($id);
            return $this->response->success(null, 'Payment deleted successfully');
        } catch (\Exception $e) {
            $this->logError('Error in PaymentController::destroy', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
}
?>
