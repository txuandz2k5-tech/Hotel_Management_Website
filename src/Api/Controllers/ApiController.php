<?php
namespace Api\Controllers;

use Api\BaseApiController;

class ApiController extends BaseApiController {
    
    /**
     * GET /api, /api/, /api/v1
     * API Information
     */
    public function index() {
        return $this->response->json([
            'message' => 'Hotel Management REST API',
            'version' => '1.0',
            'endpoints' => [
                'guests' => '/api/v1/guests',
                'bookings' => '/api/v1/bookings',
                'rooms' => '/api/v1/rooms',
                'room-types' => '/api/v1/room-types',
                'services' => '/api/v1/services',
                'departments' => '/api/v1/departments',
                'employees' => '/api/v1/employees',
                'payments' => '/api/v1/payments',
                'discounts' => '/api/v1/discounts',
                'accounts' => '/api/v1/accounts',
            ],
            'documentation' => [
                'GET /{resource}' => 'List all resources with pagination',
                'GET /{resource}/{id}' => 'Get a specific resource',
                'POST /{resource}' => 'Create a new resource',
                'PUT /{resource}/{id}' => 'Update a resource',
                'DELETE /{resource}/{id}' => 'Delete a resource',
            ]
        ]);
    }
}
?>
