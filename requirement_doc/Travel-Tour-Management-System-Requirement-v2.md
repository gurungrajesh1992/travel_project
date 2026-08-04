# Travel & Tour Management System — Requirement Specification (v2)

> This is a reformatted, professional edition of the original client requirement
> notes. Content is unchanged in substance — reorganized into numbered
> sections with consistent structure, and annotated with **Suggestion** notes
> wherever a recommendation is being made on top of the original ask. The
> original file (`Travel-Tour-Management-System-Requirement.md`) is left
> untouched for reference.

---

## 1. Project Summary

A single-vendor travel & tour booking platform (trekking/expedition-focused,
Nepal/India/Bhutan/Tibet region) consisting of a public marketing website, a
customer self-service panel, and an admin dashboard — built so the data model
can grow into multi-vendor, multi-country, hotel/vehicle/flight booking, and
an AI chatbot without re-architecting later.

**Current site (for visual/content reference):** https://jvillnepal.com.np/
**Design/UX reference:** https://themes.themeenergy.com/bookyourtravel/

> **Suggestion:** Build as a single Laravel monolith (one codebase, one
> database) rather than separate apps per panel. All three panels share the
> same `users` table, the same tour/booking data, and the same auth session —
> splitting them into separate apps would only add deployment and
> data-sync overhead with no real benefit at this scale.

### 1.1 Vendor Model

The system launches **single-vendor** (the business owns all tours), but the
database is designed multi-vendor-ready from day one: every relevant table
carries a nullable `vendor_id`. When vendor onboarding is built later, no
schema migration of existing data is required — only new UI/business logic.

### 1.2 Future Enhancements (structure now, build later)

The following are **out of scope for this build phase**, but the database
schema reserves the necessary tables/columns so they can be developed later
without breaking changes:

| Enhancement | Schema readiness |
|---|---|
| Multi-vendor marketplace | `vendor_id` nullable FK already on tours/bookings |
| Hotel booking | Stub tables: `hotels`, `hotel_rooms`, `hotel_bookings` |
| Vehicle booking | Stub tables: `vehicles`, `vehicle_types`, `vehicle_bookings` |
| Flight integration | Stub table: `flight_bookings` |
| AI chatbot | Stub tables: `chat_conversations`, `chat_messages` |
| Multi-currency | Stub table: `currencies` (tours already store a `currency` code) |
| Multi-language | Deferred — recommend `spatie/laravel-translatable` (JSON columns) over parallel translation tables when the time comes; no schema change needed today |
| PDF itineraries / CRM integration | No schema impact — generated/exported from existing tour + booking data |

---

## 2. Client Top-Level Requirements

- Multi-vendor tours (phase 2 — see §1.1)
- Tour guides
- Hotel booking (phase 2)
- Vehicle booking (phase 2)
- Flight integration (phase 2)
- AI chatbot (phase 2)
- Instant mobile notifications
- Custom itinerary management
- Tour reminders
- Booking workflow
- Better security
- Copy protection
- Custom reports

---

## 3. Project Blueprint (Draft)

### 3.1 Website (Frontend)
Home, Destinations, Tour pages, Trekking packages, Search & filters, Booking
flow, Inquiry system, Reviews, Blog, Gallery, FAQ, Contact, About, SEO,
mobile responsive.

### 3.2 Tour Management
Unlimited tours/destinations, categories, tour types, difficulty, duration,
price, seasonal pricing, dates, seats, highlights, day-by-day itinerary,
includes/excludes, maps, gallery, videos.

### 3.3 Booking System
Instant & inquiry booking, guest/registered users, booking status, payment
status, cancellation, traveler details, coupons, confirmation.

### 3.4 Customer Management
Profiles, booking history, payment history, inquiries, wishlist (optional).

### 3.5 Admin Dashboard
Tours, destinations, bookings, customers, inquiries, reviews, coupons,
reports, calendar, notifications, roles, settings.

### 3.6 Mobile Notifications
Notify on new booking, inquiry, question, cancellation, payment, review, and
upcoming trips via Firebase/Telegram/WhatsApp.

### 3.7 Reports
Sales, revenue, popular tours, monthly/yearly bookings, customer &
cancellation reports.

### 3.8 Pricing
Fixed, seasonal, group, child, private, discounts, coupons.

### 3.9 Security
Spam protection, reCAPTCHA, Cloudflare, backups, activity logs, realistic
copy protection.

> **Suggestion:** "Copy protection" for a public website has real limits —
> anything rendered in a browser can technically be copied. Treat this as
> *friction*, not a guarantee: disable right-click/text-selection on
> image/gallery areas, watermark tour photos, block hotlinking via
> `.htaccess`/CDN rules, and rate-limit scraping via Cloudflare. Don't
> over-invest here at the cost of accessibility or SEO.

### 3.10 Performance
Caching, CDN, image optimization, lazy loading, SEO.

---

## 4. Development Requirement

The build is split into four sections, detailed below:

1. [Development Environment](#5-development-environment)
2. [Dashboard Admin](#6-dashboard-admin)
3. [Website](#7-website)
4. [Website Customer Panel](#8-website-customer-panel)

---

## 5. Development Environment

Windows development machine. Node.js already installed on host. No local
PHP/MySQL/Apache — these run in Docker so the host stays clean and the setup
is reproducible for any future teammate.

> **Suggestion:** Keep Node/npm on the host (already installed) for the
> Tailwind/Vite asset build — no need to containerize it. Only PHP, Apache,
> and MySQL need containers, per the original ask. See `docker-compose.yml`
> and `docker/` in the project root for the actual environment.

---

## 6. Dashboard Admin

A responsive admin dashboard with a dedicated login flow. Roles/permissions
are managed via **Spatie Laravel-Permission**. Navigation modules:

| # | Module | Summary |
|---|---|---|
| 1 | Destinations | Countries/regions (Nepal, India, Bhutan, Tibet), self-referencing for future sub-regions |
| 2 | Categories | Trekking, Expedition, Peak Climbing, Tour, Special Activities — with sub-categories via `parent_id` |
| 3 | Difficulty Levels | Easy, Moderate, Challenging, Strenuous |
| 4 | Tours | Multi-destination & multi-category tours; highlights; day-by-day itinerary with images; includes/excludes; gallery; FAQs; departures; seasonal pricing; pricing tiers (group/child/private/solo) |
| 5 | Coupons | Percentage/fixed, usage limits, min booking amount, max discount cap, scoped to all tours / specific tours / specific categories (specific → category → all, in that priority) |
| 6 | Guides | Guide profiles selectable by customers at booking time, or assignable by admin |
| 7 | Customer Details | Profiles for all registered users (customer/vendor/admin/staff share one `users` table) |
| 8 | Inquiries | Per-tour inquiries; status new/responded/closed; filterable |
| 9 | Reviews | Moderation queue; only approved reviews show on the site |
| 10 | Bookings | Create/edit bookings, traveler details, payment & booking status, cancellation (admin or customer-initiated), partial payments via a `payments` table, full audit trail via `booking_status_logs` |
| 11 | Reports | Sales, revenue, bookings by month/year/customer/destination/category, cancellations, most-booked tours |
| 12 | Calendar | Upcoming or Running tour departure dates |
| 13 | Notifications | Bell icon with unseen count, notification list linking to source record |
| 14 | Roles & Permissions | Spatie Laravel-Permission |
| 15 | Blogs | Single-tier category + posts |
| 16 | Pages | Static pages: About, Contact, Terms, Privacy Policy, etc. |
| 17 | Gallery | Category-wise media gallery |
| 18 | FAQ | Category-wise FAQs (Booking, Payment, Visa & Permits, Trekking Gear, ...) |
| 19 | Settings | Global site settings |
| 20 | Newsletter Subscribers | View subscribers; send news/coupons by email |

> **Suggestion:** Add a **Settings → Theme** page (not in the original list)
> so the admin can change the accent/primary colors of the Admin Dashboard,
> Website, and Customer Panel independently, without a developer touching
> code. See the Dynamic Theme System note in §7.

---

## 7. Website

Public marketing + booking site, Tailwind CSS, styled after the reference
site's layout but with the **current site's navigation structure**:

```
Home | Nepal | India | Bhutan | Tibet | Multi-Country | Hotel | Vehicle
| About Us | Contact Us | Blog | Gallery | FAQ | ...
```

Each destination (Nepal, India, Bhutan, Tibet) expands to its categories
(Trekking, Expedition, Peak Climbing, Tour, Special Activities, and
combinations). **Multi-Country** expands to destination combinations
(Nepal + India, Nepal + India + Bhutan, ...) each with their own category
breakdown. This entire nav tree is data-driven from `destinations` and
`categories` (see the nav query patterns in the schema doc) — not hardcoded,
so admin changes to destinations/categories reflect immediately.

**Hotel | Vehicle** — nav entries reserved for phase 2 (managed from the
dashboard, booked by customers); schema stubs exist today, no UI this phase.

Key pages/flows:
- Tour listing with pagination and filters; tour detail page with an image
  slider and tabs (description, availability, itinerary, map, includes/
  excludes, reviews & ratings).
- Customers can submit reviews and ratings.
- Booking flow supports guest and logged-in users; payment is verified via
  customer-uploaded payment receipt/bill.
- Blog list + detail (paginated), About/Contact pages, Gallery page,
  FAQ page (accordion, grouped by category, deep-linkable per category).

> **Suggestion (Dynamic Theme System):** Both the Website and the Customer
> Panel (and the Admin Dashboard) pull their primary/secondary/accent colors
> from the same admin-editable `settings` table, exposed as CSS custom
> properties. This means a color/branding refresh never requires a
> developer or a rebuild — the admin changes it from Settings → Theme.

---

## 8. Website Customer Panel

Shown after customer login:

- Profile (view/update)
- Booking history
- Payment history
- Inquiries
- Wishlist

---

## Suggestions Summary (things added beyond the original ask)

1. Single Laravel monolith over separate apps per panel (§1).
2. Explicit phase-2 schema stubs for hotel/vehicle/flight/chatbot/currency so
   "build the structure now" is concretely tracked (§1.2).
3. Realistic framing of "copy protection" as friction, not a guarantee (§3.9).
4. Admin-editable, per-panel **dynamic theme system** (§6, §7) — not
   originally listed as a module, added to support the "dynamic theme color
   system" requirement cleanly.
5. Reusable CRUD scaffold pattern for the ~16 simple admin modules (Blogs,
   Pages, Gallery, FAQ, Newsletter, etc.) so they're generated consistently
   rather than hand-built one at a time — see the project's `stubs/module/`
   and `php artisan module:make` command.
