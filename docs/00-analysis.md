# Phase 0 Analysis — Pharmaceutical Dealer Stock Management System
**Date:** 2026-10-18
**Laravel version:** 12.70.0 | **PHP:** 8.2.12 | **Status:** All questions resolved — awaiting APPROVED PHASE 0

---

## 1. Architecture Summary

### Framework & Runtime
| Item | Value |
|---|---|
| PHP | 8.2.12 (XAMPP) |
| Laravel | 12.70.0 |
| Test framework | PHPUnit 11 — sqlite :memory: |
| Queue driver | database |
| Cache driver | database |
| Session driver | database |
| Timezone | Asia/Kathmandu (set in .env) |
| DB | MySQL — `stock_management_system` |

### Authentication
- Two guards: `admin` (admins table) and `professional` (professionals table)
- Fully hand-rolled auth — no Breeze/Jetstream/Fortify/Sanctum
- Custom middleware: `AdminAuthenticate` and `ProfessionalAuthenticate`
- Route guard selected by prefix in `bootstrap/app.php`
- Password stored with `Hash::make`; OTP reset flow exists; no 2FA
- bcmath PHP extension: **confirmed loaded**

### Routing
- `routes/web.php` — public site and professional auth
- `routes/admin.php` — admin panel (`/admin/*`, guarded by `auth.admin`)
- `routes/professional.php` — professional portal (`/professional/*`)
- `routes/console.php` — no scheduled commands yet

### Folder Conventions
```
app/
  Console/             (artisan commands — currently empty)
  Exports/             (Maatwebsite Excel exports)
  Helpers/Helpers.php  (global functions — moneyFormat, formatCrore, etc.)
  Http/
    Controllers/Admin/     (24 controllers)
    Controllers/Professional/
    Controllers/Site/
    Middleware/            (7 middleware files)
    Requests/              (form requests exist)
  Models/              (27 models)
  Providers/           (AppServiceProvider — empty boot/register)
  Services/Admin/      (22 services — thin controller->service pattern)
  Services/Professional/
  Services/Site/
  Traits/ | Jobs/ | Mail/
```

### Frontend
- Bootstrap 5.3.3 + jQuery 3.7.1 + Select2 4.0.13 + SweetAlert2 11 + DataTables 2.x (all CDN)
- Bootstrap Icons 1.13.1 + nepalify 0.5.0 (Nepali input)
- Hyper admin theme CSS: `assets/css/app.min.css`, `icons.min.css`, `main.css`, `print.css`
- Vite configured but admin panel uses pre-built `assets/` directory directly
- TailwindCSS v4 in devDependencies but NOT used in any admin views

### Existing Domain
This project is an **exam-registration and professional licensing SaaS for NHPC** (Nepal Health Professional Council). The folder name `stock_management_system` does not reflect the domain. The system is likely in production use.

---

## 2. Existing Schema

### All 28 Migration Tables

| Table | Key Columns | Category |
|---|---|---|
| `sessions` | id, user_id, payload | Laravel |
| `cache`, `cache_locks` | key, value | Laravel |
| `jobs`, `job_batches`, `failed_jobs` | — | Laravel |
| `roles` | id, name UNIQUE | RBAC |
| `modules` | id, name | RBAC |
| `permissions` | id, role_id FK, module_id FK, name JSON | RBAC |
| `verification_steps` | id, role_id FK, step INT | Workflow |
| `admins` | id, role_id FK, name, phone, email, username UNIQUE, password, status | Auth |
| `colleges` | id, name | Reference |
| `universities` | id, name | Reference |
| `provinces` | id, name | Nepal geo |
| `districts` | id, name, province_id FK | Nepal geo |
| `municipalities` | id, name, district_id FK, type | Nepal geo |
| `levels` | id, name, order, status | Academic |
| `subject_committees` | id, name, status | Academic |
| `subject_committee_users` | id, subject_committee_id FK, user_id FK | Academic |
| `programs` | id, name, level_id FK, max_seats, fees | Academic |
| `professionals` | id, name, phone, email UNIQUE, password, otp, is_foreign, is_verify, status | End-users |
| `professional_infos` | id, professional_id FK, citizenship, DOB, NMC no., address | Profile |
| `professional_qualifications` | id, professional_id FK, level_id FK, college, university, marks, docs | Profile |
| `kycs` | id, professional_id FK, certificate data | Identity |
| `exams` | id, name, program_id FK, start/end dates, fee, status | Exam |
| `applicants` | id, professional_id FK, exam_id FK, status, payment info | Application |
| `applicant_logs` | id, applicant_id FK, action, user_id | Audit |
| `admit_cards` | id, applicant_id FK, roll_no, exam_center | Exam |
| `eligibility_marks` | id, program_id FK, level_id FK, marks | Academic |
| `certificates` | id, applicant_id FK, professional_id FK, cert_no, type, issue_date | Licensing |
| `certificate_renew_requests` | id, certificate_id FK, professional_id FK, status, fee | Licensing |

### Production Data Risk
The `.env` contains real NHPC mail credentials and **live Khalti payment keys**. The existing `RoleSeeder` and `ModuleSeeder` call `Schema::disableForeignKeyConstraints()` then `truncate()` — running them against production would **destroy NHPC role and permission data**. All new pharma seeders will use `firstOrCreate`.

---

## 3. Reuse Map

| Section | Decision | Reason |
|---|---|---|
| 6.1 Settings (key/value store) | Build new | No settings table exists |
| 6.1 Roles & Permissions | Extend (additive) | roles/modules/permissions exist; JSON permission model reusable; do not replace |
| 6.1 Audit log | Build new | applicant_logs is domain-specific; need generic audit_logs |
| 6.1 Masters (warehouses, categories, manufacturers, units, tax, accounts) | Build new | No equivalents |
| 6.2 Product master + packaging | Build new | No product tables |
| 6.3 Batch & expiry | Build new | No batch tables |
| 6.4 Suppliers & customers | Build new | No supplier/customer tables |
| 6.5 Purchasing | Build new | No purchase tables |
| 6.6 Sales | Build new | No sales tables |
| 6.7 Inventory operations | Build new | No inventory tables |
| 6.8 Payments | Build new | No AR/AP tables |
| 6.9 Dashboard & reports | Build new | Existing dashboard is a stub |
| 6.10-6.11 Stock management + locations | Build new | No stock/location code |
| Admin guard + AdminAuthenticate | Reuse as-is | Works correctly; pharma staff will use same guard |
| Controller -> Service pattern | Reuse as-is | Clean; replicate for all pharma modules |
| Blade layout + Bootstrap 5 + DataTables | Reuse as-is | Extend nav and views |
| Helpers.php moneyFormat | Extend | Add bcmath-based money helpers |
| NHPC domain tables (professionals, exams, etc.) | Preserve / do not touch | System in active production use |
| modules + permissions seeder | Extend (additive) | Add pharma modules without truncating |

---

## 4. Proposed Schema

### Design Principles
- Monetary amounts: DECIMAL(15,2). Unit prices/costs: DECIMAL(15,4). Never float.
- Stock quantities: BIGINT UNSIGNED (base units, integers).
- Document tables have deleted_at (soft delete). Ledgers (stock_movements, audit_logs) are append-only — no soft delete, no UPDATE/DELETE.
- Every FK column indexed. Composite indexes on all filter/sort columns.

### 4.1 System / Foundation (all new)

**settings** — id, key VARCHAR(100) UNIQUE, value TEXT, group VARCHAR(50), timestamps

**audit_logs** (append-only — no updated_at, no deleted_at):
id, user_id NULLABLE FK->admins, user_type VARCHAR(50), auditable_type VARCHAR(100), auditable_id BIGINT NULLABLE, event VARCHAR(50), old_values JSON, new_values JSON, ip_address VARCHAR(45), user_agent TEXT, created_at TIMESTAMP INDEX

**fiscal_years** — id, name VARCHAR(20), start_date DATE, end_date DATE, is_current BOOLEAN, is_closed BOOLEAN, timestamps

**accounting_periods** — id, fiscal_year_id FK, name, start_date DATE, end_date DATE, is_closed BOOLEAN, closed_by NULLABLE FK->admins, closed_at TIMESTAMP, timestamps

**document_sequences** — id, series VARCHAR(20), fiscal_year_id FK, last_number INT UNSIGNED DEFAULT 0, prefix, suffix, pad_length TINYINT DEFAULT 5, timestamps | UNIQUE(series, fiscal_year_id)

### 4.2 Masters (all new)

**warehouses** — id, code UNIQUE, name, address TEXT, is_active, timestamps+deleted_at

**warehouse_locations** — id, warehouse_id FK INDEX, parent_id NULLABLE FK->self, code (UNIQUE within warehouse), name, type ENUM(storage|receiving|dispatch|quarantine|damaged|expired_disposal|returns|cold_storage), depth TINYINT, is_sellable BOOLEAN, is_available BOOLEAN, storage_condition VARCHAR(50), capacity DECIMAL(10,2), is_active, timestamps+deleted_at

**categories** — id, parent_id NULLABLE FK->self, name, is_active, timestamps+deleted_at

**manufacturers** — id, code UNIQUE, name, country, contact, email, phone, is_active, timestamps+deleted_at

**units** — id, name, abbreviation, is_active, timestamps

**tax_categories** — id, name, rate DECIMAL(8,4) [UNVERIFIED: confirm with IRD], is_active, timestamps

**cash_bank_accounts** — id, code UNIQUE, name, type ENUM(cash|bank|other), opening_balance DECIMAL(15,2), is_active, timestamps+deleted_at

### 4.3 Products (all new)

**products** — id, code UNIQUE, name INDEX, generic_name INDEX, brand, manufacturer_id FK, category_id FK, dosage_form, strength, base_unit_id FK->units, tax_category_id FK, mrp DECIMAL(15,4), purchase_price DECIMAL(15,4), selling_price DECIMAL(15,4), wholesale_price DECIMAL(15,4), min_stock BIGINT, reorder_level BIGINT, max_level BIGINT NULLABLE, preferred_supplier_id FK->suppliers, storage_requirement, restricted_sale BOOLEAN, registration_reference, allow_fractional_base BOOLEAN, image_path, notes, is_active, timestamps+deleted_at

> **DECISION (2026-10-18) — Date storage:** All date fields store BOTH AD date (DATE column, primary storage used for all computation) AND BS date (DATE column, bs_ prefixed, computed and stored at write time). AD is authoritative. BS is denormalised for display and BS-based filtering. bs_ columns are nullable so legacy rows are not broken. This applies to: batch.mfg_date/expiry_date, invoice dates, adjustment dates, and any other business date. A `BsDateHelper` will handle conversion using the approved library.

**product_barcodes** — id, product_id FK INDEX, barcode VARCHAR(100) INDEX, type, timestamps

**product_packaging_levels** — id, product_id FK INDEX, unit_id FK->units, level_order TINYINT, conversion_factor INT UNSIGNED, base_unit_quantity BIGINT UNSIGNED, allow_fractional_sale BOOLEAN, timestamps | UNIQUE(product_id, level_order)

### 4.4 Suppliers & Customers (all new)

**suppliers** — id, code UNIQUE, name INDEX, contact_person, phone, email, billing_address, delivery_address, pan_vat, opening_balance DECIMAL(15,2), payment_terms_days TINYINT, notes, is_active, timestamps+deleted_at

**customers** — id, code UNIQUE, name INDEX, type ENUM(pharmacy|hospital|clinic|institution|individual), contact_person, phone, email, billing_address, delivery_address, pan_vat, licence_number, licence_expiry DATE, licence_block_on_expiry BOOLEAN, opening_balance DECIMAL(15,2), credit_limit DECIMAL(15,2), payment_terms_days TINYINT, notes, is_active, timestamps+deleted_at

### 4.5 Inventory Engine (all new)

**batches** — id, product_id FK INDEX, batch_number VARCHAR(100), manufacturer_id FK INDEX, goods_receipt_id NULLABLE FK INDEX, mfg_date DATE, expiry_date DATE INDEX, supplier_id FK, unit_cost DECIMAL(15,4), mrp DECIMAL(15,4), selling_price DECIMAL(15,4), status ENUM(active|quarantined|recalled|blocked|expired|disposed) DEFAULT active, notes, timestamps

> **DECISION (2026-10-18):** No unique constraint on (product_id, batch_number, manufacturer_id). The same manufacturer may re-supply the same batch number on a later receipt — each goods_receipt_id creates a distinct batch row. Batches are identified by their surrogate id; batch_number+manufacturer is informational only. Traceability is via goods_receipt_id.

**inventory_balances** (cache — reconciled against ledger):
id, product_id FK INDEX, batch_id FK INDEX, warehouse_id FK INDEX, location_id FK INDEX, on_hand BIGINT DEFAULT 0, reserved BIGINT DEFAULT 0, damaged BIGINT DEFAULT 0, quarantined BIGINT DEFAULT 0, in_transit_out BIGINT DEFAULT 0, updated_at TIMESTAMP | UNIQUE(batch_id, warehouse_id, location_id)

Available = on_hand - reserved - damaged - quarantined - in_transit_out (single scope/method, never computed inline)

**stock_movements** (append-only ledger — no updated_at, no deleted_at):
id, product_id FK INDEX, batch_id FK INDEX, warehouse_id FK INDEX, location_id FK INDEX, movement_type VARCHAR(50), reference_type VARCHAR(100) NULLABLE, reference_id BIGINT NULLABLE, quantity BIGINT (positive=in/negative=out), unit_cost DECIMAL(15,4), from_location_id BIGINT NULLABLE FK, to_location_id BIGINT NULLABLE FK, notes, created_by NULLABLE FK->admins, created_at TIMESTAMP INDEX

### 4.6 Purchasing (all new)

**purchase_orders** — po_number UNIQUE, fiscal_year_id FK, supplier_id FK INDEX, warehouse_id FK, status ENUM(draft|approved|partially_received|received|closed|cancelled), order_date DATE, expected_date DATE NULLABLE, subtotal/discount_amount/tax_amount/total_amount DECIMAL(15,2), approved_by FK, created_by FK, timestamps+deleted_at

**purchase_order_lines** — purchase_order_id FK INDEX, product_id FK, unit_id FK, ordered_qty BIGINT, ordered_base_qty BIGINT, unit_price DECIMAL(15,4), discount_pct DECIMAL(8,4), tax_category_id FK, line_total DECIMAL(15,2), received_base_qty BIGINT DEFAULT 0

**goods_receipts** — grn_number UNIQUE, fiscal_year_id FK, purchase_order_id NULLABLE FK, supplier_id FK INDEX, warehouse_id FK, supplier_invoice_number, supplier_invoice_date, receipt_date DATE, freight_amount DECIMAL(15,2), subtotal/discount_amount/tax_amount/total_amount DECIMAL(15,2), status ENUM(draft|posted|cancelled), posted_at TIMESTAMP, posted_by FK, attachment_path, created_by FK, timestamps+deleted_at

**goods_receipt_lines** — goods_receipt_id FK INDEX, purchase_order_line_id NULLABLE FK, product_id FK, batch_id FK, location_id FK, unit_id FK, received_qty BIGINT, received_base_qty BIGINT, rejected_qty BIGINT DEFAULT 0, unit_cost DECIMAL(15,4), discount_pct, tax_category_id FK, tax_amount DECIMAL(15,2), line_total DECIMAL(15,2), landed_cost_share DECIMAL(15,4), effective_unit_cost DECIMAL(15,4)

**purchase_returns** — return_number UNIQUE, goods_receipt_id FK, supplier_id FK, return_date DATE, status ENUM(draft|posted|cancelled), subtotal/tax_amount/total_amount DECIMAL(15,2), posted_by FK, timestamps+deleted_at

**purchase_return_lines** — purchase_return_id FK INDEX, goods_receipt_line_id FK, batch_id FK, location_id FK, returned_base_qty BIGINT, unit_cost DECIMAL(15,4), line_total DECIMAL(15,2)

### 4.7 Sales (all new)

**sale_invoices** — invoice_number UNIQUE, fiscal_year_id FK, customer_id NULLABLE FK INDEX, warehouse_id FK, invoice_date DATE INDEX, salesperson_id NULLABLE FK->admins, payment_method VARCHAR(50), subtotal/discount_amount/tax_amount/delivery_charge/round_off/total_amount/paid_amount/credit_amount/cogs_amount DECIMAL(15,2), status ENUM(draft|posted|cancelled), posted_at, posted_by FK, notes, created_by FK, timestamps+deleted_at

**sale_invoice_lines** — sale_invoice_id FK INDEX, product_id FK, batch_id FK, location_id FK; SNAPSHOT: product_name, product_code, batch_number, expiry_date, unit_name, tax_rate; unit_id FK, qty BIGINT, base_qty BIGINT, unit_price DECIMAL(15,4), discount_pct, line_discount/invoice_discount_share/taxable_amount/tax_amount/line_total DECIMAL(15,2), unit_cost DECIMAL(15,4), cogs_amount DECIMAL(15,2)

**sale_returns** — return_number UNIQUE, sale_invoice_id FK INDEX, customer_id NULLABLE FK, return_date DATE, reason TEXT, status ENUM(draft|posted|cancelled), total_amount DECIMAL(15,2), credit_note_issued BOOLEAN, posted_by FK, timestamps+deleted_at

**sale_return_lines** — sale_return_id FK INDEX, sale_invoice_line_id FK, product_id FK, batch_id FK, location_id FK, returned_base_qty BIGINT, condition ENUM(saleable|damaged|expired|quarantined), unit_price DECIMAL(15,4), line_total DECIMAL(15,2), restocked BOOLEAN DEFAULT 0

### 4.8 Payments (all new)

**customer_payments** — receipt_number UNIQUE, fiscal_year_id FK, customer_id FK INDEX, cash_bank_account_id FK, payment_date DATE INDEX, payment_method, reference_number, amount DECIMAL(15,2), status ENUM(posted|reversed), reversal_id NULLABLE FK->self, posted_by FK, timestamps

**customer_payment_allocations** — customer_payment_id FK INDEX, sale_invoice_id FK INDEX, allocated_amount DECIMAL(15,2)

**supplier_payments** — voucher_number UNIQUE, fiscal_year_id FK, supplier_id FK INDEX, cash_bank_account_id FK, payment_date DATE, payment_method, reference_number, amount DECIMAL(15,2), status ENUM(posted|reversed), reversal_id NULLABLE FK->self, posted_by FK, timestamps

**supplier_payment_allocations** — supplier_payment_id FK INDEX, goods_receipt_id FK INDEX, allocated_amount DECIMAL(15,2)

### 4.9 Inventory Operations (all new)

**stock_adjustments** — adjustment_number UNIQUE, fiscal_year_id FK, warehouse_id FK, adjustment_date DATE, reason_code VARCHAR(50), reason_notes TEXT, status ENUM(draft|pending_approval|approved|posted|cancelled), total_value DECIMAL(15,2), approved_by NULLABLE FK (must differ from created_by), approved_at, posted_by FK, posted_at, created_by FK, timestamps+deleted_at

**stock_adjustment_lines** — stock_adjustment_id FK INDEX, product_id FK, batch_id FK, location_id FK, system_qty BIGINT, physical_qty BIGINT, variance_qty BIGINT, unit_cost DECIMAL(15,4), variance_value DECIMAL(15,2)

**stock_transfers** — transfer_number UNIQUE, fiscal_year_id FK, from/to_warehouse_id FK, from/to_location_id FK, transfer_date DATE, status ENUM(draft|approved|dispatched|received|cancelled), dispatched_at TIMESTAMP, received_at TIMESTAMP, approved_by FK, created_by FK, timestamps+deleted_at

**stock_transfer_lines** — stock_transfer_id FK INDEX, batch_id FK, product_id FK, dispatched_base_qty BIGINT, received_base_qty BIGINT DEFAULT 0, unit_cost DECIMAL(15,4)

**stock_relocations** — relocation_number UNIQUE, warehouse_id FK, from/to_location_id FK, batch_id FK, product_id FK, base_qty BIGINT, reason TEXT, posted_at TIMESTAMP, posted_by FK, timestamps

**recall_events** (P2 table designed now) — product_id NULLABLE FK, batch_id NULLABLE FK, manufacturer_id NULLABLE FK, reason TEXT, scope ENUM(batch|product|manufacturer), status ENUM(active|released), initiated_by FK, released_by NULLABLE FK, released_at TIMESTAMP, timestamps

### 4.10 Altered Existing Tables

| Table | Change | Risk |
|---|---|---|
| `roles` | Additive INSERT only (no ALTER) | None |
| `modules` | Additive INSERT only (no ALTER) | None |
| `permissions` | Additive INSERT only (no ALTER) | None |
| `admins` | ADD nullable JSON column `warehouse_ids` | Low — nullable, existing rows get NULL |

**All other existing tables are untouched.**

---

## 5. Migration Safety Plan

| Migration | Risk | Backup? | Rollback |
|---|---|---|---|
| admins + nullable warehouse_ids column | Low | Yes, snapshot admins table | ALTER TABLE admins DROP COLUMN warehouse_ids |
| All new Schema::create tables | Zero | No — new only | Schema::dropIfExists(...) |
| New seeder rows in modules/roles | Low | Yes, snapshot both tables | Delete new rows by name |
| OLD truncate-based seeders | HIGH — DO NOT RUN | N/A | Never run against production |

**Pre-migration protocol:**
1. `mysqldump stock_management_system > backup_YYYYMMDD.sql` before every phase
2. All migrations have proper down() methods
3. Pharma seeders use firstOrCreate / updateOrCreate — never truncate
4. Never re-run RoleSeeder or ModuleSeeder against production database

---

## 6. Phase Plan (Adjusted)

| Phase | Scope |
|---|---|
| 0 | Analysis (this document) |
| 1 | Foundation: settings, additive roles/permissions, audit log, masters (warehouses, locations, categories, manufacturers, units, tax categories, accounts), fiscal years, document sequences |
| 2 | Inventory engine: products+packaging, batches, inventory balances, stock movements ledger, InventoryService, FEFO, opening stock, reconcile command |
| 3 | Purchasing: PO, GRN, purchase invoice, supplier ledger, purchase return |
| 4 | Sales: invoice screen, FEFO allocation, numbering, PDF, customer ledger, credit limits, sales return |
| 5 | Payments: customer receipts, supplier payments, allocation, cash/bank, reversals |
| 6 | Operations: transfers, adjustments/counts, recall/quarantine, period lock, maker-checker, alert scheduler |
| 7 | Dashboard, reports, exports, imports |
| 8 | Hardening: security, query optimization, deployment docs |

---

## 7. Risks and Open Questions

### Ranked Risks

| # | Risk | Severity | Mitigation |
|---|---|---|---|
| 1 | NHPC production data in DB | High | All new tables additive; never truncate existing |
| 2 | Old seeders use truncate() — destroys data if re-run | High | New seeders use firstOrCreate; documented |
| 3 | Khalti live keys in .env | Medium | Rotate keys — out of this project's scope |
| 4 | Batch uniqueness constraint edge cases | Low-Med | Decision needed (Q1) |
| 5 | Pharma VAT rules (Nepal IRD) | Medium | All rates configurable, seeded as UNVERIFIED |
| 6 | BS date display — no library installed | Low | Store AD in DB; add BS display library per Q5 |
| 7 | XAMPP — no background queue worker | Low | Tests use sync; document for production |
| 8 | password_reference stores plaintext | Low | Existing behavior; not changing without instruction |

### Decisions Recorded (2026-10-18)

| Q | Decision | Impact |
|---|---|---|
| Q1 — Batch uniqueness | **No unique constraint** on (product_id, batch_number, manufacturer_id). Same manufacturer can re-supply same batch number on a later receipt — each GRN creates a distinct batch row identified by surrogate id. | `batches` table has no unique index on (product_id, batch_number, manufacturer_id); goods_receipt_id FK added |
| Q2 — GL scope | **Subledger only** (AR, AP, cash/bank, stock valuation). No double-entry GL in v1. GL is P3. | Payment and receipt tables are subledger, not journal entries |
| Q3 — Tax rates | **User-configurable in Settings.** No rates seeded. Tax category table has name + rate columns — admin fills them in. No compliance claims made. | tax_categories seeded empty |
| Q4 — Warehouse permissions | **Pivot table** (`admin_warehouse`) — not a JSON column on admins. Better for queries, exports, and policy checks. | New migration for admin_warehouse pivot |
| Q5 — Date storage | **Store both AD and BS dates** in the database. Every business date column has a pair: `date_ad DATE` (authoritative, used for all arithmetic) and `date_bs DATE` (denormalised, computed at write time for display and BS filtering). A `BsDateHelper` handles conversion. | All date columns doubled; bs_ columns nullable for compatibility |

---

## 8. Package Recommendations

| Category | Installed? | Decision | Notes |
|---|---|---|---|
| bcmath PHP extension | YES | Use as-is | Confirmed loaded |
| Spreadsheet import/export | YES (maatwebsite/excel ^3.1) | Use as-is | Chunked import supported |
| PDF generation | NO | **Install `barryvdh/laravel-dompdf`** | Pre-approved category in spec |
| Role/permission package | NO (custom RBAC exists) | **Do NOT add Spatie** | Existing system wired into 24 controllers+views; extend only |
| BS/AD date conversion | NO | **Install `anuzpandey/laravel-nepali-date`** | Approved via Q5: both AD+BS stored in DB |

---

*End of Phase 0 Analysis. All questions resolved. Still awaiting `APPROVED PHASE 0` to begin implementation.*
