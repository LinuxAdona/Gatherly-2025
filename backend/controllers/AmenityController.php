<?php
/**
 * Amenity Controller
 * Handles amenity operations
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/Logger.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class AmenityController {
    
    /**
     * Get all amenities
     * GET /api/amenities
     */
    public function index() {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $category = $_GET['category'] ?? null;
            
            $query = "SELECT amenity_id, amenity_name, description, icon, category, created_at
                      FROM amenities";
            
            if ($category) {
                $query .= " WHERE category = :category";
            }
            
            $query .= " ORDER BY category, amenity_name";
            
            $stmt = $conn->prepare($query);
            
            if ($category) {
                $stmt->bindParam(':category', $category);
            }
            
            $stmt->execute();
            $amenities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            Response::success(['amenities' => $amenities]);
            
        } catch (Exception $e) {
            Logger::error('Get amenities error', ['error' => $e->getMessage()]);
            Response::serverError('Failed to retrieve amenities');
        }
    }
    
    /**
     * Get single amenity
     * GET /api/amenities/:id
     */
    public function show($id) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $query = "SELECT * FROM amenities WHERE amenity_id = :amenity_id";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':amenity_id', $id);
            $stmt->execute();
            
            $amenity = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$amenity) {
                Response::notFound('Amenity not found');
            }
            
            Response::success(['amenity' => $amenity]);
            
        } catch (Exception $e) {
            Logger::error('Get amenity error', ['error' => $e->getMessage()]);
            Response::serverError('Failed to retrieve amenity');
        }
    }
    
    /**
     * Create new amenity (admin only)
     * POST /api/amenities
     */
    public function create() {
        try {
            AuthMiddleware::requireRole(['admin']);
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate
            $requiredFields = ['amenity_name', 'category'];
            $errors = Validator::required($data, $requiredFields);
            
            if (!empty($errors)) {
                Response::validationError($errors);
            }
            
            $validCategories = ['basic', 'technical', 'catering', 'accessibility', 'other'];
            if (!Validator::enum($data['category'], $validCategories)) {
                Response::validationError(['category' => 'Invalid category']);
            }
            
            $db = new Database();
            $conn = $db->getConnection();
            
            $query = "INSERT INTO amenities (amenity_name, description, icon, category)
                      VALUES (:amenity_name, :description, :icon, :category)";
            
            $stmt = $conn->prepare($query);
            
            $amenityName = Validator::sanitizeString($data['amenity_name']);
            $description = isset($data['description']) ? Validator::sanitizeString($data['description']) : '';
            $icon = isset($data['icon']) ? Validator::sanitizeString($data['icon']) : '';
            $category = $data['category'];
            
            $stmt->bindParam(':amenity_name', $amenityName);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':icon', $icon);
            $stmt->bindParam(':category', $category);
            
            if ($stmt->execute()) {
                $amenityId = $conn->lastInsertId();
                Logger::info('Amenity created', ['amenity_id' => $amenityId]);
                
                Response::success([
                    'amenity' => [
                        'amenity_id' => $amenityId,
                        'amenity_name' => $amenityName
                    ]
                ], 'Amenity created successfully', 201);
            } else {
                Response::serverError('Failed to create amenity');
            }
            
        } catch (Exception $e) {
            Logger::error('Create amenity error', ['error' => $e->getMessage()]);
            Response::serverError('Failed to create amenity');
        }
    }
    
    /**
     * Update amenity (admin only)
     * PUT /api/amenities/:id
     */
    public function update($id) {
        try {
            AuthMiddleware::requireRole(['admin']);
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $db = new Database();
            $conn = $db->getConnection();
            
            // Check if exists
            $checkQuery = "SELECT amenity_id FROM amenities WHERE amenity_id = :amenity_id";
            $stmt = $conn->prepare($checkQuery);
            $stmt->bindParam(':amenity_id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                Response::notFound('Amenity not found');
            }
            
            $query = "UPDATE amenities 
                      SET amenity_name = :amenity_name,
                          description = :description,
                          icon = :icon,
                          category = :category
                      WHERE amenity_id = :amenity_id";
            
            $stmt = $conn->prepare($query);
            
            $stmt->bindParam(':amenity_id', $id);
            $stmt->bindParam(':amenity_name', $data['amenity_name']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':icon', $data['icon']);
            $stmt->bindParam(':category', $data['category']);
            
            if ($stmt->execute()) {
                Logger::info('Amenity updated', ['amenity_id' => $id]);
                Response::success(null, 'Amenity updated successfully');
            } else {
                Response::serverError('Failed to update amenity');
            }
            
        } catch (Exception $e) {
            Logger::error('Update amenity error', ['error' => $e->getMessage()]);
            Response::serverError('Failed to update amenity');
        }
    }
    
    /**
     * Delete amenity (admin only)
     * DELETE /api/amenities/:id
     */
    public function delete($id) {
        try {
            AuthMiddleware::requireRole(['admin']);
            
            $db = new Database();
            $conn = $db->getConnection();
            
            $query = "DELETE FROM amenities WHERE amenity_id = :amenity_id";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':amenity_id', $id);
            
            if ($stmt->execute()) {
                Logger::info('Amenity deleted', ['amenity_id' => $id]);
                Response::success(null, 'Amenity deleted successfully');
            } else {
                Response::serverError('Failed to delete amenity');
            }
            
        } catch (Exception $e) {
            Logger::error('Delete amenity error', ['error' => $e->getMessage()]);
            Response::serverError('Failed to delete amenity');
        }
    }
    
    /**
     * Get amenity categories
     * GET /api/amenities/categories
     */
    public function categories() {
        try {
            $categories = [
                ['value' => 'basic', 'label' => 'Basic Facilities'],
                ['value' => 'technical', 'label' => 'Technical Equipment'],
                ['value' => 'catering', 'label' => 'Catering & Kitchen'],
                ['value' => 'accessibility', 'label' => 'Accessibility Features'],
                ['value' => 'other', 'label' => 'Other']
            ];
            
            Response::success(['categories' => $categories]);
            
        } catch (Exception $e) {
            Logger::error('Get categories error', ['error' => $e->getMessage()]);
            Response::serverError('Failed to retrieve categories');
        }
    }
}
