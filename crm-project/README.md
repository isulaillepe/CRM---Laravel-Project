# Central CRM — Customer Relationship Management System

A premium, full-stack CRM application for managing customers, proposals, invoices, and payments. Built with **Laravel 10**, **Inertia.js**, **Vue 3 Composition API**, and **Tailwind CSS**, featuring **Stripe Checkout** for automated billing and **Kanban-style pipeline boards** for visual deal tracking.

---

## 🚀 Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| **Backend Framework** | Laravel | ^10.10 |
| **Frontend Framework** | Vue 3 (Composition API, `<script setup>`) | ^3.4 |
| **Client-Server Bridge** | Inertia.js (Vue 3 adapter) | ^1.0 |
| **Build Tool** | Vite | ^5.0 |
| **Styling** | Tailwind CSS + Tailwind Forms | ^3.2 |
| **UI Components** | PrimeVue (unstyled mode) | ^4.5 |
| **Charts** | Chart.js | ^4.5 |
| **Authentication** | Laravel Breeze (Session-based) | ^1.29 |
| **Payment Gateway** | Stripe Checkout (`stripe/stripe-php`) | ^20.2 |
| **Route Helpers** | Ziggy (Laravel routes in JS) | ^2.0 |
| **Database** | MySQL with Eloquent ORM | — |
| **Email** | Laravel Mailables via SMTP (Mailtrap) | — |
| **PHP** | ^8.1 (platform locked to 8.2) | — |

---

## ✨ Features

### 📊 Analytics Dashboard
- **Financial summary cards** — Total Invoiced, Paid, Unpaid, and Overdue amounts at a glance.
- **Doughnut chart** — Visual invoice status distribution (Paid vs Unpaid vs Overdue) via Chart.js.
- **Activity line chart** — Trend analysis of billing activity over time.
- **Sparkline trend rows** — Per-metric visual sparklines for key financial segments.
- **Quick-action buttons** — Create customers, proposals, or invoices directly from the dashboard.

### 👥 Customer Management (CRUD)
- Full create, edit, and delete operations with inline modal forms.
- Real-time **search/filter** by name, email, or phone.
- Inline **status toggling** (Active / Inactive) via dropdown without leaving the page.
- **N+1 query prevention** — Eager-loads related invoices and proposals in every listing.

### 📝 Proposals Pipeline
- **List View** — Tabular overview with full CRUD operations.
- **Kanban Board** — Drag-and-drop pipeline with four swim lanes: Draft → Sent → Accepted → Declined.
- **Optimistic UI updates** — Status changes render instantly; rolled back automatically on server error.
- Live deal valuations per column and total pipeline metrics.
- Client-side search/filter across all board cards.

### 🧾 Invoice Management
- **List View** — Sortable invoice table with customer associations, amounts, and due dates.
- **Kanban Board** — Drag-and-drop board with three lanes: Unpaid → Paid → Overdue.
- **Paid invoice protection** — Paid invoices are locked from editing and dragging (enforced on both frontend and backend).
- **Manual payment tracking** — When an admin manually marks an invoice as "paid" via the board, a Transaction audit record is automatically created with a `MANUAL_PAYMENT_*` session ID.

### 💳 Stripe Payment Workflow
1. Admin clicks **Send Invoice** → system creates a Stripe Hosted Checkout session.
2. A styled HTML email is dispatched to the customer with a secure **Pay Now** button linking to Stripe.
3. Customer completes payment on Stripe's hosted page.
4. Stripe redirects back to the app → invoice status flips to `paid`, a Transaction record is created, and a **Payment Confirmation** receipt email is sent to the customer.
5. Admin sees a success flash notification on the invoices page.

### 💰 Transactions Ledger
- Read-only audit trail of all completed payments.
- Deep eager-loading: `Transaction → Invoice → Customer` resolved in a single query chain.
- Displays Stripe session IDs, amounts, currency, and timestamps.

### 🔐 Authentication & Onboarding
- Laravel Breeze session-based auth (Login / Register / Password Reset).
- **Welcome email** dispatched automatically on registration via the `UserRegistered` event + `SendWelcomeEmail` listener.
- Responsive sidebar navigation with user profile dropdown.

---

## 🗄️ Database Schema

### Entity Relationships

```
Customer (1) ──── (∞) Proposal
Customer (1) ──── (∞) Invoice
Invoice  (1) ──── (∞) Transaction
```

All foreign keys use `ON DELETE CASCADE` — deleting a customer removes all associated proposals, invoices, and transactions automatically.

### Table Definitions

**`customers`**
| Column | Type | Constraints |
|--------|------|-------------|
| `id` | BIGINT | Primary Key, Auto-increment |
| `name` | VARCHAR(255) | Required |
| `email` | VARCHAR(255) | Required, Unique |
| `phone` | VARCHAR(255) | Nullable |
| `status` | VARCHAR(255) | Default: `active` (`active` \| `inactive`) |
| `created_at` / `updated_at` | TIMESTAMP | Auto-managed |

**`proposals`**
| Column | Type | Constraints |
|--------|------|-------------|
| `id` | BIGINT | Primary Key |
| `customer_id` | BIGINT | Foreign Key → `customers.id` (CASCADE) |
| `title` | VARCHAR(255) | Required |
| `description` | TEXT | Required |
| `value` | DECIMAL(10,2) | Required |
| `status` | VARCHAR(255) | Default: `draft` (`draft` \| `sent` \| `accepted` \| `declined`) |
| `created_at` / `updated_at` | TIMESTAMP | Auto-managed |

**`invoices`**
| Column | Type | Constraints |
|--------|------|-------------|
| `id` | BIGINT | Primary Key |
| `customer_id` | BIGINT | Foreign Key → `customers.id` (CASCADE) |
| `invoice_number` | VARCHAR(255) | Required, Unique |
| `amount` | DECIMAL(10,2) | Required |
| `status` | VARCHAR(255) | Default: `unpaid` (`unpaid` \| `paid` \| `overdue`) |
| `due_date` | DATE | Required |
| `created_at` / `updated_at` | TIMESTAMP | Auto-managed |

**`transactions`**
| Column | Type | Constraints |
|--------|------|-------------|
| `id` | BIGINT | Primary Key |
| `invoice_id` | BIGINT | Foreign Key → `invoices.id` (CASCADE) |
| `stripe_session_id` | VARCHAR(255) | Unique |
| `amount_paid` | DECIMAL(10,2) | Required |
| `currency` | VARCHAR(255) | Default: `LKR` |
| `payment_status` | VARCHAR(255) | Required |
| `created_at` / `updated_at` | TIMESTAMP | Auto-managed |

---

## 🌐 Routes Map

### Public Routes

| Method | URI | Controller | Description |
|--------|-----|-----------|-------------|
| GET | `/` | Closure | Welcome/landing page |

### Authenticated Routes (require login)

| Method | URI | Controller@Method | Route Name | Description |
|--------|-----|-------------------|------------|-------------|
| GET | `/dashboard` | Closure | `dashboard` | Analytics dashboard with aggregated metrics |
| **Customers** | | | | |
| GET | `/customers` | `CustomerController@index` | `customers.index` | List all customers |
| GET | `/customers/create` | `CustomerController@create` | `customers.create` | Create customer form |
| POST | `/customers` | `CustomerController@store` | `customers.store` | Save new customer |
| GET | `/customers/{id}/edit` | `CustomerController@edit` | `customers.edit` | Edit customer form |
| PUT/PATCH | `/customers/{id}` | `CustomerController@update` | `customers.update` | Update customer |
| DELETE | `/customers/{id}` | `CustomerController@destroy` | `customers.destroy` | Delete customer |
| **Proposals** | | | | |
| GET | `/proposals` | `ProposalController@index` | `proposals.index` | List view |
| GET | `/proposals/board` | `ProposalController@board` | `proposals.board` | Kanban pipeline board |
| GET | `/proposals/create` | `ProposalController@create` | `proposals.create` | Create form |
| POST | `/proposals` | `ProposalController@store` | `proposals.store` | Save new proposal |
| GET | `/proposals/{id}/edit` | `ProposalController@edit` | `proposals.edit` | Edit form |
| PUT/PATCH | `/proposals/{id}` | `ProposalController@update` | `proposals.update` | Update proposal |
| DELETE | `/proposals/{id}` | `ProposalController@destroy` | `proposals.destroy` | Delete proposal |
| **Invoices** | | | | |
| GET | `/invoices` | `InvoiceController@index` | `invoices.index` | List view |
| GET | `/invoices/board` | `InvoiceController@board` | `invoices.board` | Kanban billing board |
| GET | `/invoices/create` | `InvoiceController@create` | `invoices.create` | Create form |
| POST | `/invoices` | `InvoiceController@store` | `invoices.store` | Save new invoice |
| GET | `/invoices/{id}/edit` | `InvoiceController@edit` | `invoices.edit` | Edit form |
| PUT/PATCH | `/invoices/{id}` | `InvoiceController@update` | `invoices.update` | Update invoice |
| DELETE | `/invoices/{id}` | `InvoiceController@destroy` | `invoices.destroy` | Delete invoice |
| **Payments** | | | | |
| POST | `/invoices/{invoice}/send` | `StripePaymentController@sendInvoiceEmail` | `invoices.send` | Create Stripe session + email customer |
| GET | `/payment/{invoice}/success` | `StripePaymentController@paymentSuccess` | `payment.success` | Handle successful payment callback |
| GET | `/payment/{invoice}/cancel` | `StripePaymentController@paymentCancel` | `payment.cancel` | Handle cancelled payment |
| GET | `/transactions` | `TransactionController@index` | `transactions.index` | Transaction audit ledger |

---

## 📂 Project Structure

```
crm-project/
├── app/
│   ├── Http/Controllers/
│   │   ├── CustomerController.php         # Customer CRUD with eager-loading
│   │   ├── ProposalController.php         # Proposal CRUD + board() Kanban endpoint
│   │   ├── InvoiceController.php          # Invoice CRUD + board() + manual payment logic
│   │   ├── StripePaymentController.php    # Stripe session creation, payment callbacks, emails
│   │   └── TransactionController.php      # Read-only transaction listing
│   ├── Models/
│   │   ├── Customer.php                   # hasMany(Invoice), hasMany(Proposal)
│   │   ├── Invoice.php                    # belongsTo(Customer)
│   │   ├── Proposal.php                   # belongsTo(Customer)
│   │   ├── Transaction.php                # belongsTo(Invoice)
│   │   └── User.php                       # Auth model
│   ├── Mail/
│   │   ├── CustomerInvoiceMail.php        # Invoice email with Stripe checkout URL
│   │   ├── PaymentConfirmation.php        # Payment receipt email
│   │   └── WelcomeNewUser.php             # Registration welcome email
│   ├── Events/
│   │   └── UserRegistered.php             # Fired on new admin registration
│   └── Listeners/
│       └── SendWelcomeEmail.php           # Sends welcome email on registration
├── resources/
│   ├── js/
│   │   ├── app.js                         # Vue + Inertia + PrimeVue bootstrap
│   │   ├── Layouts/
│   │   │   └── AuthenticatedLayout.vue    # Sidebar navigation + responsive layout
│   │   ├── Pages/
│   │   │   ├── Dashboard.vue              # Analytics dashboard with charts
│   │   │   ├── Customers/
│   │   │   │   ├── Index.vue              # Customer table + Create/Edit modals
│   │   │   │   ├── Create.vue             # Standalone create form
│   │   │   │   └── Edit.vue               # Standalone edit form
│   │   │   ├── Proposals/
│   │   │   │   ├── Index.vue              # List view
│   │   │   │   ├── PipelineBoard.vue      # Drag-and-drop Kanban board
│   │   │   │   ├── Create.vue             # Create form
│   │   │   │   └── Edit.vue               # Edit form
│   │   │   ├── Invoices/
│   │   │   │   ├── Index.vue              # List view with send/email actions
│   │   │   │   ├── InvoiceBoard.vue       # Drag-and-drop Kanban board
│   │   │   │   ├── Create.vue             # Create form
│   │   │   │   └── Edit.vue               # Edit form
│   │   │   └── Transactions/
│   │   │       └── Index.vue              # Read-only payment ledger
│   │   └── Components/
│   │       ├── InvoiceStatusChart.vue      # Doughnut chart (Chart.js)
│   │       ├── ChartActivityLine.vue       # Activity line chart (Chart.js)
│   │       ├── InvoiceTrendRow.vue         # Sparkline trend rows (Chart.js)
│   │       ├── Dropdown.vue               # Reusable dropdown component
│   │       └── ...                        # Other Breeze UI components
│   └── views/
│       ├── app.blade.php                  # Root Blade shell (@inertia)
│       └── emails/
│           ├── invoice.blade.php          # Invoice email with "Pay Now" CTA
│           ├── confirmation.blade.php     # Payment receipt email
│           └── welcome.blade.php          # Welcome email for new admins
├── routes/
│   ├── web.php                            # All application routes
│   └── auth.php                           # Breeze authentication routes
├── database/
│   ├── migrations/
│   │   ├── create_customers_table
│   │   ├── create_proposals_table
│   │   ├── create_invoices_table
│   │   └── create_transactions_table
│   └── seeders/
│       └── DatabaseSeeder.php             # Seeds 10 test User records
├── config/
│   └── services.php                       # Stripe API key configuration
├── composer.json                          # PHP dependencies
├── package.json                           # Node dependencies
├── vite.config.js                         # Vite + Laravel plugin config
└── tailwind.config.js                     # Tailwind CSS configuration
```

---

## ⚙️ Local Installation & Setup

### Prerequisites

- **PHP** >= 8.1
- **Composer** >= 2.0
- **Node.js** >= 18.x and **npm**
- **MySQL** >= 5.7
- **Stripe account** (test mode) — [dashboard.stripe.com](https://dashboard.stripe.com)
- **Mailtrap account** (for email testing) — [mailtrap.io](https://mailtrap.io)

### 1. Clone & Install

```bash
git clone https://github.com/your-username/CRM---Laravel-Project.git
cd CRM---Laravel-Project/crm-project

# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and configure the following sections:

#### Database

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_project
DB_USERNAME=root
DB_PASSWORD=YOUR_DATABASE_PASSWORD
```

#### Mailtrap SMTP

```env
MAIL_MAILER=smtp
MAIL_HOST=live.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=YOUR_MAILTRAP_USERNAME
MAIL_PASSWORD=YOUR_MAILTRAP_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-verified-domain@demomailtrap.co"
MAIL_FROM_NAME="CRM System Administrator"
```

#### Stripe API Keys

Get your test-mode keys from the [Stripe Dashboard](https://dashboard.stripe.com/test/apikeys):

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

### 3. Database Setup

```bash
# Create the database first in MySQL, then:
php artisan migrate

# Optionally seed test users:
php artisan db:seed
```

### 4. Run Development Servers

Open **two terminal windows**:

**Terminal 1 — Laravel Backend:**
```bash
php artisan serve --port=5050
```

**Terminal 2 — Vite Frontend (hot-reload):**
```bash
npm run dev
```

Visit **http://127.0.0.1:5050** to access the application.

### 5. Production Build

```bash
npm run build
```

---

## 🏗️ Architecture Overview

This application uses the **Inertia.js** monolith architecture — there is no REST API. Instead:

1. **Laravel controllers** query the database using Eloquent and return `Inertia::render()` responses.
2. **Inertia** serialises the Eloquent data as JSON props and delivers them to the matching **Vue 3 page component**.
3. **Vue components** receive data as reactive `defineProps()` and render the UI.
4. **Form submissions** use Inertia's `useForm().post()` / `.patch()` / `.delete()` helpers, which send XHR requests to the same Laravel controllers.
5. **Controller redirects** (`redirect()->route(...)`) trigger automatic Inertia re-fetches, seamlessly updating the page with fresh data — no full page reload.

### Key Patterns

- **Eager Loading**: All controllers use `::with(...)` to prevent N+1 query issues.
- **Optimistic Updates**: Kanban boards update local state immediately, then sync with the server.
- **`redirect_to` Parameter**: Board views pass `?redirect_to=board` so shared controllers redirect back to the correct view.
- **Input Sanitisation**: All controllers use `strip_tags()` and `filter_var()` on user input before validation.
- **Idempotent Payments**: `paymentSuccess` checks `if ($invoice->status !== 'paid')` to prevent duplicate processing.

---

## 📧 Email Templates

| Email | Trigger | Template | Available Variables |
|-------|---------|----------|-------------------|
| Invoice Payment Link | Admin clicks "Send Invoice" | `views/emails/invoice.blade.php` | `$invoice`, `$checkoutUrl` |
| Payment Confirmation | Successful Stripe payment | `views/emails/confirmation.blade.php` | `$transaction` (with `->invoice->customer`) |
| Welcome Email | New admin registration | `views/emails/welcome.blade.php` | User data via event |

To customise email designs, edit the Blade templates in `resources/views/emails/`. All styling uses inline CSS for maximum email client compatibility.

---

## 📝 License

This project is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).
