# Lake Garda Rentals — Laravel website

Production-oriented Laravel 12 application for two holiday apartments in Garda on Lake Garda (Italy): lead generation via phone, optional WhatsApp, and booking/contact forms (no instant booking). Includes a secure admin area for content, apartments, inquiries, FAQs, and SEO fields.

## Requirements

- PHP 8.2+
- Composer
- Node.js 20+ and npm
- Database: SQLite (default) or MySQL

## Setup

1. **Clone / enter the project directory**

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Edit `.env`: set `APP_URL`, mail settings, `ADMIN_EMAIL` / `ADMIN_PASSWORD`, and public site variables (`SITE_PHONE`, `SITE_EMAIL`, `WHATSAPP_NUMBER`, etc.). For MySQL, set `DB_CONNECTION=mysql` and credentials.

4. **Database**

   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```

5. **Frontend**

   ```bash
   npm install
   npm run build
   ```

   For development with hot reload: `npm run dev`

6. **Admin login**

   After seeding, sign in at `/login` with:

   - Email: value of `ADMIN_EMAIL` (default `admin@lakegardarentals.local`)
   - Password: value of `ADMIN_PASSWORD` (default `changeme`)

   Change these in production. Public self-registration is disabled.

## Features

- Public pages: Home, Lake Garda, Apartments (list + slug detail), Contact, Thank-you (noindex)
- Forms: `POST /inquiry` (booking-style validation), `POST /contact` (softer validation), honeypot + rate limiting
- Email: `InquiryReceived` mailable to `INQUIRY_NOTIFY_EMAIL` or `MAIL_FROM_ADDRESS`
- SEO: meta/OG/Twitter via Blade component, JSON-LD (Organization, WebSite, VacationRental, Breadcrumbs, FAQ where relevant), `/sitemap.xml`, `/robots.txt`
- Admin: CRUD for apartments (images, seasonal prices), amenities, inquiries, pages (JSON blocks + SEO), FAQs, testimonials

## Demo content

Seeded apartments: **Appartamento Orchidea Garda** and **Appartamento Lavanda Garda** (with sample copy and Booking.com URLs stored as reference). Replace images by uploading in Admin → Apartments.

## Production notes

- Set `APP_DEBUG=false`, run `php artisan config:cache route:cache view:cache`
- Use a real mail transport (`MAIL_MAILER=smtp` or provider) and queue worker if using `QUEUE_CONNECTION=database`
- Ensure HTTPS and correct `APP_URL` for canonical URLs and sitemap

## License

MIT (Laravel default; adjust as needed for your project).
