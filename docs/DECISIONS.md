# Design Decisions & Assumptions Log

**Project:** Pharmaceutical Wholesale/Distribution Stock Management System (Nepal)  
**Established:** 2026-10-18  

This document logs all key design decisions, rationale, alternatives considered, and assumptions.

---

### D-001: Preservation of Existing NHPC Application
- **Date:** 2026-10-18
- **Decision:** Keep all existing 28 database tables and NHPC SaaS logic intact. All stock management schema additions, controllers, models, and routes will be strictly additive.
- **Reason:** The database contains active production data, mail configurations, and live payment gateway keys.
- **Alternative Considered:** Forking or removing NHPC code. Rejected because the requirement strictly mandated extending the existing codebase without breaking existing functionality.

---

### D-002: Batch Uniqueness & Identification (User Decision Q1)
- **Date:** 2026-10-18
- **Decision:** No composite unique constraint on `(product_id, batch_number, manufacturer_id)`. Each receipt/GRN creates or references a distinct batch identified by a surrogate primary key `id`, with an optional `goods_receipt_id` link.
- **Reason:** The same manufacturer can supply the same batch number on subsequent shipments or receipts, which must be tracked distinctly for provenance, landed costing, and receipt reconciliation.
- **Alternative Considered:** `UNIQUE(product_id, batch_number, manufacturer_id)`. Rejected per user requirement.

---

### D-003: Financial Scope — Subledger Only (User Decision Q2)
- **Date:** 2026-10-18
- **Decision:** System v1 is limited to operational subledgers: Accounts Receivable (AR), Accounts Payable (AP), Cash/Bank books, and Stock Valuation. Full double-entry General Ledger (GL) is deferred to P3 / future phase.
- **Reason:** Clarified and confirmed with user to prioritize inventory accuracy and operational billing before accounting journal complexity.
- **Alternative Considered:** Full double-entry GL (debit/credit journal entries) in v1. Rejected as out of scope for v1.

---

### D-004: Tax & VAT Configuration (User Decision Q3)
- **Date:** 2026-10-18
- **Decision:** Tax rates will NOT be hardcoded or assumed. A configurable `tax_categories` master will be seeded empty/placeholder for user setup in Settings.
- **Reason:** Pharmaceutical VAT and exemption rules in Nepal (IRD/DDA regulations) require dealer-specific configuration and legal compliance verification by the operator.
- **Alternative Considered:** Hardcoding 0% / 13% VAT. Rejected to prevent false compliance claims.

---

### D-005: Warehouse Scoping for Admins (User Decision Q4)
- **Date:** 2026-10-18
- **Decision:** Implement an `admin_warehouse` pivot table (`admin_id`, `warehouse_id`) instead of a JSON column on the `admins` table.
- **Reason:** Better relational integrity, easier indexation, direct foreign key constraints, and cleaner query filtering.
- **Alternative Considered:** Nullable JSON column `warehouse_ids` on `admins` table. Rejected per user preference for pivot table.

---

### D-006: Dual Date Storage (AD & BS) (User Decision Q5)
- **Date:** 2026-10-18
- **Decision:** Store both Gregorian (AD) and Bikram Sambat (BS) dates in the database for all business date columns (e.g., `date_ad DATE`, `date_bs VARCHAR(10) / DATE`). `date_ad` is the authoritative source for system date arithmetic and timestamps; `date_bs` is persisted at write time for reporting, UI display, and native BS date queries. Conversion handled via `anuzpandey/laravel-nepali-date` package or helper.
- **Reason:** Nepal pharma operations legally require BS dates on invoices, receipts, and returns, while standard SQL date math requires AD dates.
- **Alternative Considered:** Dynamic runtime conversion only. Rejected per user decision to persist both in database.

---

### D-007: Seeder Safety Protocol
- **Date:** 2026-10-18
- **Decision:** Never execute existing `RoleSeeder` or `ModuleSeeder` (which execute `TRUNCATE`). All new stock management seeders must exclusively use `firstOrCreate` or `updateOrCreate`.
- **Reason:** Prevent accidental wipe of NHPC roles, modules, and permissions.
- **Alternative Considered:** Modifying existing seeders. Rejected to preserve existing migration/seeding artifacts as historical record.

---

### D-008: Authoritative 6-Channel Stock Flow Architecture
- **Date:** 2026-10-24
- **Decision:** The inventory engine and stock movements ledger strictly conform to a 6-channel closed-loop flow model:
  - **Inflows (+):** `purchase` (`PURCHASE_RECEIPT`), `Sell return` (`SALE_RETURN`), and opening stock.
  - **Central Repository:** `Stock` (Inventory Balance calculated as physical on-hand and FEFO available).
  - **Outflows (-):** `sell` (`SALE_DISPATCH`), `Purchase return` (`PURCHASE_RETURN`), `Expired item` (`EXPIRED_DISPOSAL`), and `Damage` (`DAMAGE_WRITE_OFF`).
- **Reason:** Standardizes stock ledger invariants across purchasing, sales, and warehouse operations, providing a single source of truth for stock reconciliations and audit compliance.
- **Alternative Considered:** Ad-hoc adjustment types or treating returns as positive write-offs. Rejected to prevent sign confusion and ensure clean directional flow.

---

### D-009: Full CRUD Implementation Across All Operational Sections
- **Date:** 2026-10-26
- **Decision:** Provide complete, full-featured CRUD (Create, Read, Update, Delete/Post/Approve) workflows across every operational area:
  1. **Catalog & Masters:** Products (`create`, `store`, `edit`, `update`, `delete`), Suppliers, Customers, Warehouses, and Locations.
  2. **Purchasing:** Purchase Orders, Goods Receipts (GRN), and Purchase Returns.
  3. **Sales & Distribution:** Sales Invoices (FEFO allocation, draft-to-post, PDF generation) and Sales Returns (credit note issuance, stock restock).
  4. **Warehouse Operations:** Inter-Warehouse Transfers (create, dispatch, receive), Physical Stock Adjustments (maker-checker separation of duties), and Batch Recalls.
  5. **Payments & Treasury:** Customer Receipts (AR collection, posting, ledger credit) and Supplier Payments (AP disbursement, posting, ledger debit).
- **Reason:** Satisfies the end-to-end operational workflow requirements with verified data consistency, subledger accuracy, and compliance safeguards.

