# Modern Customer Relationship Management (CRM) System

A robust, premium Customer Relationship Management (CRM) application built with **Laravel 10, Inertia.js, Vue 3 (Composition API), and Tailwind CSS**. This application integrates **Stripe Hosted Checkout** for automated customer billing and **Mailtrap** for transaction-based email delivery.

---

## 🚀 Stack & Technologies Used

- **Backend**: Laravel 10 (PHP ^8.1 / 8.2)
- **Frontend**: Inertia.js with Vue 3 (Vue Composition API with `<script setup>`) & Vite
- **Styling**: Tailwind CSS & Tailwind Forms plugin
- **Database**: MySQL (relational design with One-to-Many relations and Cascade deletions)
- **Email Delivery**: SMTP via Mailtrap (for testing & live production email dispatching)
- **Payment Gateway**: Stripe API (`stripe/stripe-php`) with Hosted Checkout and Auto-Settle Hook

---

## 📂 Project Directory Structure

```text
crm-project/
├── app/
│   ├── Events/
│   │   └── UserRegistered.php            # Dispatched when a new Admin signs up
│   ├── Listeners/
│   │   └── SendWelcomeEmail.php          # Catches registration event and fires SMTP mailer
│   ├── Mail/
│   │   ├── WelcomeNewUser.php            # Mail configuration for registration welcome
│   │   ├── CustomerInvoiceMail.php       # Mail config containing the dynamic Stripe URL
│   │   └── PaymentConfirmation.php       # Transaction confirmation mail config (placeholder)
│   ├── Models/
│   │   ├── User.php                      # Admin/User Authentication Model
│   │   ├── Customer.php                  # Client profile model (hasMany Invoices & Proposals)
│   │   ├── Invoice.php                   # Invoice model (belongsTo Customer)
│   │   ├── Proposal.php                  # Proposal model (belongsTo Customer)
│   │   └── Transaction.php               # Payments auditing ledger (belongsTo Invoice)
│   └── Http/Controllers/
│       ├── Auth/                         # Core auth controllers (Register/Login session handlers)
│       ├── CustomerController.php        # CRUD for Customers with N+1 eager-loading protection
│       ├── ProposalController.php        # Proposal CRUD linked directly to clients
│       ├── InvoiceController.php         # Invoice creation and tracking CRUD
│       ├── StripePaymentController.php   # Handles Checkout session generation & success handlers
│       └── TransactionController.php     # Admin dashboard transaction feed controller
├── database/migrations/
│   ├── 2014_10_12_000000_create_users_table.php
│   ├── 2026_06_01_050852_create_customers_table.php
│   ├── 2026_06_01_070030_create_proposals_table.php
│   ├── 2026_06_01_082728_create_invoices_table.php
│   └── 2026_06_02_041643_create_transactions_table.php
├── resources/
│   ├── js/
│   │   ├── Layouts/                      # Global UI Frame & Responsive Navigation
│   │   └── Pages/
│   │       ├── Auth/                     # Login, Registration, & Reset views
│   │       ├── Customers/                # Customer CRUD Index, Create, and Edit components
│   │       ├── Proposals/                # Proposal list & Creation views
│   │       ├── Invoices/                 # Invoices table view & Creation panel
│   │       ├── Transactions/             # Ledger showing client payments & Stripe Sessions
│   │       └── Dashboard.vue             # Core landing component showing admin statistics
│   └── views/
│       ├── app.blade.php                 # App layout anchor for Inertia
│       └── emails/
│           ├── welcome.blade.php         # Rich HTML email template for new Admins
│           └── invoice.blade.php         # Transactional email with Stripe Pay CTA button
├── routes/
│   ├── auth.php                          # Guests/Auth Breeze route declarations
│   └── web.php                           # Main application web resource controllers
└── config/services.php                   # Third-party integrations credentials configuration
```

---

## 🛠️ Detailed Feature Overview

### 1. Admin Authentication & Onboarding
- **Login / Register**: Fully secure authentication powered by Laravel Breeze and Inertia.
- **Success Welcome Email**: Immediately upon registering, a `UserRegistered` event is fired. The `SendWelcomeEmail` listener intercepts it and sends a rich HTML onboarding message using the **Mailtrap** SMTP configuration.

### 2. Stats Dashboard & Account Controls
- **Dashboard Overview**: A landing dashboard view (`Dashboard.vue`) acting as the administrative nerve center.
- **Navigation Controls**: Includes layout-integrated sidebar controls, links to profile settings (`Profile/Edit.vue`), and a single-click secure Logout action.

### 3. Customer Directory (CRUD + Status Controls)
- **Interactive Directory**: Admin can list all clients, create new profiles, edit contact information, and delete profiles.
- **Status Toggles**: Instantly change client status (`active` / `inactive`).
- **N+1 Avoidance**: Eager-loads relational arrays (`invoices` and `proposals`) in a single query database-side, maximizing speed.

### 4. Proposals Portal
- **Drafting & Tracking**: Create and bind rich commercial proposals directly to customer records.
- **Attributes**: Supports tracking Title, Description, Value (currency), and dynamic commercial statuses (`draft`, `sent`, `accepted`, `declined`).

### 5. Invoices & Automated Stripe Workflows
- **Invoice Listing**: Create invoice identifiers specifying Amounts, Due Dates, and Statuses (`unpaid`, `paid`, `overdue`).
- **Stripe Payment dispatch (`Send Invoice` button)**: 
  1. Admin clicks **Send Invoice** in the UI.
  2. The system makes an API call to Stripe using your secret credentials.
  3. Stripe creates a **Hosted Checkout Session** for the precise amount (configured to LKR).
  4. An email template is generated containing a secure **Pay Now** CTA button pointing directly to Stripe's payment portal, and dispatched to the customer's email.
- **Hosted Gateway Redirection**: Clicking the button redirects the client securely to Stripe Checkout (supports international credit cards, mobile wallets, and Apple/Google Pay in a sandboxed test environment).
- **Auto-Settlement Hook**: Upon successful checkout, Stripe redirects the client back to `/payment/{invoice}/success?session_id={CHECKOUT_SESSION_ID}`. The system:
  1. Instantly flips the database invoice status from `unpaid` to `paid`.
  2. Generates an auditing transaction entry with the Stripe Session ID, Amount Paid, and Currency.
  3. Flashes a success banner and brings the Admin back to the invoice page.

### 6. Transactions Auditing Ledger
- **Master Ledger**: Provides the admin with a read-only list of all completed payment events.
- **Eager Loading**: Automatically resolves the invoice data and the corresponding parent customer information to trace exactly who paid which invoice and when.

---

## ⚙️ Local Installation & Setup Guide

Follow these steps to run the CRM project locally on your machine:

### 1. Clone & Enter Project Folder
Ensure you navigate to the root folder of the Laravel project:
```bash
cd crm-project
```

### 2. Install Dependencies
Install all required backend PHP packages and frontend Node modules:
```bash
# Install PHP vendor packages
composer install

# Install JS node modules
npm install
```

### 3. Setup Configuration Variables
Copy the sample environment file to create your active configurations:
```bash
cp .env.example .env
```

Open `.env` in your editor and update the following configuration sections:

#### Database Setup
Create a new MySQL database named `crm_project` and update database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_project
DB_USERNAME=root
DB_PASSWORD=YOUR_DATABASE_PASSWORD
```

#### Mailtrap SMTP Configuration
Log in to your Mailtrap dashboard, select your inbox settings, and paste the configurations:
```env
MAIL_MAILER=smtp
MAIL_HOST=live.smtp.mailtrap.io   # or smtp.mailtrap.io for sandbox Testing
MAIL_PORT=587
MAIL_USERNAME=YOUR_MAILTRAP_USERNAME
MAIL_PASSWORD=YOUR_MAILTRAP_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-verified-domain@demomailtrap.co"
MAIL_FROM_NAME="CRM System Administrator"
```

#### Stripe Keys Integration
Access your Stripe Developer Dashboard in test-mode and paste your API keys:
```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

### 4. Build App Assets & Boot Server
Generate your application key and compile the frontend scripts:
```bash
# Generate the application encryption key
php artisan key:generate

# Run migrations to build DB structure and tables
php artisan migrate

# Compile/Build frontend assets
npm run build
```

### 5. Running the Local Servers
To run the CRM in development hot-reload mode, run these two commands in separate terminal sessions:

**Terminal 1 (Laravel Dev Server)**:
```bash
php artisan serve --port=5050
```

**Terminal 2 (Vite Front-end Watcher)**:
```bash
npm run dev
```

Your system will be available at: **`http://127.0.0.1:5050`**
