<?php

/**
 * Amenity Routes
 */

require_once __DIR__ . '/../controllers/AmenityController.php';

function registerAmenityRoutes($router)
{
    $controller = new AmenityController();

    // Public routes
    $router->get('/api/amenities', [$controller, 'index']);
    $router->get('/api/amenities/categories', [$controller, 'categories']);
    $router->get('/api/amenities/:id', [$controller, 'show']);

    // Protected routes (admin only)
    $router->post('/api/amenities', [$controller, 'create']);
    $router->put('/api/amenities/:id', [$controller, 'update']);
    $router->delete('/api/amenities/:id', [$controller, 'delete']);
}
