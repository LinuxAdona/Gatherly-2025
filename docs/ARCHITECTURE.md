# Gatherly GEMS - System Architecture

## Overview

Gatherly is a full-stack event management and venue booking platform built with PHP backend, HTML/CSS/JS frontend, MySQL database, and Python ML services.

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         FRONTEND LAYER                          │
│  HTML + TailwindCSS + Vanilla JavaScript                        │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐       │
│  │  Landing │  │  Venues  │  │ Booking  │  │Dashboard │       │
│  │   Page   │  │  Search  │  │   Flow   │  │Analytics │       │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘       │
└────────────────────────┬────────────────────────────────────────┘
                         │ HTTP/HTTPS + WebSocket
┌────────────────────────▼────────────────────────────────────────┐
│                       BACKEND LAYER (PHP)                        │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                   API Gateway / Router                    │  │
│  │              (RESTful API with JWT Auth)                  │  │
│  └───────────────┬──────────────────────────────────────────┘  │
│                  │                                               │
│  ┌───────────────▼───────────────────────────────────────────┐ │
│  │                    Middleware Layer                        │ │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐       │ │
│  │  │    Auth     │  │    CORS     │  │   Logger    │       │ │
│  │  │ Middleware  │  │  Middleware │  │  Middleware │       │ │
│  │  └─────────────┘  └─────────────┘  └─────────────┘       │ │
│  └───────────────┬───────────────────────────────────────────┘ │
│                  │                                               │
│  ┌───────────────▼───────────────────────────────────────────┐ │
│  │                  Controller Layer                          │ │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐     │ │
│  │  │   Auth   │ │  Venue   │ │ Booking  │ │  Event   │     │ │
│  │  │Controller│ │Controller│ │Controller│ │Controller│     │ │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘     │ │
│  └───────────────┬───────────────────────────────────────────┘ │
│                  │                                               │
│  ┌───────────────▼───────────────────────────────────────────┐ │
│  │                     Model Layer                            │ │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐     │ │
│  │  │   User   │ │  Venue   │ │ Booking  │ │  Event   │     │ │
│  │  │  Model   │ │  Model   │ │  Model   │ │  Model   │     │ │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘     │ │
│  └───────────────┬───────────────────────────────────────────┘ │
│                  │                                               │
│  ┌───────────────▼───────────────────────────────────────────┐ │
│  │                   Utility Layer                            │ │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐     │ │
│  │  │   JWT    │ │Validator │ │ Response │ │  Logger  │     │ │
│  │  │  Utils   │ │  Utils   │ │  Utils   │ │  Utils   │     │ │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘     │ │
│  └────────────────────────────────────────────────────────────┘ │
└────────────────────────┬────────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────────┐
│                    DATABASE LAYER (MySQL)                        │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐           │
│  │  users   │ │  venues  │ │ bookings │ │  events  │           │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘           │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐           │
│  │amenities │ │contracts │ │ messages │ │ reviews  │           │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘           │
└────────────────────────┬────────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────────┐
│              MACHINE LEARNING LAYER (Python)                     │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │           ML Service API (Flask REST API)                 │  │
│  └───────────────┬──────────────────────────────────────────┘  │
│                  │                                               │
│  ┌───────┬───────▼───────┬───────────┐                         │
│  │       │               │           │                          │
│  │ ┌─────▼─────┐  ┌─────▼─────┐  ┌──▼──────┐                  │
│  │ │Recommend  │  │  Dynamic  │  │Forecast │                  │
│  │ │  Engine   │  │  Pricing  │  │ Engine  │                  │
│  │ │           │  │  Engine   │  │         │                  │
│  │ │  - MCDM   │  │           │  │- Prophet│                  │
│  │ │  - CF     │  │ - Season  │  │- ARIMA  │                  │
│  │ │           │  │ - Demand  │  │         │                  │
│  │ └───────────┘  └───────────┘  └─────────┘                  │
│  └──────────────────────────────────────────────────────────────┘
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│               REAL-TIME LAYER (Node.js + Socket.IO)             │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                   Chat Server                             │  │
│  │  - Real-time messaging                                    │  │
│  │  - User presence tracking                                 │  │
│  │  - Notification broadcasting                              │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                   EXTERNAL SERVICES                              │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐       │
│  │  Google  │  │  PayPal  │  │  GCash   │  │  Email   │       │
│  │   Maps   │  │  Payment │  │  Payment │  │  SMTP    │       │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘       │
└─────────────────────────────────────────────────────────────────┘
```

---

## Technology Stack

### Frontend

- **HTML5**: Semantic markup
- **TailwindCSS**: Utility-first CSS framework
- **Vanilla JavaScript**: Client-side interactivity
- **Font Awesome**: Icon library
- **Socket.IO Client**: Real-time chat

### Backend (PHP)

- **PHP 7.4+**: Server-side logic
- **PDO**: Database abstraction layer
- **Custom Router**: RESTful routing
- **JWT**: Stateless authentication
- **bcrypt**: Password hashing

### Database

- **MySQL 5.7+**: Relational database
- **InnoDB**: Transaction support
- **Full-text indexes**: Search optimization
- **Foreign keys**: Referential integrity

### Machine Learning (Python)

- **Flask**: REST API framework
- **scikit-learn**: ML algorithms
- **Prophet**: Time series forecasting
- **pandas**: Data manipulation
- **NumPy**: Numerical computing

### Real-Time Communication

- **Node.js**: JavaScript runtime
- **Socket.IO**: WebSocket library
- **Express**: Web framework

### External APIs

- **Google Maps API**: Location services
- **PayPal SDK**: Payment processing
- **GCash API**: Local payment gateway
- **SMTP**: Email notifications

---

## Design Patterns

### 1. MVC (Model-View-Controller)

- **Models**: Data access and business logic
- **Views**: Frontend templates (HTML)
- **Controllers**: Request handlers

### 2. Repository Pattern

- Models abstract database operations
- Separation of data access from business logic

### 3. Middleware Pattern

- Authentication verification
- CORS handling
- Request logging
- Error handling

### 4. Singleton Pattern

- Database connection
- Configuration loader

### 5. Factory Pattern

- Response formatting
- JWT token generation

---

## Security Measures

### Authentication & Authorization

- JWT-based stateless authentication
- Role-based access control (RBAC)
- Password hashing with bcrypt
- Token expiration and refresh

### Input Validation

- Server-side validation for all inputs
- XSS prevention through sanitization
- SQL injection prevention via PDO prepared statements
- CSRF token validation

### Security Headers

- X-Content-Type-Options: nosniff
- X-Frame-Options: SAMEORIGIN
- X-XSS-Protection: 1; mode=block
- Content-Security-Policy (to be implemented)

### Data Protection

- HTTPS enforcement (production)
- Environment variable protection
- Database credential encryption
- Sensitive file access restriction

---

## Database Design Principles

### Normalization

- 3NF compliance
- Elimination of data redundancy
- Proper foreign key relationships

### Indexing Strategy

- Primary keys on all tables
- Foreign key indexes
- Composite indexes for common queries
- Full-text indexes for search

### Data Integrity

- Foreign key constraints
- CHECK constraints for validation
- NOT NULL constraints
- UNIQUE constraints

### Performance Optimization

- Query optimization
- Index usage
- Connection pooling
- Prepared statements

---

## API Design Principles

### RESTful Architecture

- Resource-based URLs
- HTTP methods (GET, POST, PUT, DELETE)
- Stateless communication
- Standard HTTP status codes

### Response Format

```json
{
  "success": boolean,
  "message": string,
  "data": object|array|null,
  "timestamp": ISO8601,
  "errors": object (if validation failed)
}
```

### Error Handling

- Standardized error responses
- Descriptive error messages
- Proper HTTP status codes
- Error logging

---

## Scalability Considerations

### Horizontal Scaling

- Stateless API design
- Session storage in database/Redis
- Load balancing ready

### Caching Strategy

- Database query caching
- API response caching
- Static asset caching
- CDN integration

### Database Optimization

- Connection pooling
- Query optimization
- Index optimization
- Partitioning for large tables

### Microservices Ready

- ML service separated
- Chat service separated
- Payment service abstraction
- Email service abstraction

---

## Monitoring & Logging

### Application Logging

- Error logs
- Access logs
- Performance logs
- Security audit logs

### Metrics to Track

- API response times
- Database query performance
- Error rates
- Active user sessions
- Booking conversion rates

### Alerting

- Server downtime alerts
- Error threshold alerts
- Security breach alerts
- Performance degradation alerts

---

## Development Workflow

### Git Branching Strategy

```
main (production)
  ├── develop (integration)
  │   ├── feature/venue-management
  │   ├── feature/booking-system
  │   ├── feature/ml-recommendations
  │   └── feature/chat-system
  └── hotfix/critical-bug
```

### Code Review Process

1. Create feature branch
2. Implement feature
3. Write tests
4. Submit pull request
5. Code review
6. Merge to develop
7. Deploy to staging
8. Test on staging
9. Merge to main
10. Deploy to production

---

## Testing Strategy

### Unit Tests

- Model methods
- Utility functions
- Business logic

### Integration Tests

- API endpoints
- Database operations
- External service integration

### E2E Tests

- Complete booking flow
- User registration and login
- Venue search and filter

### Performance Tests

- Load testing
- Stress testing
- Spike testing

---

## Deployment Architecture

### Development

```
XAMPP (Windows/Mac/Linux)
├── Apache Web Server
├── MySQL Database
└── PHP Runtime
```

### Production

```
Cloud Provider (AWS/Azure/DigitalOcean)
├── Web Server (Nginx/Apache)
├── Application Server (PHP-FPM)
├── Database Server (MySQL/RDS)
├── ML Service Server (Python/Flask)
├── Chat Server (Node.js)
└── Load Balancer
```

---

## Future Enhancements

### Phase 2

- Mobile app (React Native)
- Advanced analytics dashboard
- SMS notifications
- Multiple language support

### Phase 3

- AI chatbot support
- Virtual venue tours (360°)
- Blockchain-based contracts
- Integration with calendar apps

### Phase 4

- Marketplace for vendors
- Event insurance integration
- Social media integration
- White-label solutions

---

## Support & Maintenance

### Regular Maintenance

- Database backups (daily)
- Security updates (monthly)
- Performance optimization (quarterly)
- Feature updates (as needed)

### Support Channels

- Email: support@gatherly.com
- Documentation: https://docs.gatherly.com
- Community Forum: https://forum.gatherly.com
- GitHub Issues: https://github.com/LinuxAdona/Gatherly-2025/issues
