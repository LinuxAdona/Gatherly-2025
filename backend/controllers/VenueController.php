<?php
/**
 * Venue Controller
 * Handles venue CRUD operations and search
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Venue.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/Logger.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class VenueController {
    
    /**
     * Get all venues with optional filters
     * GET /api/venues
     */
    public function index() {
        try {
            $venue = new Venue();
            
            // Get query parameters
            $filters = [
                'venue_type' => $_GET['venue_type'] ?? null,
                'city' => $_GET['city'] ?? null,
                'min_capacity' => $_GET['min_capacity'] ?? null,
                'max_capacity' => $_GET['max_capacity'] ?? null,
                'min_price' => $_GET['min_price'] ?? null,
                'max_price' => $_GET['max_price'] ?? null,
                'search' => $_GET['search'] ?? null,
                'sort_by' => $_GET['sort_by'] ?? 'venue_name',
                'sort_dir' => $_GET['sort_dir'] ?? 'ASC'
            ];
            
            // Pagination
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
            $offset = ($page - 1) * $limit;
            
            // Get venues
            $venues = $venue->search($filters, $limit, $offset);
            $total = $venue->getCount($filters);
            
            Response::success([
                'venues' => $venues,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ], 'Venues retrieved successfully');
            
        } catch (Exception $e) {
            Logger::error('Get venues error', ['error' => $e->getMessage()]);
            Response::serverError('Failed to retrieve venues');
        }
    }
    
    /**
     * Get single venue by ID
     * GET /api/venues/:id
     */
    public function show($id) {
        try {
            $venue = new Venue();
            $venueData = $venue->findById($id);
            
            if (!$venueData) {
                Response::notFound('Venue not found');
            }
            
            // Parse JSON images
            if ($venueData['images']) {
                $venueData['images'] = json_decode($venueData['images'], true);
            }
            
            // Get amenities details
            $venueData['amenities_list'] = $this->getVenueAmenities($id);
            
            // Get availability summary for next 30 days
            $venueData['availability_summary'] = $this->getAvailabilitySummary($id);
            
            Response::success(['venue' => $venueData]);
            
        } catch (Exception $e) {
            Logger::error('Get venue error', ['error' => $e->getMessage(), 'venue_id' => $id]);
            Response::serverError('Failed to retrieve venue');
        }
    }
    
    /**
     * Create new venue
     * POST /api/venues
     */
    public function create() {
        try {
            // Only venue managers can create venues
            $currentUser = AuthMiddleware::requireRole(['venue_manager', 'admin']);
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $requiredFields = ['venue_name', 'venue_type', 'capacity', 'base_price', 'address', 'city'];
            $errors = Validator::required($data, $requiredFields);
            
            if (!empty($errors)) {
                Response::validationError($errors);
            }
            
            // Validate venue type
            $venue = new Venue();
            $validTypes = $venue->getVenueTypes();
            if (!Validator::enum($data['venue_type'], $validTypes)) {
                Response::validationError(['venue_type' => 'Invalid venue type']);
            }
            
            // Validate capacity and price
            if (!Validator::positive($data['capacity'])) {
                Response::validationError(['capacity' => 'Capacity must be a positive number']);
            }
            
            if (!Validator::positive($data['base_price'])) {
                Response::validationError(['base_price' => 'Base price must be a positive number']);
            }
            
            // Set venue properties
            $venue->manager_id = $currentUser['role'] === 'admin' && isset($data['manager_id']) 
                ? $data['manager_id'] 
                : $currentUser['user_id'];
            $venue->venue_name = Validator::sanitizeString($data['venue_name']);
            $venue->description = isset($data['description']) ? Validator::sanitizeString($data['description']) : '';
            $venue->venue_type = $data['venue_type'];
            $venue->capacity = (int)$data['capacity'];
            $venue->base_price = (float)$data['base_price'];
            $venue->address = Validator::sanitizeString($data['address']);
            $venue->city = Validator::sanitizeString($data['city']);
            $venue->state_province = isset($data['state_province']) ? Validator::sanitizeString($data['state_province']) : '';
            $venue->postal_code = isset($data['postal_code']) ? Validator::sanitizeString($data['postal_code']) : '';
            $venue->country = isset($data['country']) ? Validator::sanitizeString($data['country']) : 'Philippines';
            $venue->latitude = isset($data['latitude']) ? (float)$data['latitude'] : null;
            $venue->longitude = isset($data['longitude']) ? (float)$data['longitude'] : null;
            
            // Scoring (default values, can be calculated later)
            $venue->location_score = isset($data['location_score']) ? (float)$data['location_score'] : 0.50;
            $venue->amenities_score = isset($data['amenities_score']) ? (float)$data['amenities_score'] : 0.50;
            $venue->collaborative_score = 0.50;
            $venue->parking_score = isset($data['parking_score']) ? (float)$data['parking_score'] : 0.50;
            $venue->stage_setup_score = isset($data['stage_setup_score']) ? (float)$data['stage_setup_score'] : 0.50;
            $venue->accessibility_score = isset($data['accessibility_score']) ? (float)$data['accessibility_score'] : 0.50;
            
            // Images (JSON array)
            $venue->images = isset($data['images']) ? json_encode($data['images']) : json_encode([]);
            $venue->is_active = true;
            
            // Create venue
            if ($venue->create()) {
                // Add amenities if provided
                if (!empty($data['amenities'])) {
                    $this->addVenueAmenities($venue->venue_id, $data['amenities']);
                }
                
                Logger::info('Venue created', ['venue_id' => $venue->venue_id, 'user_id' => $currentUser['user_id']]);
                
                Response::success([
                    'venue' => [
                        'venue_id' => $venue->venue_id,
                        'venue_name' => $venue->venue_name,
                        'venue_type' => $venue->venue_type
                    ]
                ], 'Venue created successfully', 201);
            } else {
                Response::serverError('Failed to create venue');
            }
            
        } catch (Exception $e) {
            Logger::error('Create venue error', ['error' => $e->getMessage()]);
            Response::serverError('Failed to create venue');
        }
    }
    
    /**
     * Update venue
     * PUT /api/venues/:id
     */
    public function update($id) {
        try {
            $currentUser = AuthMiddleware::requireRole(['venue_manager', 'admin']);
            
            $venue = new Venue();
            $existingVenue = $venue->findById($id);
            
            if (!$existingVenue) {
                Response::notFound('Venue not found');
            }
            
            // Check ownership (unless admin)
            if ($currentUser['role'] !== 'admin') {
                if (!$venue->belongsToManager($id, $currentUser['user_id'])) {
                    Response::forbidden('You do not have permission to update this venue');
                }
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Update fields
            $venue->venue_id = $id;
            $venue->venue_name = isset($data['venue_name']) ? Validator::sanitizeString($data['venue_name']) : $existingVenue['venue_name'];
            $venue->description = isset($data['description']) ? Validator::sanitizeString($data['description']) : $existingVenue['description'];
            $venue->venue_type = isset($data['venue_type']) ? $data['venue_type'] : $existingVenue['venue_type'];
            $venue->capacity = isset($data['capacity']) ? (int)$data['capacity'] : $existingVenue['capacity'];
            $venue->base_price = isset($data['base_price']) ? (float)$data['base_price'] : $existingVenue['base_price'];
            $venue->address = isset($data['address']) ? Validator::sanitizeString($data['address']) : $existingVenue['address'];
            $venue->city = isset($data['city']) ? Validator::sanitizeString($data['city']) : $existingVenue['city'];
            $venue->state_province = isset($data['state_province']) ? Validator::sanitizeString($data['state_province']) : $existingVenue['state_province'];
            $venue->postal_code = isset($data['postal_code']) ? Validator::sanitizeString($data['postal_code']) : $existingVenue['postal_code'];
            $venue->country = isset($data['country']) ? Validator::sanitizeString($data['country']) : $existingVenue['country'];
            $venue->latitude = isset($data['latitude']) ? (float)$data['latitude'] : $existingVenue['latitude'];
            $venue->longitude = isset($data['longitude']) ? (float)$data['longitude'] : $existingVenue['longitude'];
            $venue->location_score = isset($data['location_score']) ? (float)$data['location_score'] : $existingVenue['location_score'];
            $venue->amenities_score = isset($data['amenities_score']) ? (float)$data['amenities_score'] : $existingVenue['amenities_score'];
            $venue->parking_score = isset($data['parking_score']) ? (float)$data['parking_score'] : $existingVenue['parking_score'];
            $venue->stage_setup_score = isset($data['stage_setup_score']) ? (float)$data['stage_setup_score'] : $existingVenue['stage_setup_score'];
            $venue->accessibility_score = isset($data['accessibility_score']) ? (float)$data['accessibility_score'] : $existingVenue['accessibility_score'];
            $venue->images = isset($data['images']) ? json_encode($data['images']) : $existingVenue['images'];
            
            if ($venue->update()) {
                // Update amenities if provided
                if (isset($data['amenities'])) {
                    $this->updateVenueAmenities($id, $data['amenities']);
                }
                
                Logger::info('Venue updated', ['venue_id' => $id, 'user_id' => $currentUser['user_id']]);
                Response::success(null, 'Venue updated successfully');
            } else {
                Response::serverError('Failed to update venue');
            }
            
        } catch (Exception $e) {
            Logger::error('Update venue error', ['error' => $e->getMessage(), 'venue_id' => $id]);
            Response::serverError('Failed to update venue');
        }
    }
    
    /**
     * Delete venue
     * DELETE /api/venues/:id
     */
    public function delete($id) {
        try {
            $currentUser = AuthMiddleware::requireRole(['venue_manager', 'admin']);
            
            $venue = new Venue();
            $existingVenue = $venue->findById($id);
            
            if (!$existingVenue) {
                Response::notFound('Venue not found');
            }
            
            // Check ownership (unless admin)
            if ($currentUser['role'] !== 'admin') {
                if (!$venue->belongsToManager($id, $currentUser['user_id'])) {
                    Response::forbidden('You do not have permission to delete this venue');
                }
            }
            
            if ($venue->delete($id)) {
                Logger::info('Venue deleted', ['venue_id' => $id, 'user_id' => $currentUser['user_id']]);
                Response::success(null, 'Venue deleted successfully');
            } else {
                Response::serverError('Failed to delete venue');
            }
            
        } catch (Exception $e) {
            Logger::error('Delete venue error', ['error' => $e->getMessage(), 'venue_id' => $id]);
            Response::serverError('Failed to delete venue');
        }
    }
    
    /**
     * Get venues by current manager
     * GET /api/venues/my-venues
     */
    public function myVenues() {
        try {
            $currentUser = AuthMiddleware::requireRole(['venue_manager']);
            
            $venue = new Venue();
            $venues = $venue->getByManager($currentUser['user_id']);
            
            Response::success(['venues' => $venues]);
            
        } catch (Exception $e) {
            Logger::error('Get my venues error', ['error' => $e->getMessage()]);
            Response::serverError('Failed to retrieve venues');
        }
    }
    
    /**
     * Helper: Get venue amenities
     */
    private function getVenueAmenities($venueId) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $query = "SELECT a.amenity_id, a.amenity_name, a.description, a.icon, a.category,
                             va.is_included, va.additional_cost
                      FROM venue_amenities va
                      INNER JOIN amenities a ON va.amenity_id = a.amenity_id
                      WHERE va.venue_id = :venue_id
                      ORDER BY a.category, a.amenity_name";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':venue_id', $venueId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Helper: Add venue amenities
     */
    private function addVenueAmenities($venueId, $amenities) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $query = "INSERT INTO venue_amenities (venue_id, amenity_id, is_included, additional_cost)
                      VALUES (:venue_id, :amenity_id, :is_included, :additional_cost)";
            
            $stmt = $conn->prepare($query);
            
            foreach ($amenities as $amenity) {
                $stmt->execute([
                    ':venue_id' => $venueId,
                    ':amenity_id' => $amenity['amenity_id'],
                    ':is_included' => $amenity['is_included'] ?? true,
                    ':additional_cost' => $amenity['additional_cost'] ?? 0
                ]);
            }
            
            return true;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Helper: Update venue amenities
     */
    private function updateVenueAmenities($venueId, $amenities) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            // Delete existing amenities
            $deleteQuery = "DELETE FROM venue_amenities WHERE venue_id = :venue_id";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->execute([':venue_id' => $venueId]);
            
            // Add new amenities
            return $this->addVenueAmenities($venueId, $amenities);
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Helper: Get availability summary
     */
    private function getAvailabilitySummary($venueId) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $query = "SELECT 
                        COUNT(*) as total_days,
                        SUM(CASE WHEN is_available = TRUE THEN 1 ELSE 0 END) as available_days
                      FROM venue_availability
                      WHERE venue_id = :venue_id
                      AND available_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':venue_id', $venueId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return ['total_days' => 0, 'available_days' => 0];
        }
    }
}
