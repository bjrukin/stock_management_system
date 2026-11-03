# Production Operations & Deployment Runbook
**Pharmaceutical Wholesale & Distribution Stock Management System (Nepal)**

---

## 1. Executive Architecture Overview

This application extends the existing Laravel codebase into an enterprise-grade **Pharmaceutical Wholesale & Distribution Stock Management System** configured for wholesale dealers and distributors operating under the regulatory oversight of the **Department of Drug Administration (DDA)** and the **Inland Revenue Department (IRD)** of Nepal.

### Technical Stack
- **Framework:** Laravel 11.x on PHP 8.2+
- **Database Engine:** MySQL 8.0+ / MariaDB 10.4+ (InnoDB engine with strict foreign key constraints)
- **Accounting & Math Engine:** Arbitrary precision math via PHP `bcmath` extension (zero floating-point rounding errors)
- **Calendar & Localization:** `anuzpandey/laravel-nepali-date` for authoritative Bikram Sambat (BS) conversion
- **Document Generation:** `barryvdh/laravel-dompdf` for DDA/IRD-compliant tax invoices
- **Architecture Pattern:** Dual-system additive architecture coexisting safely with the existing NHPC SaaS licensing platform (28 preexisting tables preserved untouched).

---

## 2. Core Domain Invariants & Rules

### 2.1 Authoritative Stock Formula
Stock levels are computed with mathematical precision directly from the immutable stock movements ledger:
$$\text{Available Stock} = \text{On-Hand} - \text{Reserved} - \text{Damaged} - \text{Quarantined} - \text{In-Transit Out}$$

- **On-Hand:** Physical count present in the warehouse.
- **Available:** Uncommitted stock eligible for sales allocation.
- **Reserved:** Stock committed to approved sales orders awaiting dispatch.
- **Damaged / Quarantined:** Isolated in non-sellable locations, strictly excluded from FEFO allocation.

### 2.2 First-Expiry-First-Out (FEFO) Engine
All sales order dispatches automatically slice order quantities across batches in ascending order of `expiry_date_ad`.
- Batches with `is_quarantined = true`, `is_recalled = true`, or `expiry_date_ad <= today` are **strictly blocked** by the allocation query.
- Multi-tier packaging conversions (e.g. Box $\rightarrow$ Strip $\rightarrow$ Tablet) convert automatically using defined conversion factors.

### 2.3 Strict Append-Only Ledgers
To comply with financial audits and regulatory traceability, the following ledgers are strictly append-only:
1. `stock_movements` (Inventory ledger)
2. `customer_ledger_entries` (Accounts Receivable subledger)
3. `supplier_ledger_entries` (Accounts Payable subledger)
4. `cash_bank_transactions` (Cash & Bank book ledger)
5. `audit_logs` (System audit trail)

> [!CAUTION]
> Eloquent models for these tables will throw a `RuntimeException` if an `update()` or `delete()` operation is attempted on an existing row. Corrections must always be made via compensating reversal transactions.

### 2.4 Dual AD / BS Date Synchronicity
- `date_ad` (Gregorian / AD) is the authoritative, indexable primary timestamp.
- `date_bs` (Bikram Sambat / BS) is automatically denormalized and stored in `YYYY-MM-DD` format via `BsDateHelper::toBs()` to satisfy Nepal statutory reporting.

### 2.5 Maker-Checker Authorization Rule
Physical stock count adjustments (`stock_adjustments`) enforce separation of duties:
- The user who created the adjustment draft (**Maker**) is strictly prohibited from approving it.
- Approval requires a distinct user (**Checker**), which then atomically records `PHYSICAL_COUNT_SURPLUS` or `PHYSICAL_COUNT_SHORTAGE` movements.

---

## 3. Database Deployment & Zero-Truncate Seeding

### 3.1 Initial Deployment & Migrations
Run the standard Laravel migration command:
```bash
php artisan migrate
```
*Note: All 43 pharmaceutical migrations are strictly additive and do not alter or drop any legacy NHPC SaaS tables.*

> [!WARNING]
> **NEVER run `php artisan migrate:fresh` in production!**  
> Running `migrate:fresh` will drop preexisting production tables including NHPC SaaS client data. Use standard `php artisan migrate` only.

### 3.2 Seeding Pharmaceutical Domain Data
To seed pharmaceutical master records (tax categories, packaging units, warehouses, manufacturers, products, suppliers, customers, and accounts), execute the dedicated composite seeder:
```bash
php artisan db:seed --class=PharmaDatabaseSeeder
```
This seeder adheres strictly to the **Zero-Truncate Protocol** using `firstOrCreate` / `updateOrCreate` and will never truncate or overwrite existing records.

---

## 4. Operational Checklists & Procedures

### 4.1 Daily Operations
1. **Queue Worker:** Ensure background workers are running for PDF rendering and notifications:
   ```bash
   php artisan queue:work --stop-when-empty
   ```
2. **Nightly Stock Ledger Reconciliation:**
   The scheduler is configured in `routes/console.php` to run nightly at 02:00 AM:
   ```bash
   php artisan stock:reconcile
   ```
   If discrepancies between cached `inventory_balances` and `stock_movements` are detected, run with `--fix` to re-synchronize:
   ```bash
   php artisan stock:reconcile --fix
   ```

### 4.2 Monthly Closing & Accounting Period Lock
1. Verify all Goods Receipts (GRN) and Sales Invoices for the month are posted.
2. Generate the **Nepal VAT Sales Register** and **Purchase Register**:
   ```php
   $salesReg = ReportingService::getSalesRegister('2026-10-01', '2026-10-31');
   $purchaseReg = ReportingService::getPurchaseRegister('2026-10-01', '2026-10-31');
   ```
3. Lock the accounting period to prevent back-dated entries:
   ```php
   $period = AccountingPeriod::where('name', 'Ashoj 2083')->first();
   $period->update([
       'is_closed' => true,
       'closed_by' => $adminId,
       'closed_at' => now(),
   ]);
   ```
   Any transaction attempted with a date inside a closed period will be immediately rejected with an exception.

### 4.3 Drug Recall & Batch Quarantine Procedure
If the DDA or manufacturer issues a drug recall order:
```php
WarehouseOperationService::quarantineBatch(
    batchId: $batchId,
    recallRef: 'DDA-RECALL-2083-05',
    issuedBy: 'Department of Drug Administration',
    reason: 'Active ingredient potency degradation',
    adminId: $adminId
);
```
**Automated Effect:**
- Sets `is_quarantined = true` and `is_recalled = true` on the batch.
- Automatically relocates all physical stock across all warehouses from ambient storage to `LOC-QUARANTINE`.
- Instantly drops available sellable stock to zero in all FEFO allocation queries.

### 4.4 Cheque Bounce & Receipt Reversal Procedure
If a customer cheque bounces or a receipt must be voided:
```php
PaymentService::reverseCustomerReceipt(
    receipt: $customerReceipt,
    adminId: $adminId,
    reason: 'Cheque #998811 dishonored by bank'
);
```
**Automated Effect:**
- Reverts sales invoice allocations (restores unpaid balance).
- Restores customer accounts receivable liability via debit reversal entry.
- Records bank withdrawal entry in `cash_bank_transactions`.
- Updates receipt status to `reversed`.

---

## 5. Backup & Disaster Recovery Protocol

### 5.1 Automated MySQL Dump
Schedule daily hot backups using `mysqldump` with transaction consistency:
```bash
mysqldump -u root -p \
  --single-transaction \
  --quick \
  --routines \
  --triggers \
  stock_management_system > /backups/pharma_db_$(date +\%Y\%m\%d_\%H\%M\%S).sql
```

### 5.2 Backup Integrity Check
To verify backup restorability without impacting production, import into a test staging instance and run the test suite:
```bash
mysql -u root -p test_restore_db < /backups/pharma_db_latest.sql
php artisan stock:reconcile
php artisan test
```

---

## 6. Regulatory Compliance Verification
- **DDA Wholesale Compliance:** Every goods receipt, invoice, and customer record captures registered DDA wholesale and retail drug license numbers.
- **Nepal IRD Tax Invoice:** Generated invoices comply with IRD Schedule 5 requirements (separate display of taxable, exempt, and 13% VAT components).
- **Audit Log Verification:** All administrative actions (approvals, postings, adjustments, reversals) are automatically recorded in `audit_logs` with before/after state diffs.
