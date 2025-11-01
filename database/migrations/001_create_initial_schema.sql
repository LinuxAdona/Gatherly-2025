-- Gatherly GEMS Database Schema
-- Version: 1.0
-- Date: 2025-11-01

-- Drop database if exists (use with caution)
-- DROP DATABASE IF EXISTS gatherly_db;

-- Create database
CREATE DATABASE IF NOT EXISTS gatherly_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE gatherly_db;

-- ============================================
-- TABLE: users
-- Stores all user accounts (organizers, venue managers, admins)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'organizer', 'venue_manager') NOT NULL DEFAULT 'organizer',
    profile_image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: venues
-- Stores venue information
-- ============================================
CREATE TABLE IF NOT EXISTS venues (
    venue_id INT AUTO_INCREMENT PRIMARY KEY,
    manager_id INT NOT NULL,
    venue_name VARCHAR(255) NOT NULL,
    description TEXT,
    venue_type ENUM('conference_hall', 'gymnasium', 'pavilion', 'community_center', 'hotel_ballroom', 'outdoor_venue', 'other') NOT NULL,
    capacity INT NOT NULL,
    base_price DECIMAL(10, 2) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state_province VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100) DEFAULT 'Philippines',
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    location_score DECIMAL(3, 2) DEFAULT 0.50 CHECK (location_score >= 0 AND location_score <= 1),
    amenities_score DECIMAL(3, 2) DEFAULT 0.50 CHECK (amenities_score >= 0 AND amenities_score <= 1),
    collaborative_score DECIMAL(3, 2) DEFAULT 0.50 CHECK (collaborative_score >= 0 AND collaborative_score <= 1),
    parking_score DECIMAL(3, 2) DEFAULT 0.50 CHECK (parking_score >= 0 AND parking_score <= 1),
    stage_setup_score DECIMAL(3, 2) DEFAULT 0.50 CHECK (stage_setup_score >= 0 AND stage_setup_score <= 1),
    accessibility_score DECIMAL(3, 2) DEFAULT 0.50 CHECK (accessibility_score >= 0 AND accessibility_score <= 1),
    images JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (manager_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_manager (manager_id),
    INDEX idx_venue_type (venue_type),
    INDEX idx_capacity (capacity),
    INDEX idx_price (base_price),
    INDEX idx_city (city),
    INDEX idx_is_active (is_active),
    FULLTEXT idx_search (venue_name, description, address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: amenities
-- Stores available amenities
-- ============================================
CREATE TABLE IF NOT EXISTS amenities (
    amenity_id INT AUTO_INCREMENT PRIMARY KEY,
    amenity_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    category ENUM('basic', 'technical', 'catering', 'accessibility', 'other') DEFAULT 'basic',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: venue_amenities
-- Many-to-many relationship between venues and amenities
-- ============================================
CREATE TABLE IF NOT EXISTS venue_amenities (
    venue_amenity_id INT AUTO_INCREMENT PRIMARY KEY,
    venue_id INT NOT NULL,
    amenity_id INT NOT NULL,
    is_included BOOLEAN DEFAULT TRUE,
    additional_cost DECIMAL(10, 2) DEFAULT 0.00,
    FOREIGN KEY (venue_id) REFERENCES venues(venue_id) ON DELETE CASCADE,
    FOREIGN KEY (amenity_id) REFERENCES amenities(amenity_id) ON DELETE CASCADE,
    UNIQUE KEY unique_venue_amenity (venue_id, amenity_id),
    INDEX idx_venue (venue_id),
    INDEX idx_amenity (amenity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: venue_availability
-- Tracks available and blocked dates for venues
-- ============================================
CREATE TABLE IF NOT EXISTS venue_availability (
    availability_id INT AUTO_INCREMENT PRIMARY KEY,
    venue_id INT NOT NULL,
    available_date DATE NOT NULL,
    time_slot_start TIME,
    time_slot_end TIME,
    is_available BOOLEAN DEFAULT TRUE,
    price_override DECIMAL(10, 2) NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (venue_id) REFERENCES venues(venue_id) ON DELETE CASCADE,
    UNIQUE KEY unique_venue_date (venue_id, available_date, time_slot_start),
    INDEX idx_venue_date (venue_id, available_date),
    INDEX idx_available (is_available)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: events
-- Stores event details
-- ============================================
CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    organizer_id INT NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    event_type ENUM('wedding', 'birthday', 'corporate', 'concert', 'conference', 'sports', 'exhibition', 'other') NOT NULL,
    description TEXT,
    expected_attendees INT NOT NULL,
    budget DECIMAL(10, 2) NOT NULL,
    event_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    required_amenities JSON,
    special_requirements TEXT,
    status ENUM('planning', 'venue_searching', 'booked', 'completed', 'cancelled') DEFAULT 'planning',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_organizer (organizer_id),
    INDEX idx_event_date (event_date),
    INDEX idx_status (status),
    INDEX idx_event_type (event_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: bookings
-- Stores confirmed venue bookings
-- ============================================
CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    venue_id INT NOT NULL,
    organizer_id INT NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    total_price DECIMAL(10, 2) NOT NULL,
    booking_status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'partial', 'paid', 'refunded') DEFAULT 'unpaid',
    payment_method VARCHAR(50),
    transaction_id VARCHAR(255),
    deposit_amount DECIMAL(10, 2) DEFAULT 0.00,
    balance_amount DECIMAL(10, 2),
    confirmation_code VARCHAR(50) UNIQUE,
    notes TEXT,
    cancelled_at TIMESTAMP NULL,
    cancellation_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE RESTRICT,
    FOREIGN KEY (venue_id) REFERENCES venues(venue_id) ON DELETE RESTRICT,
    FOREIGN KEY (organizer_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_event (event_id),
    INDEX idx_venue (venue_id),
    INDEX idx_organizer (organizer_id),
    INDEX idx_booking_date (booking_date),
    INDEX idx_status (booking_status),
    INDEX idx_payment_status (payment_status),
    UNIQUE KEY unique_venue_booking (venue_id, booking_date, start_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: price_adjustments
-- Dynamic pricing rules
-- ============================================
CREATE TABLE IF NOT EXISTS price_adjustments (
    adjustment_id INT AUTO_INCREMENT PRIMARY KEY,
    venue_id INT NOT NULL,
    adjustment_type ENUM('seasonal', 'day_of_week', 'demand_based', 'special_event', 'custom') NOT NULL,
    season VARCHAR(50),
    day_of_week VARCHAR(20),
    start_date DATE,
    end_date DATE,
    multiplier DECIMAL(3, 2) NOT NULL DEFAULT 1.00,
    fixed_amount DECIMAL(10, 2) DEFAULT 0.00,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (venue_id) REFERENCES venues(venue_id) ON DELETE CASCADE,
    INDEX idx_venue (venue_id),
    INDEX idx_type (adjustment_type),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: contracts
-- Auto-generated venue booking contracts
-- ============================================
CREATE TABLE IF NOT EXISTS contracts (
    contract_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL UNIQUE,
    contract_number VARCHAR(100) UNIQUE NOT NULL,
    contract_template TEXT NOT NULL,
    generated_pdf_path VARCHAR(255),
    organizer_signature TEXT,
    manager_signature TEXT,
    organizer_signed_at TIMESTAMP NULL,
    manager_signed_at TIMESTAMP NULL,
    status ENUM('draft', 'pending_signatures', 'fully_signed', 'cancelled') DEFAULT 'draft',
    terms_conditions TEXT,
    cancellation_policy TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE,
    INDEX idx_booking (booking_id),
    INDEX idx_status (status),
    INDEX idx_contract_number (contract_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: cf_interactions
-- Collaborative filtering data (user-venue interactions)
-- ============================================
CREATE TABLE IF NOT EXISTS cf_interactions (
    interaction_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    venue_id INT NOT NULL,
    interaction_type ENUM('view', 'inquiry', 'booking', 'rating', 'favorite') NOT NULL,
    interaction_value DECIMAL(3, 2) DEFAULT 1.00,
    event_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (venue_id) REFERENCES venues(venue_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_venue (venue_id),
    INDEX idx_type (interaction_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: messages
-- Chat messages between organizers and venue managers
-- ============================================
CREATE TABLE IF NOT EXISTS messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    booking_id INT,
    message_text TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    attachment_path VARCHAR(255),
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE SET NULL,
    INDEX idx_sender (sender_id),
    INDEX idx_receiver (receiver_id),
    INDEX idx_booking (booking_id),
    INDEX idx_sent_at (sent_at),
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: reviews
-- Venue reviews and ratings
-- ============================================
CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    venue_id INT NOT NULL,
    user_id INT NOT NULL,
    booking_id INT,
    rating DECIMAL(2, 1) NOT NULL CHECK (rating >= 1.0 AND rating <= 5.0),
    review_text TEXT,
    would_recommend BOOLEAN,
    response_text TEXT,
    response_at TIMESTAMP NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (venue_id) REFERENCES venues(venue_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_venue_review (user_id, venue_id, booking_id),
    INDEX idx_venue (venue_id),
    INDEX idx_user (user_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: notifications
-- System notifications for users
-- ============================================
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    notification_type ENUM('booking_confirmed', 'booking_cancelled', 'payment_received', 'message_received', 'contract_signed', 'review_posted', 'system') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    related_id INT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_type (notification_type),
    INDEX idx_is_read (is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: analytics_logs
-- Tracks venue views, searches, and conversions
-- ============================================
CREATE TABLE IF NOT EXISTS analytics_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    venue_id INT,
    user_id INT,
    action_type ENUM('search', 'view', 'inquiry', 'booking_started', 'booking_completed') NOT NULL,
    search_criteria JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (venue_id) REFERENCES venues(venue_id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_venue (venue_id),
    INDEX idx_user (user_id),
    INDEX idx_action (action_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Create initial admin user
-- Password: Admin@123 (hashed with bcrypt)
-- ============================================
INSERT INTO users (email, password_hash, full_name, role, is_active, email_verified)
VALUES (
    'admin@gatherly.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'System Administrator',
    'admin',
    TRUE,
    TRUE
);

-- End of schema
