<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\SaleInvoice;
use App\Models\Warehouse;
use App\Services\ReportingService;

class DashboardController extends Controller
{
    public function index()
    {
        $data['nav'] = 'dashboard';
        $data['sub_nav'] = '';
        $data['page_title'] = 'Pharmaceutical Wholesale Dashboard';

        $data['summary'] = ReportingService::getDashboardSummary();
        $data['nearExpiryBatches'] = ReportingService::getNearExpiryBatches(90);
        $data['lowStockAlerts'] = ReportingService::getLowStockAlerts();
        $data['recentInvoices'] = SaleInvoice::with(['customer'])->orderBy('id', 'desc')->limit(5)->get();
        $data['recentReceipts'] = GoodsReceipt::with(['supplier'])->orderBy('id', 'desc')->limit(5)->get();
        $data['totalProducts'] = Product::count();
        $data['totalWarehouses'] = Warehouse::count();
        $data['stockFlow'] = \App\Services\InventoryService::getStockFlowSummary();

        return view('admin.dashboard.index', $data);
    }
}
