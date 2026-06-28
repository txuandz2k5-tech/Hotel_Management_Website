<?php
namespace Api\Controllers;

use Api\BaseApiController;
use Api\Resources\ServiceOrderResource;
use Shared\Auth\JWT;

class ServiceOrderController extends BaseApiController {
    
    /**
     * Get all service orders for a specific booking
     */
    public function index() {
        try {
            $bookingId = $this->request->query('booking_id');
            $serviceOrderModel = $this->model('ServiceOrder');

            if ($bookingId) {
                // Verify booking exists
                $booking = $this->model('Booking')->find($bookingId);
                if (!$booking) {
                    return $this->response->notFound('Booking not found');
                }

                $orders = $serviceOrderModel->getByBooking($bookingId);
            } else {
                $orders = $serviceOrderModel->getAllOrders();
            }

            return $this->response->success(ServiceOrderResource::collection($orders));
        } catch (\Exception $e) {
            $this->logError('Error in ServiceOrderController::index', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    /**
     * Get a specific service order
     */
    public function show() {
        try {
            $id = $this->getId();
            if (!$id) {
                return $this->response->error('ID is required', 400);
            }

            $order = $this->model('ServiceOrder')->findWithDetails($id);
            if (!$order) {
                return $this->response->notFound('Service order not found');
            }

            return $this->response->success(ServiceOrderResource::transform($order));
        } catch (\Exception $e) {
            $this->logError('Error in ServiceOrderController::show', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    /**
     * Create a new service order (add service to booking)
     */
    public function store() {
        try {
            $data = $this->request->all();

            // Validate required fields - booking_id is required to link with customer
            if (!$this->validate($data, [
                'booking_id' => ['required'],
                'service_id' => ['required'],
                'quantity' => ['required', 'numeric'],
            ])) {
                return;
            }

            $bookingId = $data['booking_id'];
            $serviceId = $data['service_id'];
            $quantity = (int)$data['quantity'] ?? 1;

            // Verify booking exists
            $booking = $this->model('Booking')->find($bookingId);
            if (!$booking) {
                return $this->response->notFound('Booking not found');
            }

            // Check if user has permission to add service to this booking
            $currentUser = JWT::validateToken();
            if (!$currentUser) {
                return $this->response->unauthorized('Invalid or missing token');
            }
            
            if ($currentUser['role'] === 'guest' && $booking['MaKhachHang'] !== $currentUser['id']) {
                return $this->response->error('You can only add services to your own bookings', 403);
            }

            // Verify service exists
            $service = $this->model('Service')->find($serviceId);
            if (!$service) {
                return $this->response->notFound('Service not found');
            }

            // Quantity must be at least 1
            if ($quantity < 1) {
                return $this->response->error('Quantity must be at least 1', 400);
            }

            // Add service to booking
            $serviceOrder = $this->model('ServiceOrder')->addServiceToBooking($bookingId, $serviceId, $quantity);
            
            if (!$serviceOrder) {
                return $this->response->error('Failed to add service to booking', 500);
            }

            error_log('Created service order: ' . json_encode($serviceOrder));
            return $this->response->created(ServiceOrderResource::transform($serviceOrder));
        } catch (\Exception $e) {
            $this->logError('Error in ServiceOrderController::store', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    /**
     * Update service order quantity
     */
    public function update() {
        try {
            $id = $this->getId();
            if (!$id) {
                return $this->response->error('ID is required', 400);
            }

            $serviceOrderModel = $this->model('ServiceOrder');
            $order = $serviceOrderModel->find($id);
            
            if (!$order) {
                return $this->response->notFound('Service order not found');
            }

            $data = $this->request->all();
            
            // Only allow updating quantity
            if (isset($data['quantity'])) {
                $quantity = (int)$data['quantity'];
                if ($quantity < 1) {
                    return $this->response->error('Quantity must be at least 1', 400);
                }
                
                // Recalculate total price
                $unitPrice = $order['DonGia'];
                $totalPrice = $quantity * $unitPrice;
                
                $updateData = [
                    'SoLuong' => $quantity,
                    'ThanhTien' => $totalPrice,
                ];
                
                $updated = $serviceOrderModel->update($id, $updateData);
                if (!$updated) {
                    return $this->response->error('Failed to update service order', 500);
                }

                $updated = $serviceOrderModel->find($id);
                return $this->response->success(ServiceOrderResource::transform($updated));
            }

            return $this->response->error('No valid fields to update', 400);
        } catch (\Exception $e) {
            $this->logError('Error in ServiceOrderController::update', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    /**
     * Delete service order (remove service from booking)
     */
    public function destroy() {
        try {
            $id = $this->getId();
            if (!$id) {
                return $this->response->error('ID is required', 400);
            }

            $serviceOrderModel = $this->model('ServiceOrder');
            $order = $serviceOrderModel->find($id);
            
            if (!$order) {
                return $this->response->notFound('Service order not found');
            }

            $result = $serviceOrderModel->removeServiceFromBooking($id);
            
            if (!$result) {
                return $this->response->error('Failed to delete service order', 500);
            }

            return $this->response->success(['message' => 'Service order deleted successfully']);
        } catch (\Exception $e) {
            $this->logError('Error in ServiceOrderController::destroy', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }

    /**
     * Get total service cost for a booking
     */
    public function getTotalCost() {
        try {
            $bookingId = $this->request->query('booking_id');
            
            if (!$bookingId) {
                return $this->response->error('booking_id is required', 400);
            }

            // Verify booking exists
            $booking = $this->model('Booking')->find($bookingId);
            if (!$booking) {
                return $this->response->notFound('Booking not found');
            }

            $totalCost = $this->model('ServiceOrder')->getTotalServiceCost($bookingId);

            return $this->response->success([
                'booking_id' => $bookingId,
                'total_service_cost' => (float)$totalCost
            ]);
        } catch (\Exception $e) {
            $this->logError('Error in ServiceOrderController::getTotalCost', $e);
            return $this->response->error($e->getMessage(), 500);
        }
    }
}
?>
