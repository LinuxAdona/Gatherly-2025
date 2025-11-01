<?php

/**
 * Venue Model
 * Handles venue data operations
 */

require_once __DIR__ . '/../config/database.php';

class Venue
{

    private $conn;
    private $table = 'venues';

    // Venue properties
    public $venue_id;
    public $manager_id;
    public $venue_name;
    public $description;
    public $venue_type;
    public $capacity;
    public $base_price;
    public $address;
    public $city;
    public $state_province;
    public $postal_code;
    public $country;
    public $latitude;
    public $longitude;
    public $location_score;
    public $amenities_score;
    public $collaborative_score;
    public $parking_score;
    public $stage_setup_score;
    public $accessibility_score;
    public $images;
    public $is_active;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create new venue
     */
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (manager_id, venue_name, description, venue_type, capacity, base_price,
                   address, city, state_province, postal_code, country,
                   latitude, longitude, location_score, amenities_score, collaborative_score,
                   parking_score, stage_setup_score, accessibility_score, images, is_active)
                  VALUES 
                  (:manager_id, :venue_name, :description, :venue_type, :capacity, :base_price,
                   :address, :city, :state_province, :postal_code, :country,
                   :latitude, :longitude, :location_score, :amenities_score, :collaborative_score,
                   :parking_score, :stage_setup_score, :accessibility_score, :images, :is_active)";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':manager_id', $this->manager_id);
        $stmt->bindParam(':venue_name', $this->venue_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':venue_type', $this->venue_type);
        $stmt->bindParam(':capacity', $this->capacity);
        $stmt->bindParam(':base_price', $this->base_price);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':state_province', $this->state_province);
        $stmt->bindParam(':postal_code', $this->postal_code);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':latitude', $this->latitude);
        $stmt->bindParam(':longitude', $this->longitude);
        $stmt->bindParam(':location_score', $this->location_score);
        $stmt->bindParam(':amenities_score', $this->amenities_score);
        $stmt->bindParam(':collaborative_score', $this->collaborative_score);
        $stmt->bindParam(':parking_score', $this->parking_score);
        $stmt->bindParam(':stage_setup_score', $this->stage_setup_score);
        $stmt->bindParam(':accessibility_score', $this->accessibility_score);
        $stmt->bindParam(':images', $this->images);
        $stmt->bindParam(':is_active', $this->is_active);

        if ($stmt->execute()) {
            $this->venue_id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Get venue by ID
     */
    public function findById($venueId)
    {
        $query = "SELECT v.*, 
                         u.full_name as manager_name, u.email as manager_email, u.phone as manager_phone,
                         GROUP_CONCAT(DISTINCT a.amenity_name) as amenities
                  FROM " . $this->table . " v
                  LEFT JOIN users u ON v.manager_id = u.user_id
                  LEFT JOIN venue_amenities va ON v.venue_id = va.venue_id
                  LEFT JOIN amenities a ON va.amenity_id = a.amenity_id
                  WHERE v.venue_id = :venue_id
                  GROUP BY v.venue_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':venue_id', $venueId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Search venues with filters
     */
    public function search($filters = [], $limit = 20, $offset = 0)
    {
        $query = "SELECT v.*, 
                         u.full_name as manager_name,
                         (SELECT COUNT(*) FROM venue_availability va 
                          WHERE va.venue_id = v.venue_id 
                          AND va.available_date >= CURDATE() 
                          AND va.is_available = TRUE) as available_dates_count
                  FROM " . $this->table . " v
                  LEFT JOIN users u ON v.manager_id = u.user_id
                  WHERE v.is_active = TRUE";

        $params = [];

        // Add filters
        if (!empty($filters['venue_type'])) {
            $query .= " AND v.venue_type = :venue_type";
            $params[':venue_type'] = $filters['venue_type'];
        }

        if (!empty($filters['city'])) {
            $query .= " AND v.city LIKE :city";
            $params[':city'] = '%' . $filters['city'] . '%';
        }

        if (!empty($filters['min_capacity'])) {
            $query .= " AND v.capacity >= :min_capacity";
            $params[':min_capacity'] = $filters['min_capacity'];
        }

        if (!empty($filters['max_capacity'])) {
            $query .= " AND v.capacity <= :max_capacity";
            $params[':max_capacity'] = $filters['max_capacity'];
        }

        if (!empty($filters['min_price'])) {
            $query .= " AND v.base_price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $query .= " AND v.base_price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }

        if (!empty($filters['search'])) {
            $query .= " AND (v.venue_name LIKE :search OR v.description LIKE :search OR v.address LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        // Sorting
        $orderBy = $filters['sort_by'] ?? 'venue_name';
        $orderDir = $filters['sort_dir'] ?? 'ASC';

        $allowedSort = ['venue_name', 'base_price', 'capacity', 'city', 'created_at'];
        if (!in_array($orderBy, $allowedSort)) {
            $orderBy = 'venue_name';
        }

        $query .= " ORDER BY v.$orderBy $orderDir";
        $query .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        // Bind filter parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // Bind pagination
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total count for pagination
     */
    public function getCount($filters = [])
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " v WHERE v.is_active = TRUE";

        $params = [];

        if (!empty($filters['venue_type'])) {
            $query .= " AND v.venue_type = :venue_type";
            $params[':venue_type'] = $filters['venue_type'];
        }

        if (!empty($filters['city'])) {
            $query .= " AND v.city LIKE :city";
            $params[':city'] = '%' . $filters['city'] . '%';
        }

        if (!empty($filters['min_capacity'])) {
            $query .= " AND v.capacity >= :min_capacity";
            $params[':min_capacity'] = $filters['min_capacity'];
        }

        if (!empty($filters['max_capacity'])) {
            $query .= " AND v.capacity <= :max_capacity";
            $params[':max_capacity'] = $filters['max_capacity'];
        }

        if (!empty($filters['min_price'])) {
            $query .= " AND v.base_price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $query .= " AND v.base_price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }

        if (!empty($filters['search'])) {
            $query .= " AND (v.venue_name LIKE :search OR v.description LIKE :search OR v.address LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'];
    }

    /**
     * Update venue
     */
    public function update()
    {
        $query = "UPDATE " . $this->table . " 
                  SET venue_name = :venue_name,
                      description = :description,
                      venue_type = :venue_type,
                      capacity = :capacity,
                      base_price = :base_price,
                      address = :address,
                      city = :city,
                      state_province = :state_province,
                      postal_code = :postal_code,
                      country = :country,
                      latitude = :latitude,
                      longitude = :longitude,
                      location_score = :location_score,
                      amenities_score = :amenities_score,
                      parking_score = :parking_score,
                      stage_setup_score = :stage_setup_score,
                      accessibility_score = :accessibility_score,
                      images = :images,
                      updated_at = NOW()
                  WHERE venue_id = :venue_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':venue_id', $this->venue_id);
        $stmt->bindParam(':venue_name', $this->venue_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':venue_type', $this->venue_type);
        $stmt->bindParam(':capacity', $this->capacity);
        $stmt->bindParam(':base_price', $this->base_price);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':state_province', $this->state_province);
        $stmt->bindParam(':postal_code', $this->postal_code);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':latitude', $this->latitude);
        $stmt->bindParam(':longitude', $this->longitude);
        $stmt->bindParam(':location_score', $this->location_score);
        $stmt->bindParam(':amenities_score', $this->amenities_score);
        $stmt->bindParam(':parking_score', $this->parking_score);
        $stmt->bindParam(':stage_setup_score', $this->stage_setup_score);
        $stmt->bindParam(':accessibility_score', $this->accessibility_score);
        $stmt->bindParam(':images', $this->images);

        return $stmt->execute();
    }

    /**
     * Delete venue (soft delete - set is_active to false)
     */
    public function delete($venueId)
    {
        $query = "UPDATE " . $this->table . " SET is_active = FALSE WHERE venue_id = :venue_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':venue_id', $venueId);

        return $stmt->execute();
    }

    /**
     * Get venues by manager
     */
    public function getByManager($managerId, $includeInactive = false)
    {
        $query = "SELECT v.*, 
                         (SELECT COUNT(*) FROM bookings b 
                          WHERE b.venue_id = v.venue_id 
                          AND b.booking_status IN ('confirmed', 'pending')) as active_bookings_count
                  FROM " . $this->table . " v
                  WHERE v.manager_id = :manager_id";

        if (!$includeInactive) {
            $query .= " AND v.is_active = TRUE";
        }

        $query .= " ORDER BY v.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':manager_id', $managerId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get venue types
     */
    public function getVenueTypes()
    {
        return [
            'conference_hall',
            'gymnasium',
            'pavilion',
            'community_center',
            'hotel_ballroom',
            'outdoor_venue',
            'other'
        ];
    }

    /**
     * Check if venue belongs to manager
     */
    public function belongsToManager($venueId, $managerId)
    {
        $query = "SELECT venue_id FROM " . $this->table . " 
                  WHERE venue_id = :venue_id AND manager_id = :manager_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':venue_id', $venueId);
        $stmt->bindParam(':manager_id', $managerId);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
