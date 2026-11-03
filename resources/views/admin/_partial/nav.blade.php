@php
    $admin = auth()->guard('admin')->user();
@endphp
<div class="left-side-menu mt-3">
    <div class="h-100" data-simplebar>
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul id="side-menu">
                <!-- 1. DASHBOARD -->
                <li class="@if(isset($nav) && $nav == 'dashboard') menuitem-active @endif">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="mdi mdi-view-dashboard-outline text-primary"></i> <span>Dashboard</span>
                    </a>
                </li>

                <!-- 2. CATALOG & INVENTORY MASTERS -->
                <li class="@if(isset($nav) && $nav == 'pharma_catalog') menuitem-active @endif">
                    <a href="#pharmaCatalog" data-bs-toggle="collapse">
                        <i class="bi bi-capsule text-info"></i>
                        <span>Catalog & Masters</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse @if(isset($nav) && $nav == 'pharma_catalog') show @endif" id="pharmaCatalog">
                        <ul class="nav-second-level">
                            <li class="@if(isset($sub_nav) && $sub_nav == 'products') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.products') }}" class="@if(isset($sub_nav) && $sub_nav == 'products') active @endif">
                                    <i class="bi bi-box-seam me-1"></i> Products & Items
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'batches') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.batches') }}" class="@if(isset($sub_nav) && $sub_nav == 'batches') active @endif">
                                    <i class="bi bi-upc-scan me-1"></i> Batch Register
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'suppliers') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.suppliers') }}" class="@if(isset($sub_nav) && $sub_nav == 'suppliers') active @endif">
                                    <i class="bi bi-truck me-1"></i> Suppliers
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'customers') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.customers') }}" class="@if(isset($sub_nav) && $sub_nav == 'customers') active @endif">
                                    <i class="bi bi-shop me-1"></i> Customers / Pharmacies
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'warehouses') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.warehouses') }}" class="@if(isset($sub_nav) && $sub_nav == 'warehouses') active @endif">
                                    <i class="bi bi-buildings me-1"></i> Warehouses & Locations
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- 3. PURCHASING & GRN -->
                <li class="@if(isset($nav) && $nav == 'pharma_purchasing') menuitem-active @endif">
                    <a href="#pharmaPurchasing" data-bs-toggle="collapse">
                        <i class="bi bi-cart-check text-success"></i>
                        <span>Purchasing</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse @if(isset($nav) && $nav == 'pharma_purchasing') show @endif" id="pharmaPurchasing">
                        <ul class="nav-second-level">
                            <li class="@if(isset($sub_nav) && $sub_nav == 'purchase_orders') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.purchase-orders') }}" class="@if(isset($sub_nav) && $sub_nav == 'purchase_orders') active @endif">
                                    <i class="bi bi-file-earmark-plus me-1"></i> Purchase Orders (PO)
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'goods_receipts') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.goods-receipts') }}" class="@if(isset($sub_nav) && $sub_nav == 'goods_receipts') active @endif">
                                    <i class="bi bi-box-arrow-in-down me-1"></i> Goods Receipts (GRN)
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'purchase_returns') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.purchase-returns') }}" class="@if(isset($sub_nav) && $sub_nav == 'purchase_returns') active @endif">
                                    <i class="bi bi-arrow-return-left me-1"></i> Purchase Returns
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- 4. SALES & DISTRIBUTION -->
                <li class="@if(isset($nav) && $nav == 'pharma_sales') menuitem-active @endif">
                    <a href="#pharmaSales" data-bs-toggle="collapse">
                        <i class="bi bi-receipt text-warning"></i>
                        <span>Sales & Invoicing</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse @if(isset($nav) && $nav == 'pharma_sales') show @endif" id="pharmaSales">
                        <ul class="nav-second-level">
                            <li class="@if(isset($sub_nav) && $sub_nav == 'sales_invoices') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.sales-invoices') }}" class="@if(isset($sub_nav) && $sub_nav == 'sales_invoices') active @endif">
                                    <i class="bi bi-receipt-cutoff me-1"></i> Sales Invoices (VAT)
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'sales_returns') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.sales-returns') }}" class="@if(isset($sub_nav) && $sub_nav == 'sales_returns') active @endif">
                                    <i class="bi bi-arrow-return-right me-1"></i> Sales Returns (Credit)
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- 5. WAREHOUSE OPERATIONS & STOCK LEDGER -->
                <li class="@if(isset($nav) && $nav == 'pharma_warehouse') menuitem-active @endif">
                    <a href="#pharmaWarehouse" data-bs-toggle="collapse">
                        <i class="bi bi-boxes text-danger"></i>
                        <span>Warehouse & Stock</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse @if(isset($nav) && $nav == 'pharma_warehouse') show @endif" id="pharmaWarehouse">
                        <ul class="nav-second-level">
                            <li class="@if(isset($sub_nav) && $sub_nav == 'movements') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.stock-movements') }}" class="@if(isset($sub_nav) && $sub_nav == 'movements') active @endif">
                                    <i class="bi bi-journal-text me-1"></i> Stock Ledger
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'transfers') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.stock-transfers') }}" class="@if(isset($sub_nav) && $sub_nav == 'transfers') active @endif">
                                    <i class="bi bi-arrow-left-right me-1"></i> Inter-Warehouse Transfers
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'adjustments') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.stock-adjustments') }}" class="@if(isset($sub_nav) && $sub_nav == 'adjustments') active @endif">
                                    <i class="bi bi-sliders me-1"></i> Stock Adjustments
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'recalls') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.recalls') }}" class="@if(isset($sub_nav) && $sub_nav == 'recalls') active @endif">
                                    <i class="bi bi-shield-slash me-1"></i> DDA Batch Recalls
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- 6. PAYMENTS & TREASURY -->
                <li class="@if(isset($nav) && $nav == 'pharma_payments') menuitem-active @endif">
                    <a href="#pharmaPayments" data-bs-toggle="collapse">
                        <i class="bi bi-cash-stack text-success"></i>
                        <span>Payments & Accounts</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse @if(isset($nav) && $nav == 'pharma_payments') show @endif" id="pharmaPayments">
                        <ul class="nav-second-level">
                            <li class="@if(isset($sub_nav) && $sub_nav == 'customer_receipts') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.customer-receipts') }}" class="@if(isset($sub_nav) && $sub_nav == 'customer_receipts') active @endif">
                                    <i class="bi bi-cash-coin me-1"></i> Customer Receipts (AR)
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'supplier_payments') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.supplier-payments') }}" class="@if(isset($sub_nav) && $sub_nav == 'supplier_payments') active @endif">
                                    <i class="bi bi-wallet2 me-1"></i> Supplier Payments (AP)
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'cash_bank') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.cash-bank') }}" class="@if(isset($sub_nav) && $sub_nav == 'cash_bank') active @endif">
                                    <i class="bi bi-bank me-1"></i> Cash & Bank Accounts
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- 7. REPORTS & TAX REGISTERS -->
                <li class="@if(isset($nav) && $nav == 'pharma_reports') menuitem-active @endif">
                    <a href="#pharmaReports" data-bs-toggle="collapse">
                        <i class="bi bi-bar-chart-line text-primary"></i>
                        <span>Reports & Tax Registers</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse @if(isset($nav) && $nav == 'pharma_reports') show @endif" id="pharmaReports">
                        <ul class="nav-second-level">
                            <li class="@if(isset($sub_nav) && $sub_nav == 'stock_valuation') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.reports.stock-valuation') }}" class="@if(isset($sub_nav) && $sub_nav == 'stock_valuation') active @endif">
                                    <i class="bi bi-pie-chart me-1"></i> Stock Valuation
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'near_expiry') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.reports.near-expiry') }}" class="@if(isset($sub_nav) && $sub_nav == 'near_expiry') active @endif">
                                    <i class="bi bi-clock-history me-1"></i> Near-Expiry Alerts
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'sales_register') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.reports.sales-register') }}" class="@if(isset($sub_nav) && $sub_nav == 'sales_register') active @endif">
                                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Sales Register (VAT)
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'purchase_register') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.reports.purchase-register') }}" class="@if(isset($sub_nav) && $sub_nav == 'purchase_register') active @endif">
                                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Purchase Register (VAT)
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'aging') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.reports.aging') }}" class="@if(isset($sub_nav) && $sub_nav == 'aging') active @endif">
                                    <i class="bi bi-hourglass-split me-1"></i> AR / AP Aging
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- 8. SETTINGS & RBAC -->
                <li class="@if(isset($nav) && in_array($nav, ['pharma_settings', 'rolepermission', 'user'])) menuitem-active @endif">
                    <a href="#systemSettings" data-bs-toggle="collapse">
                        <i class="bi bi-person-fill-gear text-secondary"></i>
                        <span>Settings & Users</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse @if(isset($nav) && in_array($nav, ['pharma_settings', 'rolepermission', 'user'])) show @endif" id="systemSettings">
                        <ul class="nav-second-level">
                            <li class="@if(isset($sub_nav) && $sub_nav == 'user') menuitem-active @endif">
                                <a href="{{ route('admin.user') }}" class="@if(isset($sub_nav) && $sub_nav == 'user') active @endif">
                                    <i class="bi bi-people me-1"></i> System Users
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'role') menuitem-active @endif">
                                <a href="{{ route('admin.role') }}" class="@if(isset($sub_nav) && $sub_nav == 'role') active @endif">
                                    <i class="bi bi-shield-lock me-1"></i> Roles
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'permission') menuitem-active @endif">
                                <a href="{{ route('admin.permission') }}" class="@if(isset($sub_nav) && $sub_nav == 'permission') active @endif">
                                    <i class="bi bi-key me-1"></i> Permissions
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'settings') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.settings') }}" class="@if(isset($sub_nav) && $sub_nav == 'settings') active @endif">
                                    <i class="bi bi-gear me-1"></i> System Settings
                                </a>
                            </li>
                            <li class="@if(isset($sub_nav) && $sub_nav == 'audit_logs') menuitem-active @endif">
                                <a href="{{ route('admin.pharma.audit-logs') }}" class="@if(isset($sub_nav) && $sub_nav == 'audit_logs') active @endif">
                                    <i class="bi bi-shield-check me-1"></i> Audit Trail
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- 9. LEGACY NHPC SAAS (COLLAPSIBLE / ARCHIVED) -->
                <!-- @if($admin->role_id == 1 || $admin->hasPermission('Applicant', 'list') || $admin->hasPermission('Exam', 'list'))
                <li class="@if(isset($nav) && in_array($nav, ['applicant', 'exam', 'kyc', 'collegeuniversity', 'academics', 'address'])) menuitem-active @endif">
                    <a href="#legacySaaS" data-bs-toggle="collapse" class="text-muted">
                        <i class="bi bi-archive text-muted"></i>
                        <span>NHPC Licensing (Archived)</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse @if(isset($nav) && in_array($nav, ['applicant', 'exam', 'kyc', 'collegeuniversity', 'academics', 'address'])) show @endif" id="legacySaaS">
                        <ul class="nav-second-level">
                            @if($admin->hasPermission('Applicant', 'list'))
                            <li><a href="{{ route('admin.applicant') }}"><i class="bi bi-card-text me-1"></i> Applicants</a></li>
                            @endif
                            @if($admin->hasPermission('Exam', 'list'))
                            <li><a href="{{ route('admin.exam') }}"><i class="bi bi-patch-check me-1"></i> Exams</a></li>
                            @endif
                            @if($admin->hasPermission('Kyc', 'list'))
                            <li><a href="{{ route('admin.kyc') }}"><i class="bi bi-person-check-fill me-1"></i> KYC</a></li>
                            @endif
                            @if($admin->hasPermission('College', 'list'))
                            <li><a href="{{ route('admin.college') }}"><i class="bi bi-book me-1"></i> College</a></li>
                            @endif
                            @if($admin->hasPermission('University', 'list'))
                            <li><a href="{{ route('admin.university') }}"><i class="bi bi-bank me-1"></i> University</a></li>
                            @endif
                            @if($admin->hasPermission('Level', 'list'))
                            <li><a href="{{ route('admin.level') }}"><i class="bi bi-mortarboard me-1"></i> Academics Level</a></li>
                            @endif
                            @if($admin->hasPermission('Program', 'list'))
                            <li><a href="{{ route('admin.program') }}"><i class="bi bi-journal-bookmark me-1"></i> Program</a></li>
                            @endif
                            @if($admin->hasPermission('Province', 'list'))
                            <li><a href="{{ route('admin.province') }}"><i class="bi bi-geo me-1"></i> Province & Address</a></li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif -->
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>