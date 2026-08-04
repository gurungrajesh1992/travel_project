# Travel & Tour Management System — Database Schema v2 (Laravel)

Updated version incorporating: multi-country/multi-category tour support via pivot tables, dropped redundant `tour_types` table, per-day destination tagging in itineraries, and dynamic menu-query logic. Still Phase-2-ready (nullable `vendor_id` throughout) and API-ready (Sanctum tokens stub).

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
    FOREIGN KEY (parent_id) REFERENCES destinations(id)
);

-- Trekking, Expedition, Peak Climbing, Tour, Special Activities
-- Also handles sub-categories (Everest Trek, Annapurna Trek) via parent_id
-- NOTE: tour_types table removed — categories alone cover this site's needs
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
    FOREIGN KEY (parent_id) REFERENCES categories(id)
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

## 3. Tours — now with multi-destination & multi-category support

```sql
CREATE TABLE tours (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    vendor_id BIGINT UNSIGNED NULL,

    -- PRIMARY destination/category drive the canonical URL + breadcrumb.
    -- Full set of applicable destinations/categories lives in the pivot
    -- tables below (tour_destinations, tour_categories).
    -- destination_id BIGINT UNSIGNED NULL,
    -- category_id BIGINT UNSIGNED NULL,
    difficulty_id BIGINT UNSIGNED NULL,

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
    FOREIGN KEY (destination_id) REFERENCES destinations(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (difficulty_id) REFERENCES difficulty_levels(id)
);

-- NEW: many-to-many — a tour can span multiple countries
-- (e.g. "India-Nepal-Bhutan Circuit" has 3 rows here)
CREATE TABLE tour_destinations (
    tour_id BIGINT UNSIGNED,
    destination_id BIGINT UNSIGNED,
    is_primary TINYINT DEFAULT 0,   -- marks the one used for canonical URL
    PRIMARY KEY (tour_id, destination_id),
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
);

-- NEW: many-to-many — a tour can belong to multiple categories
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

-- UPDATED: itinerary days now optionally tagged with which country
-- they belong to — supports "Day 1-4: Nepal, Day 5-8: Bhutan" display
CREATE TABLE tour_itineraries (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tour_id BIGINT UNSIGNED,
    destination_id BIGINT UNSIGNED NULL,  -- NEW: which country this day is in
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

CREATE TABLE tour_itinerary_media (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tour_itinerary_id BIGINT UNSIGNED,
    file_path VARCHAR(255),
    caption VARCHAR(255) NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (tour_itinerary_id) REFERENCES tour_itineraries(id) ON DELETE CASCADE
);
--## if admin might want 2–3 photos for a single day (e.g., "views from the pass" + "teahouse lunch stop"
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

## 4. Pricing

```sql
CREATE TABLE tour_departures (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tour_id BIGINT UNSIGNED,
    departure_date DATE,
    return_date DATE NULL,
    available_seats INT,
    booked_seats INT DEFAULT 0,
    status ENUM('open','full','cancelled') DEFAULT 'open',
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
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

-- If empty for a coupon, it applies to ALL tours (site-wide, current behavior).
-- If populated, it restricts the coupon to only these tours.
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

## 5. Customers & Auth

```sql
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

---

## 6. Bookings

```sql
CREATE TABLE bookings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    booking_ref VARCHAR(50) UNIQUE,
    tour_id BIGINT UNSIGNED,
    departure_id BIGINT UNSIGNED NULL,
    vendor_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NULL,
    coupon_id BIGINT UNSIGNED NULL,

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
    FOREIGN KEY (pricing_tier_id) REFERENCES tour_pricing_tiers(id)
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

CREATE TABLE booking_status_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    booking_id BIGINT UNSIGNED,
    from_status VARCHAR(50) NULL,
    to_status VARCHAR(50),
    changed_by BIGINT UNSIGNED NULL,
    note VARCHAR(255) NULL,
    created_at TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

CREATE TABLE payments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    booking_id BIGINT UNSIGNED,
    amount DECIMAL(10,2),
    payment_method VARCHAR(50),
    transaction_ref VARCHAR(255) NULL,
    gateway_response JSON NULL,
    status ENUM('pending','success','failed','refunded'),
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);
```

---

## 7. Reviews & Inquiries

```sql
CREATE TABLE reviews (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tour_id BIGINT UNSIGNED,
    booking_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NULL,
    reviewer_name VARCHAR(150),
    reviewer_country VARCHAR(100) NULL,
    rating TINYINT,
    review_text TEXT,
    is_approved TINYINT DEFAULT 0,
    created_at TIMESTAMP,
    FOREIGN KEY (tour_id) REFERENCES tours(id),
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
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
    FOREIGN KEY (tour_id) REFERENCES tours(id)
);

CREATE TABLE newsletter_subscribers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(150) UNIQUE,
    subscribed_at TIMESTAMP
);
```

---

## 8. Blog

```sql
CREATE TABLE blog_posts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    author_id BIGINT UNSIGNED,
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
    FOREIGN KEY (author_id) REFERENCES users(id)
);

CREATE TABLE blog_categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    slug VARCHAR(100) UNIQUE
);

CREATE TABLE blog_post_category (
    blog_post_id BIGINT UNSIGNED,
    blog_category_id BIGINT UNSIGNED,
    PRIMARY KEY (blog_post_id, blog_category_id)
);
-- ## pages table  for 'about-us', 'contact-us', 'terms', 'privacy-policy'
CREATE TABLE pages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    slug VARCHAR(255) UNIQUE,        -- 'about-us', 'contact-us', 'terms', 'privacy-policy'
    content LONGTEXT,
    featured_image VARCHAR(255) NULL,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    status ENUM('draft','published') DEFAULT 'published',
    created_at TIMESTAMP, updated_at TIMESTAMP
);
```


---

## 9. Admin, Roles & Notifications

```sql
CREATE TABLE roles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100)
);

CREATE TABLE permissions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100)
);

CREATE TABLE role_permission (
    role_id BIGINT UNSIGNED,
    permission_id BIGINT UNSIGNED,
    PRIMARY KEY (role_id, permission_id)
);

CREATE TABLE user_role (
    user_id BIGINT UNSIGNED,
    role_id BIGINT UNSIGNED,
    PRIMARY KEY (user_id, role_id)
);

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
    created_at TIMESTAMP
);

CREATE TABLE activity_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(150),
    subject_type VARCHAR(100) NULL,
    subject_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at TIMESTAMP
);
```

---

## 10. Settings, Multi-language & Menu Config

```sql
-- Includes menu-related labels, e.g. key_name = 'multi_country_menu_label'
-- so the "Multi-Country Tours" nav label is admin-editable, not hardcoded
CREATE TABLE settings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    key_name VARCHAR(150) UNIQUE,
    value TEXT
);
 
--  gallery category and gallery 
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
    tour_id BIGINT UNSIGNED NULL,          -- optional: link back to source tour, if relevant
    media_type ENUM('image','video') DEFAULT 'image',
    file_path VARCHAR(255) NULL,
    video_url VARCHAR(255) NULL,
    caption VARCHAR(255) NULL,
    is_featured TINYINT DEFAULT 0,          -- show on homepage gallery preview
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP,
    FOREIGN KEY (gallery_category_id) REFERENCES gallery_categories(id),
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE SET NULL
);

-- Faq table
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

## 11. Phase 2 — API Access

```sql
CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tokenable_type VARCHAR(100),
    tokenable_id BIGINT UNSIGNED,
    name VARCHAR(150),
    token VARCHAR(64) UNIQUE,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP
);
```

---

## Key Query Patterns for the Nav Menu (reference)

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

**2. Single-country listing page** (e.g. clicking "Nepal → Trekking" — naturally includes multi-country tours that touch Nepal):
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

## Summary of Changes from v1

| Change | Reason |
|---|---|
| Removed `tour_types` table | Redundant with `categories`; reference site doesn't need a separate flat tag layer |
| Added `tour_destinations` pivot | Supports multi-country tours (India-Nepal-Bhutan Circuit) without duplicating tour rows |
| Added `tour_categories` pivot | Supports a tour belonging to multiple categories (e.g. Trekking + Cultural Tour) at once |
| `tours.destination_id` / `category_id` now nullable | They represent the *primary* (canonical URL) destination/category; full set lives in pivots |
| Added `destination_id` to `tour_itineraries` | Enables per-day country tagging ("Day 1-4: Nepal, Day 5-8: Bhutan") |
| `settings` table now documented for menu labels | Keeps static-feeling labels like "Multi-Country Tours" admin-editable instead of hardcoded, at near-zero extra cost |
