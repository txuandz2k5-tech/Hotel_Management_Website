<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\EmployeeResource;

class EmployeeController extends BaseApiController {
    public function index() {
        try {
            $page = $this->getPage();
            $perPage = $this->getPerPage();
            $empModel = $this->model('Employee');
            $emps = $empModel->paginate($page, $perPage);
            $total = $empModel->count();
            return $this->response->paginate(EmployeeResource::collection($emps), $total, $page, $perPage);
        } catch (\Exception $e) {
            $this->logError('Error in EmployeeController::index', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function show() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $emp = $this->model('Employee')->find($id);
            if (!$emp) return $this->response->notFound('Employee not found');
            return $this->response->success(EmployeeResource::transform($emp));
        } catch (\Exception $e) {
            $this->logError('Error in EmployeeController::show', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function store() {
        try {
            $data = $this->request->all();
            if (!$this->validate($data, [
                'name' => ['required'], 
                'email' => ['required', 'email'],
                'phone' => ['required'],
                'position' => ['required'],
                'address' => ['required'],
                'id_card' => ['required']
            ])) return;

            // Generate new employee ID
            $empModel = $this->model('Employee');
            $allEmps = $empModel->all();
            $maxId = 0;
            foreach ($allEmps as $emp) {
                $idStr = str_replace('NV', '', $emp['MaNhanVien']);
                $id = (int)$idStr;
                if ($id > $maxId) $maxId = $id;
            }
            $newId = 'NV' . str_pad($maxId + 1, 3, '0', STR_PAD_LEFT);

            // Split name into first and last name
            $nameParts = explode(' ', $data['name'], 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';

            // Map API fields to database fields
            $dbData = [
                'MaNhanVien' => $newId,
                'TenNhanVien' => $lastName,
                'HoNhanVien' => $firstName,
                'ChucDanhNV' => $data['position'],
                'SoDienThoaiNV' => $data['phone'],
                'EmailNhanVien' => $data['email'],
                'NgayVaoLam' => date('Y-m-d'),
                'DiaChi' => $data['address'] ?? '',
                'MaBoPhan' => $data['department_id'] ?? 'BP_LT',
                'CMND_CCCD' => $data['id_card'] ?? ''
            ];

            $emp = $empModel->create($dbData);
            return $this->response->created(EmployeeResource::transform($emp));
        } catch (\Exception $e) {
            $this->logError('Error in EmployeeController::store', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function update() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $empModel = $this->model('Employee');
            if (!$empModel->find($id)) return $this->response->notFound('Employee not found');
            $data = $this->request->all();
            
            // Validate fields if provided
            $validationRules = [];
            if (isset($data['name'])) $validationRules['name'] = ['required'];
            if (isset($data['email'])) $validationRules['email'] = ['required', 'email'];
            if (isset($data['phone'])) $validationRules['phone'] = ['required'];
            if (isset($data['position'])) $validationRules['position'] = ['required'];
            if (isset($data['address'])) $validationRules['address'] = ['required'];
            if (isset($data['id_card'])) $validationRules['id_card'] = ['required'];
            if (!empty($validationRules) && !$this->validate($data, $validationRules)) return;
            
            // Map API fields to database fields
            $dbData = [];
            if (isset($data['name'])) {
                $nameParts = explode(' ', $data['name'], 2);
                $dbData['TenNhanVien'] = $nameParts[1] ?? '';
                $dbData['HoNhanVien'] = $nameParts[0] ?? '';
            }
            if (isset($data['position'])) $dbData['ChucDanhNV'] = $data['position'];
            if (isset($data['phone'])) $dbData['SoDienThoaiNV'] = $data['phone'];
            if (isset($data['email'])) $dbData['EmailNhanVien'] = $data['email'];
            if (isset($data['address'])) $dbData['DiaChi'] = $data['address'];
            if (isset($data['department_id'])) $dbData['MaBoPhan'] = $data['department_id'];
            if (isset($data['id_card'])) $dbData['CMND_CCCD'] = $data['id_card'];
            
            $emp = $empModel->update($id, $dbData);
            return $this->response->success(EmployeeResource::transform($emp));
        } catch (\Exception $e) {
            $this->logError('Error in EmployeeController::update', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
    public function destroy() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);
            $empModel = $this->model('Employee');
            if (!$empModel->find($id)) return $this->response->notFound('Employee not found');
            $empModel->delete($id);
            return $this->response->success(null, 'Employee deleted successfully');
        } catch (\Exception $e) {
            $this->logError('Error in EmployeeController::destroy', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
}
?>
