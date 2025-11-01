-- Seed Data for Gatherly GEMS
-- Includes sample venues, amenities, and test data

USE gatherly_db;

-- ============================================
-- Amenities
-- ============================================
INSERT INTO amenities (amenity_name, description, icon, category) VALUES
('WiFi', 'High-speed wireless internet', 'wifi', 'technical'),
('Sound System', 'Professional audio equipment', 'speaker', 'technical'),
('Projector & Screen', 'HD projector with large screen', 'projector', 'technical'),
('Stage', 'Elevated performance stage', 'stage', 'basic'),
('Parking', 'On-site parking facility', 'parking', 'basic'),
('Catering Service', 'In-house catering available', 'utensils', 'catering'),
('Kitchen Facilities', 'Full kitchen for food preparation', 'kitchen', 'catering'),
('Air Conditioning', 'Climate control system', 'snowflake', 'basic'),
('Tables & Chairs', 'Event furniture included', 'chair', 'basic'),
('Lighting System', 'Professional stage lighting', 'lightbulb', 'technical'),
('Dance Floor', 'Dedicated dance area', 'music', 'basic'),
('Wheelchair Access', 'ADA compliant accessibility', 'wheelchair', 'accessibility'),
('Restrooms', 'Clean bathroom facilities', 'restroom', 'basic'),
('Security', '24/7 security personnel', 'shield', 'basic'),
('Dressing Rooms', 'Private preparation areas', 'door', 'basic');

-- ============================================
-- Sample Venue Managers
-- Password for all: Manager@123
-- ============================================
INSERT INTO users (email, password_hash, full_name, phone, role, email_verified) VALUES
('manager1@venues.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan Dela Cruz', '+639171234567', 'venue_manager', TRUE),
('manager2@venues.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maria Santos', '+639187654321', 'venue_manager', TRUE),
('manager3@venues.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pedro Reyes', '+639191112233', 'venue_manager', TRUE);

-- ============================================
-- Sample Event Organizers
-- Password for all: Organizer@123
-- ============================================
INSERT INTO users (email, password_hash, full_name, phone, role, email_verified) VALUES
('organizer1@events.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana Garcia', '+639161234567', 'organizer', TRUE),
('organizer2@events.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos Mendoza', '+639177654321', 'organizer', TRUE);

-- ============================================
-- Sample Venues
-- ============================================
INSERT INTO venues (
    manager_id, venue_name, description, venue_type, capacity, base_price,
    address, city, state_province, postal_code, country,
    latitude, longitude,
    location_score, amenities_score, collaborative_score,
    parking_score, stage_setup_score, accessibility_score,
    images, is_active
) VALUES
-- Grand Pavilion
(2, 'Grand Pavilion', 'Elegant pavilion perfect for weddings and large gatherings with beautiful garden views.', 
'pavilion', 200, 98000.00,
'123 Garden Street, BGC', 'Taguig', 'Metro Manila', '1630', 'Philippines',
14.5547, 121.0469,
0.88, 0.95, 0.92, 0.90, 0.95, 0.90,
'["pavilion1.jpg", "pavilion2.jpg", "pavilion3.jpg"]', TRUE),

-- City Gymnasium
(2, 'City Gymnasium', 'Spacious gymnasium ideal for sports events, exhibitions, and large corporate functions.',
'gymnasium', 300, 110000.00,
'456 Sports Complex Road, Quezon City', 'Quezon City', 'Metro Manila', '1100', 'Philippines',
14.6760, 121.0437,
0.80, 0.85, 0.89, 0.85, 0.90, 0.88,
'["gym1.jpg", "gym2.jpg"]', TRUE),

-- Community Hall
(3, 'Community Hall', 'Cozy community center suitable for birthday parties and small gatherings.',
'community_center', 120, 75000.00,
'789 Barangay Road, Makati', 'Makati', 'Metro Manila', '1200', 'Philippines',
14.5547, 121.0244,
0.75, 0.80, 0.87, 0.80, 0.85, 0.82,
'["hall1.jpg", "hall2.jpg"]', TRUE),

-- Hotel Grand Ballroom
(2, 'Hotel Grand Ballroom', 'Luxurious hotel ballroom with world-class amenities for prestigious events.',
'hotel_ballroom', 250, 150000.00,
'321 Luxury Avenue, Makati', 'Makati', 'Metro Manila', '1210', 'Philippines',
14.5505, 121.0330,
0.95, 0.98, 0.90, 0.95, 0.98, 0.95,
'["ballroom1.jpg", "ballroom2.jpg", "ballroom3.jpg"]', TRUE),

-- Tech Conference Center
(3, 'Tech Conference Center', 'Modern conference facility with state-of-the-art technology for corporate events.',
'conference_hall', 180, 125000.00,
'555 Innovation Drive, BGC', 'Taguig', 'Metro Manila', '1634', 'Philippines',
14.5513, 121.0508,
0.90, 0.95, 0.85, 0.90, 0.88, 0.92,
'["conference1.jpg", "conference2.jpg"]', TRUE),

-- Garden Vista
(4, 'Garden Vista', 'Outdoor venue with stunning garden landscapes perfect for weddings and receptions.',
'outdoor_venue', 150, 85000.00,
'777 Nature Park, Antipolo', 'Antipolo', 'Rizal', '1870', 'Philippines',
14.5862, 121.1755,
0.70, 0.88, 0.80, 0.75, 0.80, 0.75,
'["garden1.jpg", "garden2.jpg"]', TRUE);

-- ============================================
-- Venue Amenities Mapping
-- ============================================
-- Grand Pavilion amenities
INSERT INTO venue_amenities (venue_id, amenity_id, is_included, additional_cost) VALUES
(1, 1, TRUE, 0), (1, 2, TRUE, 0), (1, 5, TRUE, 0), (1, 6, FALSE, 15000),
(1, 8, TRUE, 0), (1, 9, TRUE, 0), (1, 11, TRUE, 0), (1, 12, TRUE, 0),
(1, 13, TRUE, 0), (1, 14, TRUE, 0);

-- City Gymnasium amenities
INSERT INTO venue_amenities (venue_id, amenity_id, is_included, additional_cost) VALUES
(2, 1, TRUE, 0), (2, 2, TRUE, 0), (2, 3, TRUE, 0), (2, 5, TRUE, 0),
(2, 4, FALSE, 8000), (2, 8, TRUE, 0), (2, 9, FALSE, 5000),
(2, 12, TRUE, 0), (2, 13, TRUE, 0), (2, 14, TRUE, 0);

-- Community Hall amenities
INSERT INTO venue_amenities (venue_id, amenity_id, is_included, additional_cost) VALUES
(3, 1, TRUE, 0), (3, 2, TRUE, 0), (3, 5, TRUE, 0), (3, 8, TRUE, 0),
(3, 9, TRUE, 0), (3, 13, TRUE, 0);

-- Hotel Grand Ballroom amenities
INSERT INTO venue_amenities (venue_id, amenity_id, is_included, additional_cost) VALUES
(4, 1, TRUE, 0), (4, 2, TRUE, 0), (4, 3, TRUE, 0), (4, 4, TRUE, 0),
(4, 5, TRUE, 0), (4, 6, TRUE, 0), (4, 7, TRUE, 0), (4, 8, TRUE, 0),
(4, 9, TRUE, 0), (4, 10, TRUE, 0), (4, 11, TRUE, 0), (4, 12, TRUE, 0),
(4, 13, TRUE, 0), (4, 14, TRUE, 0), (4, 15, TRUE, 0);

-- Tech Conference Center amenities
INSERT INTO venue_amenities (venue_id, amenity_id, is_included, additional_cost) VALUES
(5, 1, TRUE, 0), (5, 2, TRUE, 0), (5, 3, TRUE, 0), (5, 5, TRUE, 0),
(5, 8, TRUE, 0), (5, 9, TRUE, 0), (5, 10, TRUE, 0), (5, 12, TRUE, 0),
(5, 13, TRUE, 0), (5, 14, TRUE, 0);

-- Garden Vista amenities
INSERT INTO venue_amenities (venue_id, amenity_id, is_included, additional_cost) VALUES
(6, 1, FALSE, 3000), (6, 2, FALSE, 8000), (6, 5, TRUE, 0), (6, 6, FALSE, 20000),
(6, 9, FALSE, 8000), (6, 13, TRUE, 0), (6, 14, TRUE, 0);

-- ============================================
-- Venue Availability (Next 90 days)
-- Simplified approach without stored procedures
-- ============================================

-- Venue 1 - Grand Pavilion (90 days availability with some blocked dates)
INSERT INTO venue_availability (venue_id, available_date, time_slot_start, time_slot_end, is_available)
SELECT 1, DATE_ADD(CURDATE(), INTERVAL n DAY), '08:00:00', '23:00:00', TRUE
FROM (
    SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL
    SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL
    SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL
    SELECT 30 UNION ALL SELECT 31 UNION ALL SELECT 32 UNION ALL SELECT 33 UNION ALL SELECT 34 UNION ALL SELECT 35 UNION ALL SELECT 36 UNION ALL SELECT 37 UNION ALL SELECT 38 UNION ALL SELECT 39 UNION ALL
    SELECT 40 UNION ALL SELECT 41 UNION ALL SELECT 42 UNION ALL SELECT 43 UNION ALL SELECT 44 UNION ALL SELECT 45 UNION ALL SELECT 46 UNION ALL SELECT 47 UNION ALL SELECT 48 UNION ALL SELECT 49 UNION ALL
    SELECT 50 UNION ALL SELECT 51 UNION ALL SELECT 52 UNION ALL SELECT 53 UNION ALL SELECT 54 UNION ALL SELECT 55 UNION ALL SELECT 56 UNION ALL SELECT 57 UNION ALL SELECT 58 UNION ALL SELECT 59 UNION ALL
    SELECT 60 UNION ALL SELECT 61 UNION ALL SELECT 62 UNION ALL SELECT 63 UNION ALL SELECT 64 UNION ALL SELECT 65 UNION ALL SELECT 66 UNION ALL SELECT 67 UNION ALL SELECT 68 UNION ALL SELECT 69 UNION ALL
    SELECT 70 UNION ALL SELECT 71 UNION ALL SELECT 72 UNION ALL SELECT 73 UNION ALL SELECT 74 UNION ALL SELECT 75 UNION ALL SELECT 76 UNION ALL SELECT 77 UNION ALL SELECT 78 UNION ALL SELECT 79 UNION ALL
    SELECT 80 UNION ALL SELECT 81 UNION ALL SELECT 82 UNION ALL SELECT 83 UNION ALL SELECT 84 UNION ALL SELECT 85 UNION ALL SELECT 86 UNION ALL SELECT 87 UNION ALL SELECT 88 UNION ALL SELECT 89
) numbers
WHERE n < 90 AND MOD(n, 7) != 3;  -- Skip every 7th day to simulate some blocked dates

-- Venue 2 - City Gymnasium
INSERT INTO venue_availability (venue_id, available_date, time_slot_start, time_slot_end, is_available)
SELECT 2, DATE_ADD(CURDATE(), INTERVAL n DAY), '08:00:00', '23:00:00', TRUE
FROM (
    SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL
    SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL
    SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL
    SELECT 30 UNION ALL SELECT 31 UNION ALL SELECT 32 UNION ALL SELECT 33 UNION ALL SELECT 34 UNION ALL SELECT 35 UNION ALL SELECT 36 UNION ALL SELECT 37 UNION ALL SELECT 38 UNION ALL SELECT 39 UNION ALL
    SELECT 40 UNION ALL SELECT 41 UNION ALL SELECT 42 UNION ALL SELECT 43 UNION ALL SELECT 44 UNION ALL SELECT 45 UNION ALL SELECT 46 UNION ALL SELECT 47 UNION ALL SELECT 48 UNION ALL SELECT 49 UNION ALL
    SELECT 50 UNION ALL SELECT 51 UNION ALL SELECT 52 UNION ALL SELECT 53 UNION ALL SELECT 54 UNION ALL SELECT 55 UNION ALL SELECT 56 UNION ALL SELECT 57 UNION ALL SELECT 58 UNION ALL SELECT 59 UNION ALL
    SELECT 60 UNION ALL SELECT 61 UNION ALL SELECT 62 UNION ALL SELECT 63 UNION ALL SELECT 64 UNION ALL SELECT 65 UNION ALL SELECT 66 UNION ALL SELECT 67 UNION ALL SELECT 68 UNION ALL SELECT 69 UNION ALL
    SELECT 70 UNION ALL SELECT 71 UNION ALL SELECT 72 UNION ALL SELECT 73 UNION ALL SELECT 74 UNION ALL SELECT 75 UNION ALL SELECT 76 UNION ALL SELECT 77 UNION ALL SELECT 78 UNION ALL SELECT 79 UNION ALL
    SELECT 80 UNION ALL SELECT 81 UNION ALL SELECT 82 UNION ALL SELECT 83 UNION ALL SELECT 84 UNION ALL SELECT 85 UNION ALL SELECT 86 UNION ALL SELECT 87 UNION ALL SELECT 88 UNION ALL SELECT 89
) numbers
WHERE n < 90 AND MOD(n, 9) != 4;

-- Venue 3 - Community Hall
INSERT INTO venue_availability (venue_id, available_date, time_slot_start, time_slot_end, is_available)
SELECT 3, DATE_ADD(CURDATE(), INTERVAL n DAY), '08:00:00', '23:00:00', TRUE
FROM (
    SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL
    SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL
    SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL
    SELECT 30 UNION ALL SELECT 31 UNION ALL SELECT 32 UNION ALL SELECT 33 UNION ALL SELECT 34 UNION ALL SELECT 35 UNION ALL SELECT 36 UNION ALL SELECT 37 UNION ALL SELECT 38 UNION ALL SELECT 39 UNION ALL
    SELECT 40 UNION ALL SELECT 41 UNION ALL SELECT 42 UNION ALL SELECT 43 UNION ALL SELECT 44 UNION ALL SELECT 45 UNION ALL SELECT 46 UNION ALL SELECT 47 UNION ALL SELECT 48 UNION ALL SELECT 49 UNION ALL
    SELECT 50 UNION ALL SELECT 51 UNION ALL SELECT 52 UNION ALL SELECT 53 UNION ALL SELECT 54 UNION ALL SELECT 55 UNION ALL SELECT 56 UNION ALL SELECT 57 UNION ALL SELECT 58 UNION ALL SELECT 59 UNION ALL
    SELECT 60 UNION ALL SELECT 61 UNION ALL SELECT 62 UNION ALL SELECT 63 UNION ALL SELECT 64 UNION ALL SELECT 65 UNION ALL SELECT 66 UNION ALL SELECT 67 UNION ALL SELECT 68 UNION ALL SELECT 69 UNION ALL
    SELECT 70 UNION ALL SELECT 71 UNION ALL SELECT 72 UNION ALL SELECT 73 UNION ALL SELECT 74 UNION ALL SELECT 75 UNION ALL SELECT 76 UNION ALL SELECT 77 UNION ALL SELECT 78 UNION ALL SELECT 79 UNION ALL
    SELECT 80 UNION ALL SELECT 81 UNION ALL SELECT 82 UNION ALL SELECT 83 UNION ALL SELECT 84 UNION ALL SELECT 85 UNION ALL SELECT 86 UNION ALL SELECT 87 UNION ALL SELECT 88 UNION ALL SELECT 89
) numbers
WHERE n < 90;

-- Venue 4 - Hotel Grand Ballroom
INSERT INTO venue_availability (venue_id, available_date, time_slot_start, time_slot_end, is_available)
SELECT 4, DATE_ADD(CURDATE(), INTERVAL n DAY), '08:00:00', '23:00:00', TRUE
FROM (
    SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL
    SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL
    SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL
    SELECT 30 UNION ALL SELECT 31 UNION ALL SELECT 32 UNION ALL SELECT 33 UNION ALL SELECT 34 UNION ALL SELECT 35 UNION ALL SELECT 36 UNION ALL SELECT 37 UNION ALL SELECT 38 UNION ALL SELECT 39 UNION ALL
    SELECT 40 UNION ALL SELECT 41 UNION ALL SELECT 42 UNION ALL SELECT 43 UNION ALL SELECT 44 UNION ALL SELECT 45 UNION ALL SELECT 46 UNION ALL SELECT 47 UNION ALL SELECT 48 UNION ALL SELECT 49 UNION ALL
    SELECT 50 UNION ALL SELECT 51 UNION ALL SELECT 52 UNION ALL SELECT 53 UNION ALL SELECT 54 UNION ALL SELECT 55 UNION ALL SELECT 56 UNION ALL SELECT 57 UNION ALL SELECT 58 UNION ALL SELECT 59 UNION ALL
    SELECT 60 UNION ALL SELECT 61 UNION ALL SELECT 62 UNION ALL SELECT 63 UNION ALL SELECT 64 UNION ALL SELECT 65 UNION ALL SELECT 66 UNION ALL SELECT 67 UNION ALL SELECT 68 UNION ALL SELECT 69 UNION ALL
    SELECT 70 UNION ALL SELECT 71 UNION ALL SELECT 72 UNION ALL SELECT 73 UNION ALL SELECT 74 UNION ALL SELECT 75 UNION ALL SELECT 76 UNION ALL SELECT 77 UNION ALL SELECT 78 UNION ALL SELECT 79 UNION ALL
    SELECT 80 UNION ALL SELECT 81 UNION ALL SELECT 82 UNION ALL SELECT 83 UNION ALL SELECT 84 UNION ALL SELECT 85 UNION ALL SELECT 86 UNION ALL SELECT 87 UNION ALL SELECT 88 UNION ALL SELECT 89
) numbers
WHERE n < 90 AND MOD(n, 5) != 2;

-- Venue 5 - Tech Conference Center
INSERT INTO venue_availability (venue_id, available_date, time_slot_start, time_slot_end, is_available)
SELECT 5, DATE_ADD(CURDATE(), INTERVAL n DAY), '08:00:00', '23:00:00', TRUE
FROM (
    SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL
    SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL
    SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL
    SELECT 30 UNION ALL SELECT 31 UNION ALL SELECT 32 UNION ALL SELECT 33 UNION ALL SELECT 34 UNION ALL SELECT 35 UNION ALL SELECT 36 UNION ALL SELECT 37 UNION ALL SELECT 38 UNION ALL SELECT 39 UNION ALL
    SELECT 40 UNION ALL SELECT 41 UNION ALL SELECT 42 UNION ALL SELECT 43 UNION ALL SELECT 44 UNION ALL SELECT 45 UNION ALL SELECT 46 UNION ALL SELECT 47 UNION ALL SELECT 48 UNION ALL SELECT 49 UNION ALL
    SELECT 50 UNION ALL SELECT 51 UNION ALL SELECT 52 UNION ALL SELECT 53 UNION ALL SELECT 54 UNION ALL SELECT 55 UNION ALL SELECT 56 UNION ALL SELECT 57 UNION ALL SELECT 58 UNION ALL SELECT 59 UNION ALL
    SELECT 60 UNION ALL SELECT 61 UNION ALL SELECT 62 UNION ALL SELECT 63 UNION ALL SELECT 64 UNION ALL SELECT 65 UNION ALL SELECT 66 UNION ALL SELECT 67 UNION ALL SELECT 68 UNION ALL SELECT 69 UNION ALL
    SELECT 70 UNION ALL SELECT 71 UNION ALL SELECT 72 UNION ALL SELECT 73 UNION ALL SELECT 74 UNION ALL SELECT 75 UNION ALL SELECT 76 UNION ALL SELECT 77 UNION ALL SELECT 78 UNION ALL SELECT 79 UNION ALL
    SELECT 80 UNION ALL SELECT 81 UNION ALL SELECT 82 UNION ALL SELECT 83 UNION ALL SELECT 84 UNION ALL SELECT 85 UNION ALL SELECT 86 UNION ALL SELECT 87 UNION ALL SELECT 88 UNION ALL SELECT 89
) numbers
WHERE n < 90;

-- Venue 6 - Garden Vista
INSERT INTO venue_availability (venue_id, available_date, time_slot_start, time_slot_end, is_available)
SELECT 6, DATE_ADD(CURDATE(), INTERVAL n DAY), '08:00:00', '23:00:00', TRUE
FROM (
    SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL
    SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL
    SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL
    SELECT 30 UNION ALL SELECT 31 UNION ALL SELECT 32 UNION ALL SELECT 33 UNION ALL SELECT 34 UNION ALL SELECT 35 UNION ALL SELECT 36 UNION ALL SELECT 37 UNION ALL SELECT 38 UNION ALL SELECT 39 UNION ALL
    SELECT 40 UNION ALL SELECT 41 UNION ALL SELECT 42 UNION ALL SELECT 43 UNION ALL SELECT 44 UNION ALL SELECT 45 UNION ALL SELECT 46 UNION ALL SELECT 47 UNION ALL SELECT 48 UNION ALL SELECT 49 UNION ALL
    SELECT 50 UNION ALL SELECT 51 UNION ALL SELECT 52 UNION ALL SELECT 53 UNION ALL SELECT 54 UNION ALL SELECT 55 UNION ALL SELECT 56 UNION ALL SELECT 57 UNION ALL SELECT 58 UNION ALL SELECT 59 UNION ALL
    SELECT 60 UNION ALL SELECT 61 UNION ALL SELECT 62 UNION ALL SELECT 63 UNION ALL SELECT 64 UNION ALL SELECT 65 UNION ALL SELECT 66 UNION ALL SELECT 67 UNION ALL SELECT 68 UNION ALL SELECT 69 UNION ALL
    SELECT 70 UNION ALL SELECT 71 UNION ALL SELECT 72 UNION ALL SELECT 73 UNION ALL SELECT 74 UNION ALL SELECT 75 UNION ALL SELECT 76 UNION ALL SELECT 77 UNION ALL SELECT 78 UNION ALL SELECT 79 UNION ALL
    SELECT 80 UNION ALL SELECT 81 UNION ALL SELECT 82 UNION ALL SELECT 83 UNION ALL SELECT 84 UNION ALL SELECT 85 UNION ALL SELECT 86 UNION ALL SELECT 87 UNION ALL SELECT 88 UNION ALL SELECT 89
) numbers
WHERE n < 90 AND MOD(n, 6) != 1;

-- ============================================
-- Dynamic Pricing Rules
-- ============================================
-- Weekend pricing (20% increase)
INSERT INTO price_adjustments (venue_id, adjustment_type, day_of_week, multiplier, description, is_active)
SELECT venue_id, 'day_of_week', 'Saturday', 1.20, 'Weekend premium - Saturday', TRUE FROM venues;

INSERT INTO price_adjustments (venue_id, adjustment_type, day_of_week, multiplier, description, is_active)
SELECT venue_id, 'day_of_week', 'Sunday', 1.20, 'Weekend premium - Sunday', TRUE FROM venues;

-- Peak season (December, May - 30% increase)
INSERT INTO price_adjustments (venue_id, adjustment_type, season, start_date, end_date, multiplier, description, is_active)
SELECT venue_id, 'seasonal', 'December', '2025-12-01', '2025-12-31', 1.30, 'Christmas season premium', TRUE FROM venues;

INSERT INTO price_adjustments (venue_id, adjustment_type, season, start_date, end_date, multiplier, description, is_active)
SELECT venue_id, 'seasonal', 'May', '2025-05-01', '2025-05-31', 1.25, 'Summer wedding season', TRUE FROM venues;

-- ============================================
-- Sample Events
-- ============================================
INSERT INTO events (organizer_id, event_name, event_type, description, expected_attendees, budget, event_date, start_time, end_time, required_amenities, status)
VALUES
(4, 'Summer Wedding Celebration', 'wedding', 'Beautiful outdoor wedding ceremony and reception', 150, 120000, '2025-12-15', '16:00:00', '23:00:00', '["WiFi", "Sound System", "Catering Service", "Tables & Chairs"]', 'venue_searching'),
(5, 'Birthday Bash', 'birthday', '50th birthday party with dinner and entertainment', 80, 60000, '2025-11-20', '18:00:00', '22:00:00', '["Sound System", "Tables & Chairs", "Catering Service"]', 'planning'),
(4, 'Annual Corporate Summit', 'corporate', 'Company-wide conference and team building', 200, 180000, '2025-12-05', '08:00:00', '18:00:00', '["WiFi", "Projector & Screen", "Sound System", "Catering Service"]', 'planning');

-- ============================================
-- Sample Collaborative Filtering Interactions
-- ============================================
INSERT INTO cf_interactions (user_id, venue_id, interaction_type, interaction_value, event_type)
VALUES
(4, 1, 'view', 1.0, 'wedding'),
(4, 4, 'view', 1.0, 'wedding'),
(4, 6, 'view', 1.0, 'wedding'),
(4, 1, 'inquiry', 2.0, 'wedding'),
(5, 3, 'view', 1.0, 'birthday'),
(5, 1, 'view', 1.0, 'birthday'),
(5, 2, 'view', 1.0, 'corporate'),
(5, 5, 'inquiry', 2.0, 'corporate');

-- End of seed data
