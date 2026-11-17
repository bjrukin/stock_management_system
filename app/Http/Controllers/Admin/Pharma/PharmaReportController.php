<?php

namespace App\Http\Controllers\Admin\Pharma;

use App\Http\Controllers\Controller;
use App\Support\PharmaAccess;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Services\ReportingService;
use Illuminate\Http\Request;

class PharmaReportController extends Controller
{
    public function stockValuation(Request $request)
    {
        $data['nav'] = 'pharma_reports';
        $data['sub_nav'] = 'stock_valuation';
        $data['page_title'] = 'Stock Valuation Report';

        $warehouseId = $request->filled('warehouse_id') ? (int) $request->warehouse_id : null;
        $data['report'] = ReportingService::getStockValuationReport($warehouseId, PharmaAccess::warehouseIds());

        return view('admin.pharma.reports.stock_valuation', $data);
    }

    public function nearExpiry(Request $request)
    {
        $data['nav'] = 'pharma_reports';
        $data['sub_nav'] = 'near_expiry';
        $data['page_title'] = 'Near-Expiry Batches Report (< 90 Days)';

        $days = $request->filled('days') ? (int) $request->days : 90;
        $data['batches'] = ReportingService::getNearExpiryBatches($days, warehouseIds: PharmaAccess::warehouseIds());
        $data['days'] = $days;

        return view('admin.pharma.reports.near_expiry', $data);
    }

    public function salesRegister(Request $request)
    {
        $data['nav'] = 'pharma_reports';
        $data['sub_nav'] = 'sales_register';
        $data['page_title'] = 'Sales Register (Nepal VAT Format)';

        $from = $request->get('from', date('Y-m-01'));
        $to = $request->get('to', date('Y-m-d'));

        $data['register'] = ReportingService::getSalesRegister($from, $to, warehouseIds: PharmaAccess::warehouseIds());
        $data['from'] = $from;
        $data['to'] = $to;

        return view('admin.pharma.reports.sales_register', $data);
    }

    public function purchaseRegister(Request $request)
    {
        $data['nav'] = 'pharma_reports';
        $data['sub_nav'] = 'purchase_register';
        $data['page_title'] = 'Purchase Register (Nepal VAT Format)';

        $from = $request->get('from', date('Y-m-01'));
        $to = $request->get('to', date('Y-m-d'));

        $data['register'] = ReportingService::getPurchaseRegister($from, $to, warehouseIds: PharmaAccess::warehouseIds());
        $data['from'] = $from;
        $data['to'] = $to;

        return view('admin.pharma.reports.purchase_register', $data);
    }

    public function aging(Request $request)
    {
        $data['nav'] = 'pharma_reports';
        $data['sub_nav'] = 'aging';
        $data['page_title'] = 'AR & AP Aging Summary';

        $data['arAging'] = ReportingService::getArAgingSummary(warehouseIds: PharmaAccess::warehouseIds());
        $data['apAging'] = ReportingService::getApAgingSummary(warehouseIds: PharmaAccess::warehouseIds());

        return view('admin.pharma.reports.aging', $data);
    }

    public function auditLogs(Request $request)
    {
        $data['nav'] = 'pharma_settings';
        $data['sub_nav'] = 'audit_logs';
        $data['page_title'] = 'Audit Trail & Event Logs';

        $data['logs'] = AuditLog::with(['user'])->orderBy('id', 'desc')->paginate(30);

        return view('admin.pharma.reports.audit_logs', $data);
    }

    public function settings(Request $request)
    {
        $data['nav'] = 'pharma_settings';
        $data['sub_nav'] = 'settings';
        $data['page_title'] = 'Pharma System Settings';

        $data['settings'] = Setting::orderBy('group', 'asc')->get()->groupBy('group');

        return view('admin.pharma.reports.settings', $data);
    }
}
