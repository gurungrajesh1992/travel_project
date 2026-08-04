# Travel & Tour Management System — Database Schema v3 (Laravel)

Corrected/extended version of v2. Fixes a real bug (`tours` FK referencing
commented-out columns), replaces two hand-rolled subsystems with their
framework-native equivalents (roles/permissions → Spatie, API tokens →
Sanctum), adds the `guides` table the admin/booking flow already depends on,
and adds minimal Phase-2 stub tables (hotel, vehicle, flight, chatbot,
currency) so that future development doesn't require breaking migrations.
`v2.md` is left as-is for history; see "Summary of Changes from v2" at the
bottom for the full diff.

---

## 1. Destinations & Categories

```sql
-- Countries/regions: Nepal, India, Bhutan, Tibet (top-level nav)
-- Self-referencing for Region -> City/Area if ever needed
CREATE TABLE destinations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    parent_id BIGINT UNSIGNED NULL,
    name VARCHAR(150),
    slug VARCHAR(150) UNIQUE,
    description TEXT,
    banner_image VARCHAR(255),
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    sort_order INT DEFAULT 0,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP, updated_at TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES destinations(id),
    INDEX idx_destinations_status (status)
);

-- Trekking, Expedition, Peak Climbing, Tour, Special Activities
-- Also handles sub-categories (Everest Trek, Annapurna Trek) via parent_id
CREATE TABLE categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    parent_id BIGINT UNSIGNED NULL,
    name VARCHAR(150),
    slug VARCHAR(150) UNIQUE,
    description TEXT,
    icon VARCHAR(100) NULL,
    sort_order INT DEFAULT 0,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP, updated_at TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id),
    INDEX idx_categories_status (status)
);

CREATE TABLE difficulty_levels (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50),          -- Easy, Moderate, Challenging, Strenuous
    description VARCHAR(255),
    sort_order INT
);
```

---

## 2. Vendors (Phase 2 stub)

```sql
CREATE TABLE vendors (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    business_name VARCHAR(150),
    owner_name VARCHAR(150),
    email VARCHAR(150) UNIQUE,
    phone VARCHAR(30),
    logo VARCHAR(255) NULL,
    commission_rate DECIMAL(5,2) DEFAULT 0,
    status ENUM('pending','active','suspended') DEFAULT 'pending',
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP, updated_at TIMESTAMP
);
```

---

## 3. Tours — multi-destination & multi-category support

```sql
-- FIX (v3): v2 had FOREIGN KEY (destination_id)/(category_id) referencing
-- columns that were commented out — that CREATE TABLE could not run.
-- These are re-added as real, nullable columns used ONLY to resolve the
-- canonical URL/breadcrumb fast (avoids a GROUP BY query on every page
-- load). Full membership always lives in the pivot tables below; keep
-- these two in sync from whichever pivot row has is_primary = 1.
CREATE TABLE tours (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    vendor_id BIGINT UNSIGNED NULL,

    primary_destination_id BIGINT UNSIGNED NULL,
    primary_category_id BIGINT UNSIGNED NULL,
    difficulty_id BIGINT UNSIGNED NULL,
    guide_id BIGINT UNSIGNED NULL,   -- NEW (v3): default/featured guide for this tour, optional

    title VARCHAR(255),
    slug VARCHAR(255) UNIQUE,
    short_description VARCHAR(500),
    full_description LONGTEXT,

    duration_days INT,
    duration_nights INT,
    max_altitude VARCHAR(50) NULL,
    group_size_min INT DEFAULT 1,
    group_size_max INT NULL,
    best_season VARCHAR(150) NULL,

    base_price DECIMAL(10,2),
    currency VARCHAR(10) DEFAULT 'USD',
    total_seats INT NULL,

    thumbnail VARCHAR(255),
    map_embed_url VARCHAR(500) NULL,

    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,

    is_featured TINYINT DEFAULT 0,
    booking_mode ENUM('instant','inquiry','both') DEFAULT 'both',
    status ENUM('draft','published','archived') DEFAULT 'draft',

    created_at TIMESTAMP, updated_at TIMESTAMP, deleted_at TIMESTAMP NULL,

    FOREIGN KEY (vendor_id) REFERENCES vendors(id),
    FOREIGN KEY (primary_destination_id) REFERENCES destinations(id),
    FOREIGN KEY (primary_category_id) REFERENCES categories(id),
    FOREIGN KEY (difficulty_id) REFERENCES difficulty_levels(id),
    FOREIGN KEY (guide_id) REFERENCES guides(id),
    INDEX idx_tours_status (status)
);

-- many-to-many — a tour can span multiple countries
-- (e.g. "India-Nepal-Bhutan Circuit" has 3 rows here)
CREATE TABLE tour_destinations (
    tour_id BIGINT UNSIGNED,
    destination_id BIGINT UNSIGNED,
    is_primary TINYINT DEFAULT 0,   -- marks the one used for canonical URL
    PRIMARY KEY (tour_id, destination_id),
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
);

-- many-to-many — a tour can belong to multiple categories
-- (e.g. both "Trekking" and "Cultural Tour")
CREATE TABLE tour_categories (
    tour_id BIGINT UNSIGNED,
    category_id BIGINT UNSIGNED,
    is_primary TINYINT DEFAULT 0,
    PRIMARY KEY (tour_id, category_id),
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE tour_highlights (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tour_id BIGINT UNSIGNED,
    highlight_text VARCHAR(255),
    sort_order INT,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
);

-- itinerary days optionally tagged with which country they belong to —
-- supports "Day 1-4: Nepal, Day 5-8: Bhutan" display
CREATE TABLE tour_itineraries (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tour_id BIGINT UNSIGNED,
    destination_id BIGINT UNSIGNED NULL,
    day_number INT,
    title VARCHAR(255),
    description TEXT,
    altitude VARCHAR(50) NULL,
    meals VARCHAR(100) NULL,
    accommodation VARCHAR(100) NULL,
    walking_hours VARCHAR(20) NULL,
    distance_km DECIMAL(5,1) NULL,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    FOREIGN KEY (destination_id) REFERENCES destinations(id)
);

-- multiple photos per itinerary day (e.g. "views from the pass" + "teahouse lunch stop")
CREATE TABLE tour_itinerary_media (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tour_itinerary_id BIGINT UNSIGNED,
    file_path VARCHAR(255),
    caption VARCHAR(255) NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (tour_itinerary_id) REFERENCES tour_itineraries(id) ON DELETE CASCADE
);

CREATE TABLE tour_cost_details (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tour_id BIGINT UNSIGNED,
    type ENUM('include','exclude'),
    detail_text VARCHAR(255),
    sort_order INT,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
);

CREATE TABLE tour_media (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tour_id BIGINT UNSIGNED,
    media_type ENUM('image','video'),
    file_path VARCHAR(255),
    video_url VARCHAR(255) NULL,
    caption VARCHAR(255) NULL,
    sort_order INT,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
);

CREATE TABLE tour_faqs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tour_id BIGINT UNSIGNED,
    question VARCHAR(255),
    answer TEXT,
    sort_order INT,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
);
```

---

## 4. Guides (NEW in v3)

Admin module #6 and the booking flow ("customer can select their personal
guide, or leave unassigned") both depend on this table, which v2 never
defined.

```sql
CREATE TABLE guides (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150),
    slug VARCHAR(150) UNIQUE,
    photo VARCHAR(255) NULL,
    bio TEXT NULL,
    languages VARCHAR(255) NULL,       -- comma-separated or JSON, e.g. "English,Nepali,Hindi"
    experience_years INT NULL,
    phone VARCHAR(30) NULL,
    email VARCHAR(150) NULL,
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP, updated_at TIMESTAMP
);
```

---

## 5. Pricing

```sql
CREATE TABLE tour_departures (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tour_id BIGINT UNSIGNED,
    departure_date DATE,
    return_date DATE NULL,
    available_seats INT,
    booked_seats INT DEFAULT 0,
    status ENUM('open','full','cancelled') DEFAULT 'open',
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    INDEX idx_departures_date (departure_date)
);

CREATE TABLE tour_seasonal_pricing (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tour_id BIGINT UNSIGNED,
    season_name VARCHAR(100),
    start_date DATE,
    end_date DATE,
    price DECIMAL(10,2),
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
);

CREATE TABLE tour_pricing_tiers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tour_id BIGINT UNSIGNED,
    tier_type ENUM('group','child','private','solo'),
    min_pax INT NULL,
    max_pax INT NULL,
    price_per_person DECIMAL(10,2),
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
);

CREATE TABLE coupons (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE,
    type ENUM('percentage','fixed'),
    value DECIMAL(10,2),
    min_booking_amount DECIMAL(10,2) NULL,
    max_discount_amount DECIMAL(10,2) NULL,
    usage_limit INT NULL,
    used_count INT DEFAULT 0,
    valid_from DATE NULL,
    valid_until DATE NULL,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP
);

-- If empty for a coupon, it applies to ALL tours (site-wide).
-- If populated, restricts the coupon to only these tours.
-- Resolution order in app code: specific tour match > category match > all.
CREATE TABLE coupon_tours (
    coupon_id BIGINT UNSIGNED,
    tour_id BIGINT UNSIGNED,
    PRIMARY KEY (coupon_id, tour_id),
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
);

CREATE TABLE coupon_categories (
    coupon_id BIGINT UNSIGNED,
    category_id BIGINT UNSIGNED,
    PRIMARY KEY (coupon_id, category_id),
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
```

---

## 6. Customers & Auth

```sql
-- Same table for customer/vendor/admin/staff. Fine-grained access control
-- is layered on top via Spatie Laravel-Permission (see §9) — this `role`
-- column stays only as a fast, denormalized check for coarse gating and
-- doesn't replace the permission tables.
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150),
    email VARCHAR(150) UNIQUE,
    phone VARCHAR(30) NULL,
    country VARCHAR(100) NULL,
    password VARCHAR(255) NULL,
    social_provider ENUM('facebook','google','none') DEFAULT 'none',
    social_id VARCHAR(255) NULL,
    avatar VARCHAR(255) NULL,
    role ENUM('customer','vendor','admin','staff') DEFAULT 'customer',
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP, updated_at TIMESTAMP
);

CREATE TABLE wishlists (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED,
    tour_id BIGINT UNSIGNED,
    created_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    UNIQUE (user_id, tour_id)
);
```

> Laravel's own default migrations additionally provide `password_reset_tokens`,
> `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs` —
> these ship with the framework/Breeze install and are not hand-defined here.

---

## 7. Bookings

```sql
CREATE TABLE bookings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    booking_ref VARCHAR(50) UNIQUE,
    tour_id BIGINT UNSIGNED,
    departure_id BIGINT UNSIGNED NULL,
    vendor_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NULL,
    coupon_id BIGINT UNSIGNED NULL,
    guide_id BIGINT UNSIGNED NULL,   -- NEW (v3): customer-selected or admin-assigned guide

    booking_type ENUM('instant','inquiry') DEFAULT 'instant',

    guest_name VARCHAR(150) NULL,
    guest_email VARCHAR(150) NULL,
    guest_phone VARCHAR(30) NULL,

    num_adults INT DEFAULT 1,
    num_children INT DEFAULT 0,
    pricing_tier_id BIGINT UNSIGNED NULL,

    subtotal DECIMAL(10,2),
    discount_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2),
    deposit_required DECIMAL(10,2) NULL,

    booking_status ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
    payment_status ENUM('unpaid','partial','paid','refunded') DEFAULT 'unpaid',
    cancellation_reason TEXT NULL,
    cancelled_at TIMESTAMP NULL,

    special_requests TEXT NULL,
    source ENUM('website','admin','api') DEFAULT 'website',

    created_at TIMESTAMP, updated_at TIMESTAMP,

    FOREIGN KEY (tour_id) REFERENCES tours(id),
    FOREIGN KEY (departure_id) REFERENCES tour_departures(id),
    FOREIGN KEY (vendor_id) REFERENCES vendors(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (coupon_id) REFERENCES coupons(id),
    FOREIGN KEY (guide_id) REFERENCES guides(id),
    FOREIGN KEY (pricing_tier_id) REFERENCES tour_pricing_tiers(id),
    INDEX idx_bookings_status (booking_status),
    INDEX idx_bookings_payment_status (payment_status)
);

CREATE TABLE booking_travelers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    booking_id BIGINT UNSIGNED,
    full_name VARCHAR(150),
    passport_number VARCHAR(50) NULL,
    nationality VARCHAR(100) NULL,
    date_of_birth DATE NULL,
    gender ENUM('male','female','other') NULL,
    is_lead_traveler TINYINT DEFAULT 0,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Every time a booking's status changes, insert a new row here (full audit trail)
CREATE TABLE booking_status_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    booking_id BIGINT UNSIGNED,
    from_status VARCHAR(50) NULL,
    to_status VARCHAR(50),
    changed_by BIGINT UNSIGNED NULL,
    note VARCHAR(255) NULL,
    created_at TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id)
);

-- Per-booking payments so partial payments can be tracked; bookings.payment_status
-- stays denormalized on the parent row for fast list/report queries.
CREATE TABLE payments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    booking_id BIGINT UNSIGNED,
    amount DECIMAL(10,2),
    payment_method VARCHAR(50),
    receipt_path VARCHAR(255) NULL,   -- NEW (v3): customer-uploaded payment bill/receipt
    transaction_ref VARCHAR(255) NULL,
    gateway_response JSON NULL,
    status ENUM('pending','success','failed','refunded'),
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);
```

> **v3 addition:** `payments.receipt_path` — the requirement doc's Website
> section calls for "payment verified by payment bill upload by customers";
> v2's `payments` table had no field to store that upload. Added here.

---

## 8. Reviews & Inquiries

```sql
-- App-level validation: rating must be 1-5.
CREATE TABLE reviews (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tour_id BIGINT UNSIGNED,
    booking_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NULL,
    reviewer_name VARCHAR(150),
    reviewer_country VARCHAR(100) NULL,
    rating TINYINT UNSIGNED,
    review_text TEXT,
    is_approved TINYINT DEFAULT 0,
    created_at TIMESTAMP,
    FOREIGN KEY (tour_id) REFERENCES tours(id),
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_reviews_approved (is_approved)
);

CREATE TABLE inquiries (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tour_id BIGINT UNSIGNED NULL,
    name VARCHAR(150),
    email VARCHAR(150),
    phone VARCHAR(30) NULL,
    subject VARCHAR(255) NULL,
    message TEXT,
    status ENUM('new','responded','closed') DEFAULT 'new',
    responded_by BIGINT UNSIGNED NULL,
    responded_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    FOREIGN KEY (tour_id) REFERENCES tours(id),
    FOREIGN KEY (responded_by) REFERENCES users(id),
    INDEX idx_inquiries_status (status)
);

CREATE TABLE newsletter_subscribers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(150) UNIQUE,
    subscribed_at TIMESTAMP
);
```

---

## 9. Blog & Static Pages

```sql
CREATE TABLE blog_categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    slug VARCHAR(100) UNIQUE
);

CREATE TABLE blog_posts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    author_id BIGINT UNSIGNED,
    blog_category_id BIGINT UNSIGNED NULL, -- single category per post, per requirement
    title VARCHAR(255),
    slug VARCHAR(255) UNIQUE,
    excerpt VARCHAR(500) NULL,
    content LONGTEXT,
    featured_image VARCHAR(255) NULL,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    status ENUM('draft','published') DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP, updated_at TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id),
    FOREIGN KEY (blog_category_id) REFERENCES blog_categories(id)
);

-- pages table for 'about-us', 'contact-us', 'terms', 'privacy-policy'
CREATE TABLE pages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    slug VARCHAR(255) UNIQUE,
    content LONGTEXT,
    featured_image VARCHAR(255) NULL,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    status ENUM('draft','published') DEFAULT 'published',
    created_at TIMESTAMP, updated_at TIMESTAMP
);
```

> **v3 change:** v2 defined a `blog_post_category` many-to-many pivot, but
> the requirement doc says blog posts are "assigning category single."
> Replaced with a direct `blog_category_id` FK on `blog_posts` — simpler and
> matches the actual requirement.

---

## 10. Roles, Permissions & Notifications

```sql
-- REMOVED in v3: hand-rolled `roles` / `permissions` / `role_permission` /
-- `user_role` tables. The requirement doc explicitly asks for
-- Spatie Laravel-Permission, which ships and manages its own migration
-- (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`,
-- `role_has_permissions`) via:
--   php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
-- Hand-rolling equivalent tables alongside the package would just create
-- two competing, out-of-sync sources of truth.

CREATE TABLE notifications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    notifiable_type VARCHAR(100),
    notifiable_id BIGINT UNSIGNED,
    type VARCHAR(100),
    channel ENUM('firebase','telegram','whatsapp','email','in_app'),
    title VARCHAR(255),
    body TEXT,
    data JSON NULL,
    read_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    INDEX idx_notifications_notifiable (notifiable_type, notifiable_id)
);

CREATE TABLE activity_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(150),
    subject_type VARCHAR(100) NULL,
    subject_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## 11. Settings, Gallery & FAQ

```sql
-- Key/value config: menu labels (e.g. 'multi_country_menu_label') AND
-- per-panel theme colors (e.g. 'theme.website.primary', 'theme.admin.primary',
-- 'theme.customer.primary') — both admin-editable, no schema change needed
-- to add more keys later.
CREATE TABLE settings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    key_name VARCHAR(150) UNIQUE,
    value TEXT
);

CREATE TABLE gallery_categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),        -- 'Mountains', 'Culture', 'Wildlife', 'Food', 'Team'
    slug VARCHAR(100) UNIQUE,
    sort_order INT DEFAULT 0,
    status TINYINT DEFAULT 1
);

CREATE TABLE gallery_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    gallery_category_id BIGINT UNSIGNED NULL,
    tour_id BIGINT UNSIGNED NULL,
    media_type ENUM('image','video') DEFAULT 'image',
    file_path VARCHAR(255) NULL,
    video_url VARCHAR(255) NULL,
    caption VARCHAR(255) NULL,
    is_featured TINYINT DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP,
    FOREIGN KEY (gallery_category_id) REFERENCES gallery_categories(id),
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE SET NULL
);

CREATE TABLE faq_categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),        -- 'Booking', 'Payment', 'Visa & Permits', 'Trekking Gear'
    slug VARCHAR(100) UNIQUE,
    sort_order INT DEFAULT 0
);

CREATE TABLE faqs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    faq_category_id BIGINT UNSIGNED NULL,
    question VARCHAR(255),
    answer TEXT,
    sort_order INT DEFAULT 0,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP, updated_at TIMESTAMP,
    FOREIGN KEY (faq_category_id) REFERENCES faq_categories(id)
);
```

---

## 12. Phase 2 — API Access

```sql
-- REMOVED in v3: hand-rolled `personal_access_tokens`. Laravel Sanctum
-- publishes and owns this exact table shape via:
--   php artisan install:api
-- Reusing the framework's own migration avoids drift from Sanctum's
-- expectations (column names/types it queries directly).
```

---

## 13. Phase 2 Stubs — Hotel, Vehicle, Flight, Chatbot, Currency (NEW in v3)

Minimal shells only — no business logic or UI is built against these this
phase. They exist so future development is additive, not a schema rewrite.

```sql
CREATE TABLE hotels (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    vendor_id BIGINT UNSIGNED NULL,
    destination_id BIGINT UNSIGNED NULL,
    name VARCHAR(200),
    slug VARCHAR(200) UNIQUE,
    star_rating TINYINT NULL,
    description TEXT NULL,
    address VARCHAR(255) NULL,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP, updated_at TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id),
    FOREIGN KEY (destination_id) REFERENCES destinations(id)
);

CREATE TABLE hotel_rooms (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    hotel_id BIGINT UNSIGNED,
    room_type VARCHAR(150),
    price_per_night DECIMAL(10,2),
    capacity INT DEFAULT 2,
    total_rooms INT DEFAULT 1,
    status TINYINT DEFAULT 1,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
);

CREATE TABLE hotel_bookings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    hotel_room_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED NULL,
    check_in DATE,
    check_out DATE,
    guests INT DEFAULT 1,
    total_amount DECIMAL(10,2),
    status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP,
    FOREIGN KEY (hotel_room_id) REFERENCES hotel_rooms(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE vehicle_types (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100)          -- 'Sedan', 'SUV', 'Van', 'Bus'
);

CREATE TABLE vehicles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    vendor_id BIGINT UNSIGNED NULL,
    vehicle_type_id BIGINT UNSIGNED NULL,
    name VARCHAR(150),
    capacity INT DEFAULT 4,
    price_per_day DECIMAL(10,2),
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP, updated_at TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id),
    FOREIGN KEY (vehicle_type_id) REFERENCES vehicle_types(id)
);

CREATE TABLE vehicle_bookings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    vehicle_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED NULL,
    pickup_date DATE,
    return_date DATE,
    total_amount DECIMAL(10,2),
    status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE flight_bookings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    booking_id BIGINT UNSIGNED NULL,   -- optional link to a tour booking
    user_id BIGINT UNSIGNED NULL,
    provider VARCHAR(100) NULL,        -- e.g. the flight API/aggregator used
    pnr VARCHAR(50) NULL,
    from_airport VARCHAR(10) NULL,
    to_airport VARCHAR(10) NULL,
    departure_at TIMESTAMP NULL,
    status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    raw_response JSON NULL,
    created_at TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE currencies (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(10) UNIQUE,   -- 'USD', 'NPR', 'EUR'
    name VARCHAR(100),
    symbol VARCHAR(10),
    exchange_rate_to_usd DECIMAL(12,6) DEFAULT 1,
    is_default TINYINT DEFAULT 0
);

CREATE TABLE chat_conversations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,      -- NULL = anonymous website visitor
    session_id VARCHAR(100) NULL,
    started_at TIMESTAMP,
    ended_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE chat_messages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    chat_conversation_id BIGINT UNSIGNED,
    sender ENUM('user','bot') DEFAULT 'user',
    message TEXT,
    created_at TIMESTAMP,
    FOREIGN KEY (chat_conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE
);
```

---

## Key Query Patterns for the Nav Menu (reference, unchanged from v2)

**1. Build per-country dropdown structure** (Nepal ▾, India ▾, Bhutan ▾ with their categories):
```sql
SELECT d.id AS destination_id, d.name AS destination_name,
       c.id AS category_id, c.name AS category_name
FROM destinations d
JOIN tour_destinations td ON td.destination_id = d.id
JOIN tours t ON t.id = td.tour_id AND t.status = 'published'
JOIN tour_categories tc ON tc.tour_id = t.id
JOIN categories c ON c.id = tc.category_id
WHERE d.parent_id IS NULL
GROUP BY d.id, c.id
ORDER BY d.sort_order, c.sort_order;
```

**2. Single-country listing page** (e.g. clicking "Nepal → Trekking"):
```sql
SELECT t.* FROM tours t
JOIN tour_destinations td ON td.tour_id = t.id
JOIN tour_categories tc ON tc.tour_id = t.id
WHERE td.destination_id = ? AND tc.category_id = ? AND t.status = 'published';
```

**3. Multi-country combo listing** (dedicated "Multi-Country Tours" section):
```sql
SELECT t.id, t.title,
       GROUP_CONCAT(d.name ORDER BY td.is_primary DESC, d.name SEPARATOR '-') AS country_label
FROM tours t
JOIN tour_destinations td ON td.tour_id = t.id
JOIN destinations d ON d.id = td.destination_id
WHERE t.status = 'published'
GROUP BY t.id
HAVING COUNT(DISTINCT td.destination_id) > 1;
```

---

## Summary of Changes from v2

| Change | Reason |
|---|---|
| **Fixed:** `tours` FK referencing commented-out `destination_id`/`category_id` | That CREATE TABLE could not run as written in v2 |
| Renamed to `primary_destination_id` / `primary_category_id`, kept nullable | Fast canonical URL/breadcrumb lookup; full membership stays in the pivots |
| Added `guides` table + `tours.guide_id` + `bookings.guide_id` | Admin module #6 and the booking flow reference guides, but v2 never defined the table |
| Removed hand-rolled `roles`/`permissions`/`role_permission`/`user_role` | Requirement doc asks for Spatie Laravel-Permission, which owns this schema |
| Removed hand-rolled `personal_access_tokens` | Laravel Sanctum owns this schema; avoids drift |
| `blog_post_category` pivot → `blog_posts.blog_category_id` | Requirement says blog posts have a single category, not many |
| Added `payments.receipt_path` | Requirement calls for customer-uploaded payment bill verification; v2 had nowhere to store it |
| Added indexes on `status`/date/FK columns across tours, bookings, inquiries, reviews, departures | These are the columns every list/filter page in the admin dashboard queries on |
| Added Phase-2 stub tables: hotels/hotel_rooms/hotel_bookings, vehicles/vehicle_types/vehicle_bookings, flight_bookings, chat_conversations/chat_messages, currencies | Explicitly requested: "create all required database structure so later we can continue development" for hotel, vehicle, flight, AI chatbot, multi-currency |
| `reviews.rating` documented as app-validated 1–5 | Enum/check wasn't practical in the column type chosen (`TINYINT`); enforce in `FormRequest` validation |
