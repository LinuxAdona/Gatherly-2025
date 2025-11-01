# Gatherly GEMS - API Documentation

## Base URL

```
Development: http://localhost/Gatherly-2025/backend/api
Production: https://yourdomain.com/api
```

## Authentication

All protected endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer YOUR_JWT_TOKEN
```

---

## Authentication Endpoints

### Register User

**POST** `/auth/register`

Create a new user account (organizer or venue manager).

**Request Body:**

```json
{
  "email": "user@example.com",
  "password": "SecurePass@123",
  "full_name": "John Doe",
  "phone": "+639171234567",
  "role": "organizer"
}
```

**Validation Rules:**

- `email`: Required, valid email format, unique
- `password`: Required, min 8 chars, must contain uppercase, lowercase, number, special char
- `full_name`: Required
- `phone`: Optional, Philippine format (+639XXXXXXXXX or 09XXXXXXXXX)
- `role`: Required, must be "organizer" or "venue_manager"

**Response (201):**

```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "user_id": 1,
      "email": "user@example.com",
      "full_name": "John Doe",
      "role": "organizer"
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  },
  "timestamp": "2025-11-01T10:00:00+08:00"
}
```

---

### Login

**POST** `/auth/login`

Authenticate user and receive JWT token.

**Request Body:**

```json
{
  "email": "user@example.com",
  "password": "SecurePass@123"
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "user_id": 1,
      "email": "user@example.com",
      "full_name": "John Doe",
      "phone": "+639171234567",
      "role": "organizer",
      "profile_image": null
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  },
  "timestamp": "2025-11-01T10:00:00+08:00"
}
```

---

### Get Current User Profile

**GET** `/auth/me`

Get authenticated user's profile information.

**Headers:**

```
Authorization: Bearer YOUR_JWT_TOKEN
```

**Response (200):**

```json
{
  "success": true,
  "message": "Success",
  "data": {
    "user": {
      "user_id": 1,
      "email": "user@example.com",
      "full_name": "John Doe",
      "phone": "+639171234567",
      "role": "organizer",
      "profile_image": null,
      "email_verified": false
    }
  },
  "timestamp": "2025-11-01T10:00:00+08:00"
}
```

---

### Update Profile

**PUT** `/auth/profile`

Update user profile information.

**Headers:**

```
Authorization: Bearer YOUR_JWT_TOKEN
```

**Request Body:**

```json
{
  "full_name": "John Updated Doe",
  "phone": "+639187654321",
  "profile_image": "https://example.com/image.jpg"
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "user": {
      "user_id": 1,
      "full_name": "John Updated Doe",
      "phone": "+639187654321",
      "profile_image": "https://example.com/image.jpg"
    }
  },
  "timestamp": "2025-11-01T10:00:00+08:00"
}
```

---

### Change Password

**POST** `/auth/change-password`

Change user password.

**Headers:**

```
Authorization: Bearer YOUR_JWT_TOKEN
```

**Request Body:**

```json
{
  "current_password": "OldPass@123",
  "new_password": "NewSecurePass@456"
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Password changed successfully",
  "data": null,
  "timestamp": "2025-11-01T10:00:00+08:00"
}
```

---

## Health Check

### API Health Status

**GET** `/health`

Check if API is running.

**Response (200):**

```json
{
  "success": true,
  "message": "API is running",
  "data": {
    "status": "healthy",
    "timestamp": "2025-11-01T10:00:00+08:00",
    "version": "1.0.0"
  },
  "timestamp": "2025-11-01T10:00:00+08:00"
}
```

---

## Error Responses

### Validation Error (422)

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": "Invalid email format",
    "password": [
      "Password must be at least 8 characters long",
      "Password must contain at least one uppercase letter"
    ]
  },
  "timestamp": "2025-11-01T10:00:00+08:00"
}
```

### Unauthorized (401)

```json
{
  "success": false,
  "message": "Authentication token required",
  "timestamp": "2025-11-01T10:00:00+08:00"
}
```

### Forbidden (403)

```json
{
  "success": false,
  "message": "Insufficient permissions",
  "timestamp": "2025-11-01T10:00:00+08:00"
}
```

### Not Found (404)

```json
{
  "success": false,
  "message": "Resource not found",
  "timestamp": "2025-11-01T10:00:00+08:00"
}
```

### Server Error (500)

```json
{
  "success": false,
  "message": "Internal server error",
  "timestamp": "2025-11-01T10:00:00+08:00"
}
```

---

## More Endpoints (Coming Soon)

- Venues Management
- Bookings
- Events
- Recommendations
- Dynamic Pricing
- Contracts
- Chat/Messages
- Reviews
- Analytics

---

## Rate Limiting

- Rate limiting will be implemented in production
- Current: No limits in development

## Versioning

- Current version: v1.0.0
- API follows semantic versioning

## Support

For API support, contact: support@gatherly.com
