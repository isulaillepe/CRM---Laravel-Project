# 💼 Advanced CRM System with Stripe and Mailtrap

An elegant, modern CRM system built with **Laravel**, **Vue 3 (Inertia.js)**, **Tailwind CSS**, **Stripe Checkout**, and **Mailtrap**. This application provides a complete dashboard for administrators to manage customers, dispatch proposals, send invoices, handle secure online card payments, and monitor transactions.

---

## 🚀 Key Features

### 🔐 1. Admin Authentication & Dashboard
- **Admin Access:** Registration and login screens styled with a sleek, modern UI.
- **Registration Success Email:** Automatically dispatches a welcome email via Mailtrap upon successful account creation.
- **Admin Dashboard:** Displays key navigation links, user profile details, and a secure logout action.

### 👥 2. Customer Directory Management
- Full **CRUD Operations** (Create, Read, Update, Delete) for customers.
- **Status Toggling:** Easy activation/deactivation switch.

### 📝 3. Proposals Module
- Bind structured proposals directly to registered customers.
- Manage status transitions (e.g., *Draft*, *Sent*, *Accepted*, *Declined*) with CRUD controls.

### 💳 4. Invoices & Stripe Checkout Lifecycle
- **Generate Invoices:** Create and link invoices directly to customers (CRUD).
- **Email Delivery:** Send invoice notifications directly to client emails with a **secure payment button** generated via Stripe.
- **Stripe Hosted Gateway:** Clicking the payment button redirects the client to a Stripe-hosted payment interface supporting credit card billing.
- **Auto-Flipped Status:** Upon successful payment validation, the invoice state is automatically toggled to `Paid` in the database.

### 📊 5. Audit Ledger & Transaction Tracking
- **Transaction History:** A dedicated ledger detailing successful customer transactions, tracking Stripe session identifiers, invoice amounts, currencies, and timestamps.

---

## 🛠️ Tech Stack

- **Backend:** Laravel 10 (PHP 8+)
- **Frontend:** Vue 3, Inertia.js, Tailwind CSS
- **Database:** MySQL
- **Payments:** Stripe PHP SDK
- **Testing Mail Server:** Mailtrap (SMTP)
- **Dev Servers:** 
  - **Laravel application server:** Port `5050`
  - **Vite compilation server:** Port `5051`

---

## ⚙️ Local Installation & Setup

Follow these steps to set up the project on your local machine:

### 1. Clone & Install Dependencies
First, clone the repository and install the backend and frontend dependencies:

```bash
# Install PHP Composer dependencies
composer install

# Install NPM dependencies
npm install
```

### 2. Configure Environment Variables
Copy the example environment file and configure the settings:

```bash
cp .env.example .env
```

Open `.env` and fill out your database, Mailtrap, and Stripe credentials:

```env
# Application Ports
APP_URL=http://127.0.0.1:5050
SERVER_PORT=5050

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_project
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

# Mailtrap Settings
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS="no-reply@crm-system.com"
MAIL_FROM_NAME="CRM System"

# Stripe Gateway Credentials
STRIPE_KEY=pk_test_your_publishable_key
STRIPE_SECRET=sk_test_your_secret_key
```

### 3. Generate Key & Run Migrations
Generate the Laravel application key and initialize the database schema:

```bash
# Generate key
php artisan key:generate

# Run migrations and table creation
php artisan migrate
```

### 4. Build Assets & Start Servers
Start the application and assets compiler on their configured ports (`5050` & `5051`):

#### **Terminal Window 1: Laravel Server**
```bash
php artisan serve
# App runs at: http://127.0.0.1:5050
```

#### **Terminal Window 2: Vite Dev Server**
```bash
npm run dev
# Assets run at: http://localhost:5051
```

*(Note: Ensure that the leftover `public/hot` file is cleared if you switch between dev and production modes using `rm -f public/hot`).*

---

## 📁 System Architecture & Routes

The application features a secure, structured routing architecture under auth middleware:

| Route Path | HTTP Method | Action / Controller | Description |
|---|---|---|---|
| `/register` | GET/POST | Auth Controllers | Create Admin profile & triggers Welcome mail |
| `/login` | GET/POST | Auth Controllers | Secure Admin Login |
| `/dashboard` | GET | Inertia View | Landing page with profile info |
| `/customers` | GET/POST/PUT/DELETE | `CustomerController` | Customers directory and actions |
| `/proposals` | GET/POST/DELETE | `ProposalController` | Proposals creation and directory |
| `/invoices` | GET/POST/DELETE | `InvoiceController` | Invoices management dashboard |
| `/invoices/{invoice}/send` | POST | `StripePaymentController` | Dispatches payment checkout email |
| `/payment/{invoice}/success` | GET | `StripePaymentController` | Stripe payment success handler |
| `/payment/{invoice}/cancel` | GET | `StripePaymentController` | Stripe payment cancellation handler |
| `/transactions` | GET | `TransactionController` | Ledger listing transaction histories |

---

## 📧 Email & Payment Integration Details

### Mailtrap Setup
Ensure Mailtrap credentials are set in your `.env`. All sent messages (registration confirmation and invoices) will appear in your Mailtrap developer inbox.

### Stripe Integration Workflow
1. When an admin clicks **"Send Invoice"** on an invoice card:
   - Laravel initiates a secure Stripe Checkout Session with a defined currency (`LKR`), amount, and name.
   - It compiles an email template with a secure checkout link containing `{CHECKOUT_SESSION_ID}`.
   - The email is sent to the customer via SMTP.
2. The customer clicks the **"Pay Now"** button in their email and is redirected to Stripe's secure page.
3. Once Stripe accepts the credit card details:
   - The checkout redirects back to `/payment/{invoice}/success?session_id=...`.
   - The system validates the invoice, changes the database status to `paid`, and writes an audit entry in the `transactions` table.

---

## 🧪 Running Tests

To run the automated feature and unit tests (e.g. Invoices, Customer validation, API paths):

```bash
php artisan test
```

---

## 📝 License

This CRM system is open-source software licensed under the [MIT license](LICENSE).
