<?php

/**
 * Venue Routes
 */

require_once __DIR__ . '/../controllers/VenueController.php';

function registerVenueRoutes($router)
{
    $venueController = new VenueController();

    // Public routes
    $router->get('/api/venues', [$venueController, 'index']);
    $router->get('/api/venues/:id', [$venueController, 'show']);

    // Protected routes (venue managers)
    $router->get('/api/venues/my/list', [$venueController, 'myVenues']);
    $router->post('/api/venues', [$venueController, 'create']);
    $router->put('/api/venues/:id', [$venueController, 'update']);
    $router->delete('/api/venues/:id', [$venueController, 'delete']);
}
