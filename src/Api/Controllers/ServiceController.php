<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\ServiceResource;

class ServiceController extends BaseApiController {
    
    public function index() {
        try {
            $serviceModel = $this->model('Service');
            $services = $serviceModel->all();

            return $this->response->success(ServiceResource::collection($services));
        } catch (\Exception $e) {
            $this->logError('Error in ServiceController::index', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function show() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $service = $this->model('Service')->find($id);
            if (!$service) return $this->response->notFound('Service not found');

            return $this->response->success(ServiceResource::transform($service));
        } catch (\Exception $e) {
            $this->logError('Error in ServiceController::show', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function store() {
        try {
            $data = $this->request->all();

            if (!$this->validate($data, [
                'name' => ['required'],
                'price' => ['required', 'numeric'],
            ])) {
                return;
            }

            // Generate new service ID
            $serviceModel = $this->model('Service');
            $allServices = $serviceModel->all();
            $maxId = 0;
            foreach ($allServices as $service) {
                $idStr = $service['MaDichVu'];
                if (preg_match('/#?DV(\d+)/', $idStr, $matches)) {
                    $id = (int)$matches[1];
                    if ($id > $maxId) $maxId = $id;
                }
            }
            $newId = 'DV' . str_pad($maxId + 1, 2, '0', STR_PAD_LEFT);

            // Map API fields to database fields
            $dbData = [
                'MaDichVu' => $newId,
                'TenDichVu' => $data['name'],
                'MoTaDichVu' => $data['description'] ?? '',
                'ChiPhiDichVu' => $data['price']
            ];

            $service = $serviceModel->create($dbData);
            error_log('Created service: ' . json_encode($service));
            return $this->response->created(ServiceResource::transform($service));
        } catch (\Exception $e) {
            $this->logError('Error in ServiceController::store', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function update() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $serviceModel = $this->model('Service');
            if (!$serviceModel->find($id)) return $this->response->notFound('Service not found');

            $data = $this->request->all();
            
            // Map API fields to database fields
            $dbData = [];
            if (isset($data['name'])) $dbData['TenDichVu'] = $data['name'];
            if (isset($data['description'])) $dbData['MoTaDichVu'] = $data['description'];
            if (isset($data['price'])) $dbData['ChiPhiDichVu'] = $data['price'];
            
            $service = $serviceModel->update($id, $dbData);

            return $this->response->success(ServiceResource::transform($service));
        } catch (\Exception $e) {
            $this->logError('Error in ServiceController::update', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    public function destroy() {
        try {
            $id = $this->getId();
            if (!$id) return $this->response->error('ID is required', 400);

            $serviceModel = $this->model('Service');
            if (!$serviceModel->find($id)) return $this->response->notFound('Service not found');

            $serviceModel->delete($id);
            return $this->response->success(null, 'Service deleted successfully');
        } catch (\Exception $e) {
            $this->logError('Error in ServiceController::destroy', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
}
?>
