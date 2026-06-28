<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\AccountResource;

class AccountController extends BaseApiController {
    public function index() {
        try {
            $page = $this->getPage();
            $perPage = $this->getPerPage();
            $accModel = $this->model('Account');
            $accs = $accModel->paginate($page, $perPage);
            $total = $accModel->count();
            return $this->response->paginate(AccountResource::collection($accs), $total, $page, $perPage);
        } catch (\Exception $e) {
            $this->logError('Error in AccountController::index', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function show() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $acc = $this->model('Account')->find($id);
            if (!$acc) return $this->response->notFound('Account not found');
            return $this->response->success(AccountResource::transform($acc));
        } catch (\Exception $e) {
            $this->logError('Error in AccountController::show', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function store() {
        try {
            $data = $this->request->all();
            if (!$this->validate($data, ['email' => ['required', 'email'], 'name' => ['required']])) return;
            $acc = $this->model('Account')->create($data);
            return $this->response->created(AccountResource::transform($acc));
        } catch (\Exception $e) {
            $this->logError('Error in AccountController::store', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function update() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $accModel = $this->model('Account');
            if (!$accModel->find($id)) return $this->response->notFound('Account not found');
            $data = $this->request->all();
            $acc = $accModel->update($id, $data);
            return $this->response->success(AccountResource::transform($acc));
        } catch (\Exception $e) {
            $this->logError('Error in AccountController::update', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function destroy() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $accModel = $this->model('Account');
            if (!$accModel->find($id)) return $this->response->notFound('Account not found');
            $accModel->delete($id);
            return $this->response->success(null, 'Account deleted successfully');
        } catch (\Exception $e) {
            $this->logError('Error in AccountController::destroy', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
}
?>
