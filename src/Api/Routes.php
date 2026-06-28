<?php
/**
 * API Routes Definition
 * Format: [METHOD, PATH, CONTROLLER, ACTION, MIDDLEWARE]
 * Middleware: 
 * - 'auth': login required
 * - 'admin': admin only
 * - 'guest': admin or guest (guest can only access own bookings/services)
 */

return [
    // ============ AUTHENTICATION ============
    ['POST', '/api/v1/auth/login', 'AuthController', 'login'],
    ['GET', '/api/v1/auth/me', 'AuthController', 'me', 'auth'],
    ['POST', '/api/v1/auth/logout', 'AuthController', 'logout', 'auth'],

    // ============ Root ============
    ['GET', '/api', 'ApiController', 'index'],
    ['GET', '/api/', 'ApiController', 'index'],
    ['GET', '/api/v1', 'ApiController', 'index'],

    // ============ GUESTS (Admin only) ============
    ['GET', '/api/v1/guests', 'GuestController', 'index', 'admin'],
    ['POST', '/api/v1/guests', 'GuestController', 'store', 'admin'],
    ['GET', '/api/v1/guests/:id', 'GuestController', 'show', 'admin'],
    ['PUT', '/api/v1/guests/:id', 'GuestController', 'update', 'admin'],
    ['DELETE', '/api/v1/guests/:id', 'GuestController', 'destroy', 'admin'],

    // ============ BOOKINGS (Admin only) ============
    ['GET', '/api/v1/bookings', 'BookingController', 'index', 'admin'],
    ['POST', '/api/v1/bookings', 'BookingController', 'store', 'admin'],
    ['GET', '/api/v1/bookings/:id', 'BookingController', 'show', 'admin'],
    ['PUT', '/api/v1/bookings/:id', 'BookingController', 'update', 'admin'],
    ['DELETE', '/api/v1/bookings/:id', 'BookingController', 'destroy', 'admin'],

    // ============ ROOMS (Manage: Admin only) ============
    ['GET', '/api/v1/rooms', 'RoomController', 'index', 'admin'],
    ['POST', '/api/v1/rooms', 'RoomController', 'store', 'admin'],
    ['GET', '/api/v1/rooms/:id', 'RoomController', 'show', 'admin'],
    ['PUT', '/api/v1/rooms/:id', 'RoomController', 'update', 'admin'],
    ['DELETE', '/api/v1/rooms/:id', 'RoomController', 'destroy', 'admin'],

    // ============ ROOM TYPES (View: Auth, Manage: Admin) ============
    ['GET', '/api/v1/room-types', 'RoomTypeController', 'index', 'auth'],
    ['POST', '/api/v1/room-types', 'RoomTypeController', 'store', 'admin'],
    ['GET', '/api/v1/room-types/:id', 'RoomTypeController', 'show', 'auth'],
    ['PUT', '/api/v1/room-types/:id', 'RoomTypeController', 'update', 'admin'],
    ['DELETE', '/api/v1/room-types/:id', 'RoomTypeController', 'destroy', 'admin'],

    // ============ SERVICES (Manage: Admin only) ============
    ['GET', '/api/v1/services', 'ServiceController', 'index', 'admin'],
    ['POST', '/api/v1/services', 'ServiceController', 'store', 'admin'],
    ['GET', '/api/v1/services/:id', 'ServiceController', 'show', 'admin'],
    ['PUT', '/api/v1/services/:id', 'ServiceController', 'update', 'admin'],
    ['DELETE', '/api/v1/services/:id', 'ServiceController', 'destroy', 'admin'],

    // ============ DEPARTMENTS (Admin only) ============
    ['GET', '/api/v1/departments', 'DepartmentController', 'index', 'admin'],
    ['POST', '/api/v1/departments', 'DepartmentController', 'store', 'admin'],
    ['GET', '/api/v1/departments/:id', 'DepartmentController', 'show', 'admin'],
    ['PUT', '/api/v1/departments/:id', 'DepartmentController', 'update', 'admin'],
    ['DELETE', '/api/v1/departments/:id', 'DepartmentController', 'destroy', 'admin'],

    // ============ EMPLOYEES (Admin only) ============
    ['GET', '/api/v1/employees', 'EmployeeController', 'index', 'admin'],
    ['POST', '/api/v1/employees', 'EmployeeController', 'store', 'admin'],
    ['GET', '/api/v1/employees/:id', 'EmployeeController', 'show', 'admin'],
    ['PUT', '/api/v1/employees/:id', 'EmployeeController', 'update', 'admin'],
    ['DELETE', '/api/v1/employees/:id', 'EmployeeController', 'destroy', 'admin'],

    // ============ PAYMENTS (Admin only) ============
    ['GET', '/api/v1/payments', 'PaymentController', 'index', 'admin'],
    ['POST', '/api/v1/payments', 'PaymentController', 'store', 'admin'],
    ['GET', '/api/v1/payments/:id', 'PaymentController', 'show', 'admin'],
    ['PUT', '/api/v1/payments/:id', 'PaymentController', 'update', 'admin'],
    ['DELETE', '/api/v1/payments/:id', 'PaymentController', 'destroy', 'admin'],

    // ============ DISCOUNTS (Admin only) ============
    ['GET', '/api/v1/discounts', 'DiscountController', 'index', 'admin'],
    ['POST', '/api/v1/discounts', 'DiscountController', 'store', 'admin'],
    ['GET', '/api/v1/discounts/:id', 'DiscountController', 'show', 'admin'],
    ['PUT', '/api/v1/discounts/:id', 'DiscountController', 'update', 'admin'],
    ['DELETE', '/api/v1/discounts/:id', 'DiscountController', 'destroy', 'admin'],

    // ============ ACCOUNTS (Admin only) ============
    ['GET', '/api/v1/accounts', 'AccountController', 'index', 'admin'],
    ['POST', '/api/v1/accounts', 'AccountController', 'store', 'admin'],
    ['GET', '/api/v1/accounts/:id', 'AccountController', 'show', 'admin'],
    ['PUT', '/api/v1/accounts/:id', 'AccountController', 'update', 'admin'],
    ['DELETE', '/api/v1/accounts/:id', 'AccountController', 'destroy', 'admin'],

    // ============ SERVICE ORDERS ============
    ['GET', '/api/v1/service-orders', 'ServiceOrderController', 'index', 'guest'],
    ['POST', '/api/v1/service-orders', 'ServiceOrderController', 'store', 'guest'],
    ['GET', '/api/v1/service-orders/total-cost', 'ServiceOrderController', 'getTotalCost', 'guest'],
    ['GET', '/api/v1/service-orders/:id', 'ServiceOrderController', 'show', 'guest'],
    ['PUT', '/api/v1/service-orders/:id', 'ServiceOrderController', 'update', 'guest'],
    ['DELETE', '/api/v1/service-orders/:id', 'ServiceOrderController', 'destroy', 'guest'],
];
?>
