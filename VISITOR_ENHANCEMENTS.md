# Visitor Management Enhancements

## Overview
Comprehensive visitor management enhancements have been implemented to provide advanced features for tracking, categorizing, and managing visitors.

## ✅ Implemented Features

### 1. **Visitor History & Search**
- **Search Functionality**: Search visitors by name, phone, email, company, or purpose
- **History Tracking**: View complete visit history for any visitor by phone number
- **Advanced Filters**: 
  - Filter by category
  - Filter by visitor type (regular, contractor, vendor, guest)
  - Filter by VIP status
  - Filter by status (checked_in, checked_out)
  - Date range filtering

### 2. **Visitor Categories/Tags**
- **Category System**: Organize visitors into categories (VIP, Regular, Contractor, Vendor, Guest)
- **Color Coding**: Each category has a color for visual identification
- **Priority System**: Categories can be prioritized for sorting
- **Category Management**: Full CRUD operations for categories
- **Default Categories**: Seeder included for common categories

### 3. **Visitor Notes/Comments**
- **Public Notes**: Notes visible to visitors (optional)
- **Internal Notes**: Private notes for staff/admin only
- **Update Notes**: Ability to add/edit notes at any time
- **Notes History**: Notes are preserved with timestamps

### 4. **Visitor Blacklist/Whitelist**
- **Blacklist Management**: Add visitors to blacklist with reasons
- **Automatic Checking**: System automatically checks blacklist during check-in
- **Expiration Dates**: Blacklist entries can have expiration dates
- **Multiple Criteria**: Blacklist by phone, email, or both
- **Blacklist Reasons**: Track why visitors were blacklisted
- **Active/Inactive**: Toggle blacklist entries on/off

### 5. **Visitor Pre-registration**
- **Pre-register Visitors**: Allow visitors to register before arrival
- **QR Code Generation**: Automatic QR code generation for quick check-in
- **Expected Arrival Time**: Schedule expected arrival times
- **Status Tracking**: Track pre-registration status (pending, confirmed, canceled, completed)
- **Quick Check-in**: Convert pre-registration to actual check-in with one click
- **Pre-registration List**: View all pre-registrations

### 6. **Visitor Wait Time Tracking**
- **Automatic Tracking**: Automatically starts when visitor checks in
- **Manual Control**: Start/end wait time manually
- **Duration Calculation**: Calculates wait duration in minutes
- **Wait Time Display**: Shows wait time in visitor details
- **Analytics Ready**: Wait time data available for reporting

### 7. **Visitor Feedback/Surveys**
- **Rating System**: 1-5 star rating system
- **Comments**: Free-form feedback comments
- **Feedback Types**: Categorize feedback (general, service, facility, staff)
- **Anonymous Feedback**: Option for anonymous feedback
- **Public Display**: Option to display feedback publicly
- **Survey Responses**: JSON field for structured survey data

## 📁 Database Structure

### New Tables

1. **visitor_categories**
   - id, name, slug, color, description, is_active, priority, timestamps

2. **visitor_blacklists**
   - id, name, phone, email, reason, notes, created_by, expires_at, is_active, timestamps

3. **visitor_preregistrations**
   - id, name, phone, email, company, purpose, user_id, category_id, expected_arrival_time, notes, status, qr_code, checked_in_at, checked_in_by, timestamps

4. **visitor_feedback**
   - id, visitor_id, rating, comments, survey_responses, feedback_type, is_anonymous, visitor_email, is_public, timestamps

### Enhanced Visitors Table

New fields added:
- `category_id` - Foreign key to visitor_categories
- `email` - Visitor email address
- `company` - Company name
- `internal_notes` - Private notes for staff
- `wait_start_time` - When wait time started
- `wait_duration_minutes` - Calculated wait duration
- `is_vip` - VIP status flag
- `visitor_type` - Type of visitor (regular, contractor, vendor, guest)

## 🔧 Models

### Updated Models

1. **Visitors Model**
   - Added relationships: category(), feedback()
   - Added scopes: byCategory(), vip(), byType(), search()
   - Added methods: startWaitTime(), endWaitTime(), getVisitDurationAttribute()

2. **New Models**
   - `VisitorCategory` - Category management
   - `VisitorBlacklist` - Blacklist management with expiration checking
   - `VisitorPreregistration` - Pre-registration with QR code generation
   - `VisitorFeedback` - Feedback and survey management

## 🛣️ Routes

### New Routes

**Visitor Routes:**
- `GET /visitors` - Enhanced with search and filters
- `GET /visitors/{id}` - View visitor details
- `GET /visitors/history/{phone}` - View visitor history
- `POST /visitors/{id}/notes` - Update notes
- `POST /visitors/{id}/wait/start` - Start wait time
- `POST /visitors/{id}/wait/end` - End wait time

**Pre-registration Routes:**
- `GET /visitors/preregister` - Pre-registration form
- `POST /visitors/preregister/store` - Store pre-registration
- `GET /visitors/preregistrations` - List pre-registrations
- `POST /visitors/preregistrations/{id}/checkin` - Check-in from pre-registration

**Feedback Routes:**
- `GET /visitors/{id}/feedback` - Feedback form
- `POST /visitors/{id}/feedback` - Store feedback

**Management Routes (Admin/Receptionist):**
- `GET /visitors/categories` - List categories
- `GET /visitors/categories/create` - Create category
- `POST /visitors/categories/store` - Store category
- `GET /visitors/categories/{id}/edit` - Edit category
- `PUT /visitors/categories/{id}/update` - Update category
- `DELETE /visitors/categories/{id}` - Delete category

- `GET /visitors/blacklist` - List blacklist
- `GET /visitors/blacklist/create` - Add to blacklist
- `POST /visitors/blacklist/store` - Store blacklist entry
- `GET /visitors/blacklist/{id}/edit` - Edit blacklist entry
- `PUT /visitors/blacklist/{id}/update` - Update blacklist entry
- `DELETE /visitors/blacklist/{id}` - Remove from blacklist

## 🎯 Key Features

### Search & Filter
```php
// Search visitors
$visitors = Visitors::search('john')->get();

// Filter by category
$visitors = Visitors::byCategory($categoryId)->get();

// Filter VIP visitors
$visitors = Visitors::vip()->get();

// Filter by type
$visitors = Visitors::byType('contractor')->get();
```

### Blacklist Checking
```php
// Check if visitor is blacklisted
if (VisitorBlacklist::isBlacklisted($phone, $email)) {
    // Deny access
}
```

### Wait Time Tracking
```php
// Start wait time
$visitor->startWaitTime();

// End wait time (automatically calculates duration)
$visitor->endWaitTime();
```

### Pre-registration
```php
// Generate QR code
$preregistration->generateQRCode();

// Check in from pre-registration
$visitor = $preregistration->checkIn();
```

## 📊 Usage Examples

### Creating a Visitor with Category
```php
Visitors::create([
    'name' => 'John Doe',
    'phone' => '1234567890',
    'email' => 'john@example.com',
    'company' => 'ABC Corp',
    'category_id' => 1, // VIP category
    'visitor_type' => 'contractor',
    'is_vip' => true,
    // ... other fields
]);
```

### Adding to Blacklist
```php
VisitorBlacklist::create([
    'name' => 'John Doe',
    'phone' => '1234567890',
    'reason' => 'Security concern',
    'expires_at' => Carbon::now()->addMonths(6),
]);
```

### Pre-registering a Visitor
```php
$preregistration = VisitorPreregistration::create([
    'name' => 'Jane Smith',
    'phone' => '0987654321',
    'expected_arrival_time' => Carbon::now()->addDays(1),
    'user_id' => $staffId,
]);
$qrCode = $preregistration->generateQRCode();
```

## 🚀 Setup Instructions

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```

2. **Seed Default Categories**
   ```bash
   php artisan db:seed --class=VisitorCategorySeeder
   ```

3. **Update Views**
   - Update `visitors/index.blade.php` with search and filters
   - Create views for categories, blacklist, pre-registration, feedback
   - Add visitor detail view with history

## 📝 Notes

- **Blacklist Checking**: Automatically checked during check-in (both public and admin)
- **Wait Time**: Automatically starts on check-in, can be manually controlled
- **QR Codes**: Generated automatically for pre-registrations
- **Categories**: Default categories can be customized via seeder
- **Feedback**: Can be anonymous or linked to visitor email

## 🔮 Future Enhancements

1. **Email Notifications**: Notify staff when VIP visitors arrive
2. **SMS Integration**: Send SMS for pre-registration confirmations
3. **Badge Printing**: Print visitor badges with QR codes
4. **Analytics Dashboard**: Visualize wait times, categories, feedback
5. **Export Features**: Export visitor data with all new fields
6. **API Endpoints**: RESTful API for mobile apps
7. **Visitor Portal**: Self-service portal for visitors

## ✅ Testing Checklist

- [x] Migrations created and tested
- [x] Models with relationships
- [x] Controllers with all methods
- [x] Routes configured
- [x] Seeder for default categories
- [ ] Views updated (in progress)
- [ ] Search functionality tested
- [ ] Blacklist checking tested
- [ ] Pre-registration flow tested
- [ ] Wait time tracking tested
- [ ] Feedback system tested

## 📚 Documentation

All models include proper relationships, scopes, and helper methods. Controllers include validation and error handling. The system is designed to be extensible and maintainable.

---

*This enhancement provides a comprehensive visitor management system with advanced features for modern reception management.*

