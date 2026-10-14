# Zapio VMS 2.0 — Enterprise Visitor Management System

[![Laravel](https://img.shields.io/badge/Laravel-11.x%20%2F%2013.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)](https://www.postgresql.org)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-Proprietary-000000?style=for-the-badge)](LICENSE)

**Zapio VMS 2.0** (branded operationally as **Madkhal** — *Arabic for "Entrance / Entry"*) is an enterprise-grade, multi-tenant Visitor Management & Physical Security System built on **Laravel** and **PostgreSQL**.

---

## Key Features

- **Multi-Tenancy Isolation**: Database-enforced multi-tenancy using `organization_id` discriminator with composite foreign keys `(organization_id, parent_id)` preventing cross-tenant data leakage.
- **Fail-Closed Security Scope**: Custom `TenantScope` and `BelongsToOrganization` Eloquent traits with automatic query scoping, background job context support, and fail-closed query blocking.
- **Visitor Lifecycle Management**: Pre-registration, QR access passes, walk-in registration, photo capture, and check-in/out tracking.
- **Emergency Evacuation Roll-Call**: Real-time roll-call incident management, elapsed timer, floor-level location filters, and one-click "Mark Safe" workflows for first responders.
- **Security & Compliance**:
  - Live watchlist fuzzy-name & exact-document screening.
  - Interactive NDA & legal agreement builder with versioning, draw/typed signatures, and signed audit trails.
  - Government ID encryption at rest (`id_number`) with salted SHA-256 hashes for lookup.
  - UAE Personal Data Protection Law (PDPL) compliance with configurable retention & purge rules.
- **Staff Attendance & Kiosk Terminal**: Integrated barcode/QR scanning for employee attendance punches, shift schedules, and badge layout customization.
- **Role-Based Access Control (RBAC)**: Fine-grained permission matrix mapping 15 permission flags across 7 tenant roles (*Super Admin*, *Admin*, *Manager*, *Security*, *Receptionist*, *Host*, *Viewer*).

---

## System Architecture & Data Spine

```
[Organizations] ──> [Sites] ──> [Departments / Employees] ──> [Visits]
                                                                │
         ┌───────────────────┬───────────────────┬──────────────┴────────────┐
         ▼                   ▼                   ▼                           ▼
    [Approvals]       [Agreements]          [Watchlist]                  [Badges]
         │                   │                   │                           │
         └───────────────────┴───────────────────┴──────────────┬────────────┘
                                                                ▼
                                                [Notifications / Reports / Audit]
```

### Module Breakdown (39 Business Tables)

1. **Core / Tenancy**: `organizations`, `sites`, `buildings`, `floors`, `areas`, `departments`, `organization_settings`, `integration_settings`, `countries`, `id_types`
2. **Identity & Access**: `users`, `roles`, `permissions`, `role_permissions`, `user_roles`, `audit_logs`
3. **Employees & Attendance**: `employees`, `shifts`, `attendance_records`, `employee_access_credentials`
4. **Visitors & Visit Lifecycle**: `visitors`, `visits`, `visitor_types`, `visit_purposes`, `invitations`, `approvals`
5. **Security & Compliance**: `watchlist_entries`, `watchlist_matches`, `agreements`, `agreement_sections`, `visit_agreements`
6. **Kiosk, Badges & Emergency**: `kiosk_devices`, `kiosk_checkin_events`, `badge_templates`, `badges`, `evacuation_events`, `evacuation_records`
7. **Notifications**: `notification_templates`, `notifications`

---

## Local Setup Instructions (Laravel Herd)

### Requirements
- **PHP 8.2+** (PHP 8.4 recommended)
- **PostgreSQL 14+** (or MySQL / SQLite for development)
- **Composer 2.x**
- **Node.js 18+** & **npm**

### Step-by-Step Installation

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/ZapioVMS/VMSdxb.git
   cd VMSdxb
   ```

2. **Install PHP Dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Configure your PostgreSQL connection in `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=zapio_vms
   DB_USERNAME=postgres
   DB_PASSWORD=your_password
   ```

4. **Run Migrations & Seed Demo Data**:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Install & Build Frontend Assets**:
   ```bash
   npm install
   npm run dev
   ```

6. **Serve Application**:
   If using **Laravel Herd**, the application is automatically available at `http://zapio-vms.test`. Alternatively:
   ```bash
   php artisan serve
   ```

---

## Demo Credentials

After running `php artisan db:seed`, log in with:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Super Admin** | `allanjacob03@gmail.com` | `password` |
| **HR Manager** | `sarah.khan@zapio.ae` | `password` |

---

## Testing & Quality Assurance

Run PHP syntax verification and test suite:
```bash
php artisan test
```

---

## License

Proprietary software. All rights reserved by **Zapio VMS**.
