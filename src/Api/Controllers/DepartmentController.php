<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\DepartmentResource;

class DepartmentController extends BaseApiController {
    public function index() {
        try {
            $page = $this->getPage();
            $perPage = $this->getPerPage();
            $deptModel = $this->model('Department');
            $depts = $deptModel->paginate($page, $perPage);
            $total = $deptModel->count();
            return $this->response->paginate(DepartmentResource::collection($depts), $total, $page, $perPage);
        } catch (\Exception $e) {
            $this->logError('Error in DepartmentController::index', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function show() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $dept = $this->model('Department')->find($id);
            if (!$dept) return $this->response->notFound('Department not found');
            return $this->response->success(DepartmentResource::transform($dept));
        } catch (\Exception $e) {
            $this->logError('Error in DepartmentController::show', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function store() {
        try {
            $data = $this->request->all();
            if (!$this->validate($data, ['name' => ['required']])) return;
            $dept = $this->model('Department')->create($data);
            return $this->response->created(DepartmentResource::transform($dept));
        } catch (\Exception $e) {
            $this->logError('Error in DepartmentController::store', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function update() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $deptModel = $this->model('Department');
            if (!$deptModel->find($id)) return $this->response->notFound('Department not found');
            $data = $this->request->all();
            $dept = $deptModel->update($id, $data);
            return $this->response->success(DepartmentResource::transform($dept));
        } catch (\Exception $e) {
            $this->logError('Error in DepartmentController::update', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function destroy() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $deptModel = $this->model('Department');
            if (!$deptModel->find($id)) return $this->response->notFound('Department not found');
            $deptModel->delete($id);
            return $this->response->success(null, 'Department deleted successfully');
        } catch (\Exception $e) {
            $this->logError('Error in DepartmentController::destroy', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
}
?>
